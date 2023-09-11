<?php

namespace App\Http\Controllers\Api;

use App\Repositories\ShortUrlRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Utils\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UrlController extends BaseController
{
    protected UrlRepository $urlRepository;
    protected ShortUrlRepository $shortUrlRepository;

    public function __construct(UrlRepository $urlRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->urlRepository = $urlRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    /**
     * @OA\Get(
     *     tags={"urls"},
     *     path="/api/v1/user/urls/all",
     *     summary="All urls",
     *     description="returns all urls created by user with short urls amount as paginated response",
     *     operationId="allUrls",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *     name="page",
     *     description="specifies page of pagination",
     *     in="query",
     *     required=false,
     *     example="1"
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound", description="user doesn't have enough urls to show in this page"),
     *
     *     @OA\Response(
     *      response="200",
     *      description="user's all urls with count of short urls",
     *       @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="data", type="object",
     *       @OA\Property(property="current_page", type="int", example=1),
     *       @OA\Property(property="data", type="array",
     *       @OA\Items(type="object",
     *       @OA\Property(property="id", type="int", example="1"),
     *       @OA\Property(property="url", type="string", example="https://example.com"),
     *       @OA\Property(property="short_url_amount", type="int", example="400"),
     *       @OA\Property(property="created_at", type="string", example="2023-09-10 21:08:15"),
     *       ),
     *       ),
     *       @OA\Property(property="first_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/all?page=1"),
     *       @OA\Property(property="from", type="int", example="1"),
     *       @OA\Property(property="last_page", type="int", example="1"),
     *       @OA\Property(property="last_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/all?page=1"),
     *
     *       @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *       @OA\Items(type="object",
     *       @OA\Property(property="url", type="string", example="http://localhost:8001/api/v1/user/urls/all?page=2"),
     *       @OA\Property(property="label", type="string", example="&laquo; Next"),
     *       @OA\Property(property="active", type="boolean", example="true"),
     *       ),
     *       ),
     *       @OA\Property(property="next_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/all?page=2"),
     *       @OA\Property(property="path", type="string", example="http://localhost:8001/api/v1/user/urls/all"),
     *       @OA\Property(property="per_page", type="int", example="10"),
     *       @OA\Property(property="prev_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/all?page=0"),
     *       @OA\Property(property="to", type="int", example="10"),
     *       @OA\Property(property="total", type="int", example="20"),
     *       ),
     *       @OA\Property(property="message", type="string", example="all urls"),
     *       ),
     *      ),
     *
     * )
     *
     */
    public function all(Request $request): JsonResponse
    {

        $page = $request->get("page") ?? 1;
        $user_id = Auth::id();

        $urls = Cache::tags("user_{$user_id}_urls")->remember("user_urls_{$user_id}_{$page}", 60 * 30, function () use ($user_id) {
            return $this->urlRepository->findByUserIdWithShortUrlAmount($user_id);
        });

        if ($urls->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($urls, "all urls");
    }

    /**
     * @OA\Get(
     *     tags={"urls"},
     *     path="/api/v1/user/urls/{id}",
     *     summary="Show url",
     *     description="get url's data",
     *     operationId="showUrl",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *     @OA\Response(response="200", description="shows requested url",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="url", type="string", example="https://example.com"),
     *     @OA\Property(property="user_id", type="int", example="1"),
     *     @OA\Property(property="created_at", type="string", example="2023-09-10 21:08:15")
     *     ),
     *     ),
     *     @OA\Property(property="message", type="string", example="url"),
     *     ),
     *     ),
     * )
     *
     */
    public function show(int $id): JsonResponse
    {
        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        return Response::success($url->toArray(), "url", 200);
    }

    /**
     * @OA\Delete(
     *     tags={"urls"},
     *     path="/api/v1/user/urls/{id}",
     *     summary="Delete url",
     *     description="deletes url and all short urls of url",
     *     operationId="deleteUrl",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/NotFound"),
     *     @OA\Response(response="404", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="202",
     *     description="deletes url and short urls of url",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="boolean", example="true"),
     *     @OA\Property(property="message", type="string", example="url and url's short-urls deleted")
     *     ),
     *     ),
     * )
     *
     */
    public function delete(int $id): JsonResponse
    {
        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        $deleted = $this->urlRepository->delete($url->id);

        return Response::success($deleted, "url and url's short-urls deleted", 202);
    }

    /**
     * @OA\Get(
     *     tags={"urls", "short urls"},
     *     path="/api/v1/user/urls/{id}/short-urls",
     *     description="returns all short-urls created for this url with total clicks count as paginated response",
     *     operationId="showUrlShortUrls",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of url",
     *     required=true,
     *     example="1"
     *     ),
     *
     *     @OA\Parameter(
     *     name="page",
     *     description="specifies page of pagination",
     *     in="query",
     *     required=false,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound", description="user doesn't have enough resource to show in this page"),
     *
     *     @OA\Response(response="200", description="all short urls for requested url",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="current_page", type="int", example=1),
     *     @OA\Property(property="data", type="array",
     *     @OA\Items(type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="url_id", type="int", example="1"),
     *     @OA\Property(property="short_url", type="string", example="4h7gw"),
     *     @OA\Property(property="total_clicks", type="int", example="100"),
     *     ),
     *     ),
     *     @OA\Property(property="first_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls?page=1"),
     *     @OA\Property(property="from", type="int", example="1"),
     *     @OA\Property(property="last_page", type="int", example="1"),
     *     @OA\Property(property="last_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls?page=1"),
     *
     *     @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *     @OA\Items(type="object",
     *     @OA\Property(property="url", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls?page=2"),
     *     @OA\Property(property="label", type="string", example="&laquo; Next"),
     *     @OA\Property(property="active", type="boolean", example="true"),
     *     ),
     *     ),
     *     @OA\Property(property="next_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls?page=2"),
     *     @OA\Property(property="path", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls"),
     *     @OA\Property(property="per_page", type="int", example="15"),
     *     @OA\Property(property="prev_page_url", type="string", example="http://localhost:8001/api/v1/user/urls/1/short-urls?page=0"),
     *     @OA\Property(property="to", type="int", example="15"),
     *     @OA\Property(property="total", type="int", example="20"),
     *     ),
     *     @OA\Property(property="message", type="string", example="all batches created by user"),
     *
     *    ),
     *    ),
     * )
     */
    public function showShortUrls(int $id): JsonResponse
    {

        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        $shortUrls = $this->shortUrlRepository->findByUrlId($url->id);

        if ($shortUrls->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($shortUrls, "all short urls of url: $url->url", 200);
    }

    /**
     * @OA\Get(
     *     tags={"urls"},
     *     path="/api/v1/user/urls/search",
     *     summary="Search urls",
     *     description="search urls and get up to 10 results",
     *     operationId="searchUrl",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Parameter(
     *     name="q",
     *     description="search query",
     *     required=true,
     *     in="query",
     *     example="example",
     *     ),
     *
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *     @OA\Response(response="401", ref="#components/responses/Unauthorized"),
     *
     *     @OA\Response(
     *     response="200", description="returns id and url of the query results",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="array",
     *     @OA\Items(type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="url", type="string", example="https://example.com")))
     *     ),
     *     @OA\Property(property="message", type="string", example="search results")
     *     ),
     * )
     *
     */
    public function search(Request $request): JsonResponse
    {

        if (! $request->get("q"))
            throw new NotFoundHttpException();

        $urls = $this->urlRepository->searchByUrl($request->get("q"), \auth()->user()->id);

        if ($urls->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($urls->toArray(), "search results");
    }
}
