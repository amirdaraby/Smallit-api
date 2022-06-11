<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlRequest;
use App\Models\Domain;
use App\Models\ShortUrl;
use App\Models\Url;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class UrlController extends Controller
{

    public function index()
    {
        $user_data = User::find(Auth::id())->first();
        $url_data = ShortUrl::where("user_id","=",Auth::id())->with("domain")->first();
        $data = [$user_data,$url_data];
        return $data;
        return view("dashboard",["data"=>$data]);
    }


    public function create()
    {
        return view('create');
    }

    public static function generateShortUrl($length = 6)
    {
        $chars = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';

        return substr(str_shuffle(str_repeat(Auth::id().$chars, $length)), 1, $length);
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

    public function store(UrlRequest $request)
    {

        ShortUrl::create([
            "url"=>$this->generateShortUrl(),
            "user_id"=>Auth::id(),
            "domain_id"=>$this->FindOrNewDomain($request->url)
        ]);

        return redirect(route("url.index"));
    }


    public function show($url = "")
    {

        $url = Url::where("short_url","=",$url)->first();
        if (! $url)
            abort(404);

        return redirect($url->url);
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($url)
    {
        Url::destroy($url);
        return redirect(route("url.index"));
    }
}
