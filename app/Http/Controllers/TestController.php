<?php

namespace App\Http\Controllers;

use App\Models\Url;
use http\Header;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(){
        Url::create([
            "url" => "https://google.com",
            "short_url"=>"https://localhost:8088/jjj",
            "user_id"=>1
        ]);
    }
    public function show ($url){

        $short = Url::where("short_url","=",$url)->first();
        return redirect($short->url);
    }
}
