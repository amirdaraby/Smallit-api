<?php

namespace App\Http\Controllers\Api;
use App\Http\Controllers\Controller;
use App\Http\Requests\UrlRequest;
use App\Models\Domain;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use \Illuminate\Database\Eloquent\ModelNotFoundException;
class UrlShorterController extends BaseController
{

    public function index()
    {
        return ShortUrl::with("domain")->get();
    }

    public function FindOrNewDomain($url){
        $domain = Domain::where("url","=",$url)->first();
//        return $domain;
        if (! isset($domain) )
        {
            $domain = Domain::create([
                     "url"=>$url
                 ]);
            return $domain->id;
        }
        else return $domain->id;

    }


    public function store(UrlRequest $request)
    {
//        return $this->FindOrNewDomain($request->url);
       return ShortUrl::create([
            "url"=>"abcefg",
            "domain_id"=>$this->FindOrNewDomain($request->url),
            "user_id"=>1 // TODO change this
        ]);
    }


    public function show(ShortUrl $url)
    {
            return $this->success($url->domain->url,"ok" , 201);
    }


    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return int
     */
    public function destroy(ShortUrl $url)
    {
        return ShortUrl::destroy($url->id);
    }
}
