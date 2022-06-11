<?php

namespace App\Http\Controllers;

use App\Http\Requests\UrlRequest;
use App\Models\Url;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlController extends Controller
{

    public function index()
    {
        $data = User::find(Auth::id())->with("url")->first();

        return view("dashboard",["data"=>$data]);
    }


    public function create()
    {
        return view('create');
    }

    public function generateShortUrl($length){

            $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $charactersLength = strlen($characters);
            $randomString = '';
            for ($i = 0; $i < $length; $i++) {
                $randomString .= $characters[rand(0, $charactersLength - 1)];
            }
            return $randomString;


    }



    public function store(UrlRequest $request)
    {

        return $this->generateShortUrl($request->url);
        Url::create([
            "url" => $request->url,
            "short_url"
        ]);

    }


    public function show($id)
    {
        //
    }


    public function edit($id)
    {
        //
    }


    public function update(Request $request, $id)
    {
        //
    }


    public function destroy($id)
    {
        //
    }
}
