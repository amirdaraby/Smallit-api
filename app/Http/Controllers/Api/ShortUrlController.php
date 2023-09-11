<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShortUrl\ShortUrlRequest;
use App\Jobs\ShortUrlJob;
use App\Repositories\BatchRepository;
use App\Repositories\ShortUrlRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Utils\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class ShortUrlController extends BaseController
{

    protected UrlRepository $urlRepository;
    protected BatchRepository $batchRepository;
    protected ShortUrlRepository $shortUrlRepository;

    public function __construct(UrlRepository $urlRepository, BatchRepository $batchRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->urlRepository = $urlRepository;
        $this->batchRepository = $batchRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }


    /**
     * @OA\Get(
     *     tags={"short urls"},
     *     path="/api/v1/user/short-urls/all",
     *     summary="All short urls",
     *     description="returns all short urls created by user as paginated response",
     *     operationId="allShortUrls",
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
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound", description="user doesn't have enough resource to show in this page"),
     *
     *     @OA\Response(
     *     response="200",
     *     description="user's all short urls",
     *      @OA\JsonContent(
     *      @OA\Property(property="status", type="string", example="success"),
     *      @OA\Property(property="data", type="object",
     *      @OA\Property(property="current_page", type="int", example=1),
     *      @OA\Property(property="data", type="array",
     *      @OA\Items(type="object",
     *      @OA\Property(property="id", type="int", example="1"),
     *      @OA\Property(property="url_id", type="int", example="1"),
     *      @OA\Property(property="short_url", type="string", example="4h7gw"),
     *      @OA\Property(property="long_url", type="string", example="https://example.com"),
     *      @OA\Property(property="total_clicks", type="int", example="100"),
     *      ),
     *      ),
     *      @OA\Property(property="first_page_url", type="string", example="https://example.com/api/v1/user/short-urls/all?page=1"),
     *      @OA\Property(property="from", type="int", example="1"),
     *      @OA\Property(property="last_page", type="int", example="1"),
     *      @OA\Property(property="last_page_url", type="string", example="https://example.com/api/v1/user/short-urls/all?page=1"),
     *
     *      @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *      @OA\Items(type="object",
     *      @OA\Property(property="url", type="string", example="https://example.com/api/v1/user/short-urls/all?page=2"),
     *      @OA\Property(property="label", type="string", example="&laquo; Next"),
     *      @OA\Property(property="active", type="boolean", example="true"),
     *      ),
     *      ),
     *      @OA\Property(property="next_page_url", type="string", example="https://example.com/api/v1/user/short-urls/all?page=2"),
     *      @OA\Property(property="path", type="string", example="https://example.com/api/v1/user/short-urls/all"),
     *      @OA\Property(property="per_page", type="int", example="15"),
     *      @OA\Property(property="prev_page_url", type="string", example="https://example.com/api/v1/user/short-urls/all?page=0"),
     *      @OA\Property(property="to", type="int", example="15"),
     *      @OA\Property(property="total", type="int", example="20"),
     *      ),
     *      @OA\Property(property="message", type="string", example="all batches created by user"),
     *      ),
     *     ),
     * )
     */
    public function all(): JsonResponse
    {
        $urls = $this->shortUrlRepository->findByUserId(\auth()->user()->id);

        if ($urls->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($urls->toArray(), "user's all short urls");
    }

    /**
     * @OA\Post(
     *     tags={"short urls", "batches"},
     *     path="/api/v1/user/short-urls/batch",
     *     summary="Create short-urls",
     *     description="Creates a batch for creating an amount of short-urls for requested url",
     *     operationId="createShortUrl",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\RequestBody(
     *     required=true,
     *     description="url and amount of short urls",
     *     @OA\JsonContent(
     *     required={"url","amount"},
     *     @OA\Property(property="url", type="string",
     *     example="https://example.com/"),
     *     @OA\Property(property="amount", type="int", example="100", minimum=1, maximum=100000),
     *      ),
     *  ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *
     *     @OA\Response(
     *     response="202",
     *     description="batch created successfully and added to queue",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", example=null),
     *     @OA\Property(property="message", type="string", example="your request to create 100 short urls for https://someUrl.com Added to queue")
     *     ),
     *     ),
     *
     *    @OA\Response(
     *    response="422",
     *    description="Validation Error",
     *    @OA\JsonContent(
     *    @OA\Property(property="status", type="string", example="error"),
     *
     *    @OA\Property(property="data", type="object", description="returns validation errors list",
     *    @OA\Property(property="url", type="array", @OA\Items(type="string", example={"The url must be a valid URL."})),
     *    @OA\Property(property="amount", type="array", @OA\Items(type="string", format="email", example={"The amount must be between 1 and 100000."})),
     *      ),
     *      @OA\Property(property="message", type="string", example="Validation failed")
     *      ),
     *    ),
     * )
     *
     */
    public function store(ShortUrlRequest $request): JsonResponse
    {
        $url = $request->url;
        $user_id = Auth::id();
        $amount = $request->amount;
        $name = $request->batch_name ?? null;

        $url_id = $this->urlRepository->findOrNew(compact("url", "user_id"))->getAttribute("id");

        Cache::tags("user_{$user_id}_urls")->flush();

        $batch = $this->batchRepository->create(compact("url_id", "user_id", "amount", "name"));

        ShortUrlJob::dispatch($url_id, $amount, $user_id, $batch)->onQueue("short-urls");

        return Response::success(null, "your request to create $amount short urls for $url Added to queue", 202);
    }

    /**
     * @OA\Get(
     *     tags={"short urls"},
     *     path="/api/v1/user/short-urls/{id}",
     *     summary="Show short url",
     *     description="get short url's data with long url and total clicks",
     *     operationId="showShortUrl",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of short url",
     *     required=true,
     *     example="1",
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *     @OA\Response(response="200", description="shows requested short url with long url and total clicks",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="short_url", type="string", example="41dsgs"),
     *     @OA\Property(property="user_id", type="int", example="1"),
     *     @OA\Property(property="url_id", type="int", example="1"),
     *     @OA\Property(property="batch_id", type="int", example="1"),
     *     @OA\Property(property="total_clicks", type="int", example="1000"),
     *     @OA\Property(property="url", type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="url", type="string", example="https://example.com"),
     *     @OA\Property(property="created_at", type="string", example="2023-09-10 21:08:15")
     *     ),
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's data with url and clicks amount"),
     *     ),
     *     ),
     * )
     *
     */
    public function show(int $id): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findByIdWithLongUrlAndClicksAmount($id);

        Gate::authorize("shorturl-owner", $shortUrl);

        return Response::success($shortUrl, "short url's data with url and clicks amount");
    }

}