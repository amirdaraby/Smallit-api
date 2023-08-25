<?php

namespace App\Http\Controllers\Api;

use App\Http\Resources\ShortUrl;
use App\Jobs\StoreClickJob;
use App\Repositories\ClickRepository;
use App\Repositories\ShortUrlRepository;
use Illuminate\Http\Request;

class ClickController extends BaseController
{
    protected ShortUrlRepository $shortUrlRepository;
    protected ClickRepository $clickRepository;

    public function __construct(ClickRepository $clickRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->clickRepository = $clickRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function click(string $shortUrl, Request $request): object
    {
        $shortUrl = $this->shortUrlRepository->findByShortUrlWithLongUrlOrFail($shortUrl);

        if ($request->header("user-agent"))
        StoreClickJob::dispatch($request->header("user-agent"), $request->header("uid"), $shortUrl);

        return responseSuccess(ShortUrl::make($shortUrl), "short url clicked", 200);
    }
}
