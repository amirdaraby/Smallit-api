<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ShortUrl;
use App\Jobs\StoreClickJob;
use App\Repositories\ClickRepository;
use App\Repositories\ShortUrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Utils\Response;

class ClickController extends BaseController
{
    protected ShortUrlRepository $shortUrlRepository;
    protected ClickRepository $clickRepository;

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
}
