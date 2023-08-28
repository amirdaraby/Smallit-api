<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\ShortUrl\ShortUrlRequest;
use App\Http\Requests\Url\SearchRequest;
use App\Jobs\ShortUrlJob;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Repositories\BatchRepository;
use App\Repositories\ShortUrlRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;

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


    public function all(): object
    {
        $urls = $this->shortUrlRepository->findByUserId(\auth()->user()->id);

        if ($urls->isEmpty())
            return responseError("user has no short urls", 404);
        return responseSuccess($urls->toArray(), "user's all short urls");
    }

    public function store(ShortUrlRequest $request): object
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
            return responseSuccess(null, "your request to create $amount short urls for $url successfully Added to queue", 202);
        }
        return responseError("there is a problem in server", 500);
    }

    public function show(int $id){
        $shortUrl = $this->shortUrlRepository->findByIdWithLongUrlAndClicksAmount($id);

        Gate::authorize("shorturl-owner", $shortUrl);

        return responseSuccess($shortUrl, "short url's data with url and clicks amount");
    }

    public function urlStats(Url $id)
    {


        $shorturls = ShortUrl::where([["url_id", "=", $id->id], ["user_id", "=", Auth::id()]])
            ->withCount("clicks")
            ->orderBy("clicks_count", "desc")
            ->paginate(10);

        return
            empty($shorturls)
                ? $this->success(["url" => $id], "ok", 200)
                : $this->success(["url" => $id, "shorturl" => $shorturls], "ok", 200);

    }

    public function search(SearchRequest $request)
    {

        $user_id = Auth::user()->id;
        $url_id = Url::select("id")->where("url", "LIKE", "%$request->search%")->get();

        $data = ShortUrl::with("url")->where("user_id", $user_id)->whereIn("url_id", $url_id)->orderBy("created_at", "desc")->get();
        $count = count($data);

        if (count($data) == 0)
            return $this->error("Not found");
        return $this->success(["data" => $data, "count" => $count], "data found");
    }
}