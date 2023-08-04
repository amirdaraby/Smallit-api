<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\ShortUrl\ShortUrlRequest;
use App\Jobs\ShortUrlJob;
use App\Jobs\StoreClickJob;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\UserJobs;
use App\Repositories\BatchRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends BaseController
{

    protected UrlRepository $urlRepository;
    protected BatchRepository $batchRepository;
    public function __construct(UrlRepository $urlRepository, BatchRepository $batchRepository)
    {
        $this->urlRepository = $urlRepository;
        $this->batchRepository = $batchRepository;
    }

    public function index(Request $request): object
    {
        $user = Auth::user();

        $url = ShortUrl::with(["url" => function ($q) use ($user) {
            $q->select("id", "url")->withCount(["shorturl" => function ($q) use ($user) {
                $q->where("user_id", $user->id);
            }]);
        }])->select("url_id")
            ->where("user_id", $user->id)->groupBy('url_id')
            ->paginate(10);
        if ($url->isEmpty())
            return $this->error("this user does not have short urls", 404, null);
        return $this->success(["user" => $user, "url" => $url], "user's shorturl data");

    }


    /**
     * @param ShortUrlRequest $request
     * @return string
     */
    public function store(ShortUrlRequest $request)
    {
        $url = $request->url;
        $user_id = Auth::id();
        $amount = $request->amount;
        $name = $request->batch_name ?? null;

        $url_id = $this->urlRepository->findOrNew(compact("url", "user_id"))->getAttribute("id");
        $batch = $this->batchRepository->create(compact("url_id", "user_id", "amount", "name"));

        if ($batch){
            $job = ShortUrlJob::dispatch($url_id, $amount, $user_id, $batch);
            if ($job)
                return responseSuccess(null, "your request to create $amount short urls for $url successfully Added to queue",202);
        }
        return responseError("there is a problem in serve.", 500);
    }

    /**
     * @param ShortUrl $url
     * @return \Illuminate\Http\JsonResponse
     */
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


    /**
     * Update the specified resource in storage.
     *
     * @param \Illuminate\Http\Request $request
     * @param int $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }


    public function destroy(ShortUrl $url)
    {
        return $url;
    }
}