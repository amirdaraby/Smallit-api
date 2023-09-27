<?php

namespace App\Http\Controllers\Api\Clicks;

use App\Http\Controllers\Api\BaseController;
use App\Http\Resources\ShortUrl;
use App\Jobs\StoreClickJob;
use App\Repositories\ClickRepository;
use App\Repositories\ShortUrlRepository;
use App\Utils\Response;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use function PHPUnit\Framework\isEmpty;

class ClickController extends BaseController
{
    protected ClickRepository $clickRepository;
    protected ShortUrlRepository $shortUrlRepository;

    public function __construct(ClickRepository $clickRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->clickRepository = $clickRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    /**
     * @OA\Get(
     *     tags={"clicks", "short urls"},
     *     path="/api/v1/click/{shortUrl}",
     *     summary="Click",
     *     description="dispatches a click and returns short url and url",
     *     operationId="clickShortUrl",
     *
     *     @OA\PathParameter(
     *     name="short_url",
     *     description="short url to click and get url",
     *     required=true,
     *     example="255t",
     *     ),
     *
     *     @OA\Parameter(
     *     name="uid",
     *     description="this parameter is optional but in case of use, can count Unique clicks, read FingerprintJS for an example",
     *     required=false,
     *     in="header",
     *     example="Q1ddv9mE2JcxA1",
     *     ),
     *
     *     @OA\Parameter(
     *     name="user-agent",
     *     description="make sure you send USER's user agent to server, otherwise no click will be count",
     *     required=false,
     *     in="header",
     *     example="Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/114.0.0.0 Safari/537.36",
     *     ),
     *
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *
     *     @OA\Response(response="200", description="returns short url and url in response and counts a new click in background",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="message"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="short_url", type="string", example="255t"),
     *     @OA\Property(property="long_url", type="string", example="https://example.com")
     *     ),
     *     @OA\Property(property="message", type="string", example="short url clicked"),
     *     ),
     *     ),
     * )
     */
    public function click(string $shortUrl, Request $request): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findByShortUrlWithLongUrlOrFail($shortUrl);

        if ($request->header("user-agent"))
            StoreClickJob::dispatch($request->header("user-agent"), $request->header("uid"), $shortUrl)->onQueue("clicks");

        return Response::success(ShortUrl::make($shortUrl), "short url clicked", 200);
    }

    /**
     * @OA\Get(
     *     tags={"clicks", "unique clicks"},
     *     path="/api/v1/user/clicks/{id}",
     *     summary="Show click",
     *     description="get click's data by id",
     *     operationId="showClick",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of click",
     *     required=true,
     *     example="1",
     *     ),
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *
     *     @OA\Response(response="200", description="shows requested click's data",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="short_url_id", type="int", example="1"),
     *     @OA\Property(property="platform", type="string", example="Linux"),
     *     @OA\Property(property="browser", type="string", example="Chrome"),
     *     @OA\Property(property="created_at", type="string", example="2023-08-25 15:15:46")
     *     ),
     *     @OA\Property(property="message", type="string", example="click's data")
     *     ),
     *     ),
     * )
     */
    public function show(int $id): JsonResponse
    {
        $click = $this->clickRepository->findById($id, ["id", "short_url_id", "platform", "browser", "created_at"]);
        $shortUrl = $this->shortUrlRepository->findById($click->short_url_id);

        $this->authorize("shorturl-owner", $shortUrl);

        return Response::success($click, "click's data", 200);
    }

    /**
     * @OA\Get(
     *     tags={"clicks", "unique clicks", "short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks",
     *     summary="Short url's clicks index",
     *     description="get total and unique clicks amount of short url",
     *     operationId="indexClick",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="shortUrlId",
     *     description="id of short url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *
     *     @OA\Response(response="200", description="shows total and unique clicks of short url",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="total_clicks", type="int", example="1000"),
     *     @OA\Property(property="unique_clicks", type="int", example="500"),
     *     @OA\Property(property="last_click_time", type="string", example="2023-08-25 15:15:46")
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's basic clicks data")
     *     ),
     *     )
     * )
     */
    public function index(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);
        $clicks = $this->clickRepository->findBasicInsightsByShortUrlId($shortUrl->id);

