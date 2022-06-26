<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
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

        $user =  Auth::user();
        $short = ShortUrl::with("url")->where("user_id",Auth::user()->id)->get();

        return $this->success(["user" => $user , "short" => $short],"User Data");
    }

    /**
     * @param UrlRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(UrlRequest $request)
    {

        $data = ShortUrl::create([
            "short_url" => Str::random(5),
            "url_id" => $this->FindOrNewUrl($request->url),
            "user_id" => auth()->user()->id
        ]);

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
