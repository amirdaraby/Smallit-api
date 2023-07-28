<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\SearchRequest;
use App\Http\Requests\UrlRequest;
use App\Jobs\ShortUrlJob;
use App\Jobs\StoreClickJob;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\UserJobs;
use App\Traits\UserAgent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ShortUrlController extends BaseController
{

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
     * @param UrlRequest $request
     * @return string
     */
    public function store(UrlRequest $request)
    {
        $url = $request->url;
        $url_id = $this->FindOrNewUrl($url);
        $user_id = Auth::user()->id;
        $count = $request->count;

        $job = UserJobs::create([
            'user_id' => $user_id,
            'url_id' => $url_id,
            'count' => $count,
            'status' => 'queue'
        ]);


        $job = ShortUrlJob::dispatch($url_id, $count, $user_id, $job);

        if ($job)
            return $this->success($job, "your request to create : $request->count short urls for url : $request->url submitted", 201);
        else
            return $this->error("failed", 500);
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