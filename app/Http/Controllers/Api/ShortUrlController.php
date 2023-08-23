<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ShortUrl\ShortUrlRequest;
use App\Jobs\ShortUrlJob;
use App\Jobs\StoreClickJob;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Repositories\BatchRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ShortUrlController extends BaseController
{

    protected UrlRepository $urlRepository;
    protected BatchRepository $batchRepository;

    public function __construct(UrlRepository $urlRepository, BatchRepository $batchRepository)
    {
        $this->urlRepository = $urlRepository;
        $this->batchRepository = $batchRepository;
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
            ShortUrlJob::dispatch($url_id, $amount, $user_id, $batch);
            return responseSuccess(null, "your request to create $amount short urls for $url successfully Added to queue", 202);
        }
        return responseError("there is a problem in server", 500);
    }


    public function show(ShortUrl $url, Request $request): object
    {

        StoreClickJob::dispatch($request->header("user-agent"), $request->header("uid"), $url->id);

        $url = $url->url->url;
        return $this->success($url, "ok");
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


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(ShortUrl $url)
    {
        return $url;
    }
}