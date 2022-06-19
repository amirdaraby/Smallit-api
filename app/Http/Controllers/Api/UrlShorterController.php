<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\UrlRequest;
//use App\Models\Domain;
use App\Models\ShortUrl;
use App\Models\Url;
use Illuminate\Http\Request;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Auth;

class UrlShorterController extends BaseController
{

    public function index()
    {


        $data = ShortUrl::with("url")->get();
        if (!empty($data))
            return $this->success($data, "ok");
        else
            return $this->error("record not found", 404);
    }

    /**
     * @param UrlRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UrlRequest $request)
    {

        $data = ShortUrl::create([
            "short_url" => "abcefg",
            "url_id" => $this->FindOrNewUrl($request->url),
            "user_id" => 1 // TODO change this
        ]);
//        return $data;
        return $this->success($data, "shorturl created", 201);
    }

    /**
     * @param ShortUrl $url
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(ShortUrl $url)
    {
        return $this->success($url->url->url , "ok",201);
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