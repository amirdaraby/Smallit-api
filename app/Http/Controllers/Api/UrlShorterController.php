<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\SearchRequest;
use App\Http\Requests\UrlRequest;

//use App\Models\Domain;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Http\Request;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UrlShorterController extends BaseController
{

    public function index()
    {
//dd("something");

        $user = Auth::user();
        $short = ShortUrl::with("url")->where("user_id", Auth::user()->id)->get();

        return $this->success(["user" => $user, "short" => $short], "User Data");
    }

    /*
     *
     *
     *  Sends User Data for web header
     */




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
            return $this->error("create failed", 500);


    }

    /**
     * @param ShortUrl $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShortUrl $url)
    {
        return $this->success($url->url->url, "ok", 201);
    }

    public function search(SearchRequest $request)
    {
        $short = ShortUrl::with("url")->where("user_id", Auth::user()->id)->get();
        $data = [];
        $i = 0;

        foreach ($short as $item) {
            if (preg_match("/.*$request->search.*/i", $item->url->url))
                $data [$i++] = $item;
        }

        if ($data)
            return $this->success($data, "ok");

        else
            return $this->error("not found", 404);
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
