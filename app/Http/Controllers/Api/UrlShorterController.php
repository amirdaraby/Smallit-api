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

class UrlShorterController extends BaseController
{

    public function index()
    {

        $user = Auth::user();
//        $url = ShortUrl::select("url_id")->where("user_id",$user->id);

        $url = ShortUrl::with("url")->select("url_id", DB::raw('count(*) as count'))
            ->where("user_id", $user->id)->groupBy('url_id')
            ->orderBy("COUNT", "desc")->get();


        return $this->success(["user" => $user, "url" => $url], "User Data");
    }


    /**
     * @param UrlRequest $request
     * @return string
     */
    public function store(UrlRequest $request)
    {

        global $data;

        for ($i = 0; $i < $request->count; $i++) {
            $data [$i] = ShortUrl::create([
                "short_url" => Str::random(5),
                "url_id" => $this->FindOrNewUrl($request->url),
                "user_id" => auth()->user()->id
            ]);
        }

        if (count($data) == $request->count)
            return $this->success($data, "ok", 200);
        else
            ddd($data);
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


        $short = ShortUrl::with("url")->where("user_id", $user->id)->where("url_id", 5)->get();

        if ($short-)
            return $this->success($short, "Url data", 201);
        else
            return $this->error("Not Found", 404);

    }


    public function search(SearchRequest $request)
    {
        $short = ShortUrl::with("url")->where("user_id", Auth::user()->id)->get();
        $data = [];
        $i = 0;


        foreach ($short as $item) {
            if (preg_match("/.*http.*/i", $item->url->url))
                if ($request->search == $item->url->url)
                    $data [$i++] = $item;
                elseif ($request->search == $item->url->url)
                    $data [$i++] = $item;
        }

        if ($data)
            return $this->success($data, "ok");

        else
            return $this->error("not found", 404);
    }


    public function test()
    {
        $short = ShortUrl::all();


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
        return $this->success(ShortUrl::destroy($url->id), "data deleted", 202);
    }
}