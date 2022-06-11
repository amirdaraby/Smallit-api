<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlRequest;
use App\Models\Domain;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\JsonResponse;

class UrlApiController extends Controller
{

    public function index()
    {
        return \response()->json(["hello"], 500); // Status
    }

    public function FindOrNewDomain($url){

        if (! Domain::where("url","=",$url)->first())
        {
            Domain::create([
                "url"=>$url
            ]);
        }
        return Domain::where("url","=",$url)->first()->id;

    }

    public function generateShortUrl($length = 6)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat(Auth::id().$chars, $length)), 1, $length);
    }


    public function store(UrlRequest $request)
    {
//        return $request->all();
       $res = ShortUrl::create([
            "url"=>$this->generateShortUrl(),
            "user_id"=>1, // TODO change this
            "domain_id"=>$this->FindOrNewDomain($request->url)
        ]);
        return \response()->json([
            "status"=>$res
        ],201);
    }


    public function show($id)
    {
        //
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
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
