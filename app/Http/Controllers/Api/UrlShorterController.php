<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FindRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UrlRequest;

use App\Models\Browser;
use App\Models\Click;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Faker\Provider\Base;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use App\Http\Controllers\Api\AgentController;
use function GuzzleHttp\Promise\all;
use function PHPUnit\Framework\isEmpty;

class UrlShorterController extends BaseController
{

    public function clickTest()
    {

        $short = User::with(["shorturl" => function ($q) {
            $q->withCount("click");
        }])
            ->get();


        return $short;
    }


    public function test(Request $request)
    {

//        dd($request->header("browser"));
        $userAgent = "Mozilla/5.0 (X11; Linux x86_64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/103.0.0.0 Safari/537.36";
        $uid       = "08d220a29df946983a74dc988e9577bd";

        $click = Click::updateOrCreate([
            "uid"         => $uid,
            "shorturl_id" => 3,
            "platform_id" => AgentController::FindOrNewPlatform($userAgent),
            "browser_id"  => AgentController::FindOrNewBrowser($userAgent),
            "useragent"   => $userAgent
        ]);
        return $click;

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



            return $this->success(["user" => $user, "url" => $url], "user's shorturl data",201);

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
                "user_id"   => $user_id
            ];
        }
//        dd($data);

        $data = ShortUrl::insert($data);

        if ($data)
            return $this->success($data, "ok", 200);
        else
            return $this->error($data, 500);
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
                "browser_id"  => AgentController::getBrowser($request->header("user_agent")),
                "platform_id" => AgentController::getOs($request->header("user_agent")),
                "useragent" => $request->header("user_agent")
            ]
        ); // TODO add this to basecontroller
        $url = $url->url->url;
        return $this->success($url, "ok");
    }

    public function find(FindRequest $request): object
    {

        $user = Auth::user();

        $url    = Url::select("id")->where("url", $request->find)->first();
        $url_id = $url->id;
//        return $this->success($user->id,"ok");
        $short = ShortUrl::
        where([["url_id", "=", $url_id], ["user_id", "=", $user->id]])
            ->withCount("click")
            ->orderBy("click_count","desc")
            ->get();


        if (!empty($short))
            return $this->success($short, "Url data", 201);

        return $this->error("Not Found", 404);

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