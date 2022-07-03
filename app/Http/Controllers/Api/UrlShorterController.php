<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\FindRequest;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UrlRequest;

use App\Models\ShortUrl;
use App\Models\Url;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use function PHPUnit\Framework\isEmpty;

class UrlShorterController extends BaseController
{

    public function test(Request $request){

       return $this->regexUrl($request->url);

    }


    public function index()
    {

        $user = Auth::user();
//        $url = ShortUrl::select("url_id")->where("user_id",$user->id);

        $url = ShortUrl::with("url")->select("url_id", DB::raw('count(*) as count'))
            ->where("user_id", $user->id)->groupBy('url_id')
            ->orderBy("COUNT", "desc")->get();


        return $this->success(["user" => $user, "url" => $url], "user's shorturl data");
    }


    /**
     * @param UrlRequest $request
     * @return string
     */
    public function store(UrlRequest $request)
    {
        $url = $request->url;

        if ($this->regexUrl($url))
            $url = $url."/";

        global $data;
        $short_url_id = $this->FindOrNewUrl($url);
        $user_id = Auth::user()->id;


        for ($i = 0; $i < $request->count; $i++) {
            $data [$i] = [
                "short_url" => Str::random(5),
                "url_id" => $short_url_id,
                "user_id" => $user_id
            ];
        }
//        dd($data);

        $data = ShortUrl::insert($data);


        if ($data == 1)
            return $this->success($data, "ok", 200);
        else
            return $this->error($data, 500);
//            return $this->error("create failed", 500);


    }

    /**
     * @param ShortUrl $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShortUrl $url)
    {
        return $this->success($url->url->url, "ok", 201);
    }

    public function find(FindRequest $request)
    {
        $user = Auth::user();
        $url = Url::select("id")->where("url", $request->find)->first();
        $url_id = $url->id;

        $short = ShortUrl::where([
            ["url_id", "=", $url_id], ["user_id", "=", $user->id]
        ])->get();


        if (!empty($short))
            return $this->success($short, "Url data", 201);

            return $this->error("Not Found", 404);

    }


    public function search(SearchRequest $request)
    {

        $user_id = Auth::user()->id;
        $url_id  = Url::select("id")->where("url","LIKE","%$request->search%")->get();

        $data = ShortUrl::with("url")->where("user_id" , $user_id )->whereIn("url_id",$url_id)->orderBy("created_at" , "desc")->get();
        $count = count($data);

        if ( count($data) == 0)
            return $this->error("Not found");
            return $this->success( ["data" => $data, "count" => $count]  , "data found");
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