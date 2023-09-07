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

    public function click(string $shortUrl, Request $request): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findByShortUrlWithLongUrlOrFail($shortUrl);

        if ($request->header("user-agent"))
            StoreClickJob::dispatch($request->header("user-agent"), $request->header("uid"), $shortUrl)->onQueue("clicks");

        return Response::success(ShortUrl::make($shortUrl), "short url clicked", 200);
    }

    public function show(int $id): JsonResponse
    {
        $click = $this->clickRepository->findById($id);
        $shortUrl = $this->shortUrlRepository->findById($click->short_url_id);

        $this->authorize("shorturl-owner", $shortUrl);

        return Response::success($click, "click's data", 200);
    }

    public function index(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);
        $clicks = $this->clickRepository->findBasicInsightsByShortUrlId($shortUrl->id);

        return Response::success($clicks, "short url's basic clicks data", 200);
    }

    public function all(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("This short url has no click", 404);

        return Response::success($clicks, "short url's all clicks");
    }


    public function browsers(int $shortUrlId): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findTotalBrowsersInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no click", 404);

        return Response::success($clicks->toArray(), "short url's clicks by browser, ordered by most clicks", 200);
    }

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
