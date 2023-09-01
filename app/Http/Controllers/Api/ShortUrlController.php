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


    public function all(): JsonResponse
    {
        $urls = $this->shortUrlRepository->findByUserId(\auth()->user()->id);

        if ($urls->isEmpty())
            return Response::error("user has no short urls", 404);
        return Response::success($urls->toArray(), "user's all short urls");
    }

    public function store(ShortUrlRequest $request): JsonResponse
    {
        $url = $request->url;
        $user_id = Auth::id();
        $amount = $request->amount;
        $name = $request->batch_name ?? null;

        $url_id = $this->urlRepository->findOrNew(compact("url", "user_id"))->getAttribute("id");

        Cache::tags("user_{$user_id}_urls")->flush();

        $batch = $this->batchRepository->create(compact("url_id", "user_id", "amount", "name"));

        if ($batch) {
            ShortUrlJob::dispatch($url_id, $amount, $user_id, $batch)->onQueue("short-urls");
            return Response::success(null, "your request to create $amount short urls for $url successfully Added to queue", 202);
        }
        return Response::error("there is a problem in server", 500);
    }

    public function show(int $id): JsonResponse
    {
        $shortUrl = $this->shortUrlRepository->findByIdWithLongUrlAndClicksAmount($id);

        Gate::authorize("shorturl-owner", $shortUrl);

        return Response::success($shortUrl, "short url's data with url and clicks amount");
    }

}