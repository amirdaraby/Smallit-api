<?php

namespace App\Http\Controllers\Api\Clicks;

use App\Http\Controllers\Api\BaseController;
use App\Repositories\ClickRepository;
use App\Repositories\ShortUrlRepository;
use App\Utils\Response;


class UniqueClickController extends BaseController
{
    protected ClickRepository $clickRepository;
    protected ShortUrlRepository $shortUrlRepository;

    public function __construct(ClickRepository $clickRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->clickRepository = $clickRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function all(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner",$shortUrl);

        $clicks = $this->clickRepository->findAllUniqueClicksByShortUrl($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no unique click", 404);
        return Response::success($clicks, "short url's unique clicks ordered by last click", 200);
    }

    public function browsers(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findUniqueBrowsersInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no unique click", 404);

        return Response::success($clicks, "short url's unique click by browser, ordered by last click", 200);
    }

    public function platforms(int $shortUrlId)
    {
        $shortUrl = $this->shortUrlRepository->findById($shortUrlId);
        $this->authorize("shorturl-owner", $shortUrl);

        $clicks = $this->clickRepository->findUniquePlatformsInsightByShortUrlId($shortUrl->id);

        if ($clicks->isEmpty())
            return Response::error("this short url has no unique click", 404);

        return Response::success($clicks, "short url's unique click by browser, ordered by last click", 200);
    }
}
