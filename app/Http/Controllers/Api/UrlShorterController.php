<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FindRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UrlRequest;
use App\Models\Click;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Auth\Access\Gate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\AgentController;
use function PHPUnit\Framework\isEmpty;

class UrlShorterController extends BaseController
{

    public function clickTest()
    {

        $short = User::with(["shorturl" => function ($q) {
            $q->withCount("clicks");
        }])
            ->get();


        return $short;
    }

    public function index(): object
    {


//        $url = ShortUrl::select("url_id")->where("user_id",$user->id);

        $user = Auth::user();

        $url = ShortUrl::with(["url" => function ($q) use ($user) {
            $q->select("id", "url")->withCount(["shorturl" => function ($q) use ($user) {
                $q->where("user_id", $user->id);
            }]);
        }])->select("url_id")
            ->where("user_id", $user->id)->groupBy('url_id')
            ->get();


        return $this->success(["user" => $user, "url" => $url], "user's shorturl data", 201);

    }


    /**
     * @param UrlRequest $request
     * @return string
     */
    public function store(UrlRequest $request): object
    {
        $url = $request->url;

        if ($this->regexUrl($url))
            $url = $url . "/";

        global $data;
        $short_url_id = $this->FindOrNewUrl($url);
        $user_id      = Auth::user()->id;


        for ($i = 0; $i < $request->count; $i++) {
            $data [$i] = [
                "short_url" => Str::random(5),
                "url_id"    => $short_url_id,
                "user_id"   => $user_id,
                "created_at" => now()->format("Y/m/d H:i:s"),
                "updated_at" => now()->format("Y/m/d H:i:s")
            ];
        }


        $data = ShortUrl::insert($data);

        if ($data)
            return $this->success($data, "$request->count short urls created for $request->url", 201);
        else
            return $this->error("no", 500);
//            return $this->error("create failed", 500);


    }

    /**
     * @param ShortUrl $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShortUrl $url, Request $request): object
    {


        Click::updateOrCreate(
            [
                "uid"         => $request->header("uid"),
                "shorturl_id" => $url->id,
                "browser"     => AgentController::getBrowser($request->header("user_agent")),
                "platform"    => AgentController::getOs($request->header("user_agent")),
                "useragent"   => $request->header("user_agent")
            ]
        ); // TODO add this to basecontroller
        $url = $url->url->url;
        return $this->success($url, "ok");
    }

    public function urlStats(Url $id): object
    {

        $shorturls = ShortUrl::where([["url_id", "=", $id->id], ["user_id", "=", Auth::id()]])
            ->withCount("clicks")
            ->orderBy("clicks_count", "desc")
            ->get()
            ->toArray();

        return
            empty($shorturls)
                ? $this->success(["url" => $id], "ok", 200)
                : $this->success(["url" => $id, "shorturl" => $shorturls], "ok", 200);

    }

    public function search(SearchRequest $request)
    {

        $user_id = Auth::user()->id;
        $url_id  = Url::select("id")->where("url", "LIKE", "%$request->search%")->get();

        $data  = ShortUrl::with("url")->where("user_id", $user_id)->whereIn("url_id", $url_id)->orderBy("created_at", "desc")->get();
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