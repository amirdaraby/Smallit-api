<?php

namespace App\Http\Controllers\Api\Clicks;

use App\Http\Controllers\Api\BaseController;
use App\Repositories\ClickRepository;
use App\Repositories\ShortUrlRepository;
use App\Utils\Response;
use OpenApi\Annotations\OpenApi as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;


class UniqueClickController extends BaseController
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
     *     tags={"unique clicks","short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/unique/all",
     *     summary="All short url's unique clicks",
     *     description="returns all unique clicks of short url as paginated response",
     *     operationId="allUniqueClicks",
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
     *     description="responses short url's all unique clicks",
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
     *      @OA\Property(property="first_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all?page=1"),
     *      @OA\Property(property="from", type="int", example="1"),
     *      @OA\Property(property="last_page", type="int", example="1"),
     *      @OA\Property(property="last_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all?page=1"),
     *
     *      @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *      @OA\Items(type="object",
     *      @OA\Property(property="url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all?page=2"),
     *      @OA\Property(property="label", type="string", example="&laquo; Next"),
     *      @OA\Property(property="active", type="boolean", example="true"),
     *      ),
     *      ),
     *      @OA\Property(property="next_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all?page=2"),
     *      @OA\Property(property="path", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all"),
     *      @OA\Property(property="per_page", type="int", example="10"),
     *      @OA\Property(property="prev_page_url", type="string", example="https://example.com/api/v1/user/short-urls/1/clicks/unique/all?page=0"),
     *      @OA\Property(property="to", type="int", example="10"),
     *      @OA\Property(property="total", type="int", example="20"),
     *      ),
     *      @OA\Property(property="message", type="string", example="short url's all clicks"),
     *      ),
     *     ),
     * )
     */
    public function all(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner",$shortUrl);

        $clicks = $this->clickRepository->findAllUniqueClicksByShortUrl($shortUrl->id, ["id", "short_url_id", "platform", "browser", "created_at"]);

        if ($clicks->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($clicks, "short url's unique clicks ordered by last click", 200);
    }

    /**
     * @OA\Get(
     *     tags={"unique clicks", "short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/unique/browsers",
     *     summary="Unique clicks browsers",
     *     description="get short url's amount of unique clicks by different browsers",
     *     operationId="uniqueBrowsersClicks",
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
     *     @OA\Response(response="200", description="returns all unique clicks of short url data by browser as an array of objects which is ordered by most clicks",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="array", collectionFormat="multi",
     *     @OA\Items(type="object", @OA\Property(property="browser", type="string", example="Chrome"), @OA\Property(property="unique_clicks", type="int", example="100"))
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's unique clicks by browser, ordered by most clicks")
     *     ),
     *     )
     * )
     */
    public function browsers(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findUniqueBrowsersInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($clicks, "short url's unique clicks by browser, ordered by last click", 200);
    }
    /**
     * @OA\Get(
     *     tags={"unique clicks", "short urls"},
     *     path="/api/v1/user/short-urls/{shortUrlId}/clicks/unique/platforms",
     *     summary="Unique clicks platforms",
     *     description="get short url's amount of unique clicks by different platforms",
     *     operationId="uniquePlatformsClicks",
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
     *     @OA\Response(response="200", description="returns all unique clicks of short urls data by platforms as an array of objects which ordered by most clicks",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="array", collectionFormat="multi",
     *     @OA\Items(type="object", @OA\Property(property="platform", type="string", example="Linux"), @OA\Property(property="unique_clicks", type="int", example="100"))
     *     ),
     *     @OA\Property(property="message", type="string", example="short url's unique clicks by platform, ordered by most clicks")
     *     ),
     *     )
     * )
     */
    public function platforms(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findUniquePlatformsInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            throw new NotFoundHttpException();

        return Response::success($clicks, "short url's unique clicks by platform, ordered by last click", 200);
    }
}