        return Response::success($clicks, "short url's basic clicks data", 200);
    }

    /**
     * @OA\Get(
     *     tags={"clicks","short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/all",
     *     summary="All short url's clicks",
     *     description="returns all clicks of short url as paginated response",
     *     operationId="allClicks",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *      name="page",
     *      description="specifies page of pagination",
     *      in="query",
     *      required=false,
     *      example="1"
     *      ),
     *
     *     @OA\PathParameter(
     *     name="shortUrlId",
     *     description="id of short url",
     *     required=true,
     *     example="1"
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound", description="user doesn't have enough resource to show in this page"),
     *
     *     @OA\Response(
     *     response="200",
     *     description="responses short url's all clicks",
     *      @OA\JsonContent(
     *      @OA\Property(property="status", type="string", example="success"),
     *      @OA\Property(property="data", type="object",
     *      @OA\Property(property="current_page", type="int", example=1),
     *      @OA\Property(property="data", type="array",
     *      @OA\Items(type="object",
     *      @OA\Property(property="id", type="int", example="1"),
     *      @OA\Property(property="short_url_id", type="int", example="1"),
     *      @OA\Property(property="platform", type="string", example="Linux"),
     *      @OA\Property(property="browser", type="string", example="Chrome"),
     *      ),
     *      ),
     *      @OA\Property(property="first_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all?page=1"),
     *      @OA\Property(property="from", type="int", example="1"),
     *      @OA\Property(property="last_page", type="int", example="1"),
     *      @OA\Property(property="last_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all?page=1"),
     *
     *      @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *      @OA\Items(type="object",
     *      @OA\Property(property="url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all?page=2"),
     *      @OA\Property(property="label", type="string", example="&laquo; Next"),
     *      @OA\Property(property="active", type="boolean", example="true"),
     *      ),
     *      ),
     *      @OA\Property(property="next_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all?page=2"),
     *      @OA\Property(property="path", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all"),
     *      @OA\Property(property="per_page", type="int", example="10"),
     *      @OA\Property(property="prev_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/all?page=0"),
     *      @OA\Property(property="to", type="int", example="10"),
     *      @OA\Property(property="total", type="int", example="20"),
     *      ),
     *      @OA\Property(property="message", type="string", example="short url's all clicks"),
     *      ),
     *     ),
     * )
     */
    public function all(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findByShortUrlId($shortUrl->id, ["id", "short_url_id", "platform","browser", "created_at"]);

        if ($clicks->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($clicks, "short url's all clicks");
    }


    /**
     * @OA\Get(
     *     tags={"clicks", "short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/browsers",
     *     summary="Clicks browsers",
     *     description="get short url's amount of clicks by different browsers",
     *     operationId="browsersClicks",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="shortUrlId",
     *     description="id of short url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *
     *     @OA\Response(response="200", description="returns all browsers clicked the short url data as an array of objects which is ordered by most clicks",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="array", collectionFormat="multi",
     *     @OA\Items(type="object", @OA\Property(property="browser", type="string", example="Chrome"), @OA\Property(property="total_clicks", type="int", example="100"))
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's clicks by browser, ordered by most clicks")
     *     ),
     *     )
     * )
     */
    public function browsers(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findTotalBrowsersInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no click", 404);

        return Response::success($clicks->toArray(), "short url's clicks by browser, ordered by most clicks", 200);
    }

    /**
     * @OA\Get(
     *     tags={"clicks", "short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/platforms",
     *     summary="Clicks platforms",
     *     description="get short url's amount of clicks by different platforms",
     *     operationId="platformsClicks",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="shortUrlId",
     *     description="id of short url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *
     *     @OA\Response(response="200", description="returns all platforms clicked the short url data as an array of objects which ordered by most clicks",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="array", collectionFormat="multi",
     *     @OA\Items(type="object", @OA\Property(property="platform", type="string", example="Linux"), @OA\Property(property="total_clicks", type="int", example="100"))
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's clicks by platform, ordered by most clicks")
     *     ),
     *     )
     * )
     */
    public function platforms(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findTotalPlatformsInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no click", 404);

        return Response::success($clicks->toArray(), "short url's clicks by platform, ordered by most clicks", 200);
    }

}
