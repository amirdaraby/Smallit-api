<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Hashids\Hashids;
use App\Models\Url;
use Illuminate\Http\Request;

class BaseController extends Controller
{
    public static function success($data, $message, $code = 200)
    {
        return response()->json(["status" => "ok", 'data' => $data, "message" => $message], $code);
    }


    public static function error($message, $code = 422)
    {
        return response()->json(["status" => "error", "message" => $message],$code);
    }

    /*
     *
     * Find Or New Domain
     * returns Domain ID
     *
     */

    public function FindOrNewUrl($url)
    {
        $LongUrl = Url::where("url", $url)->first();

        if (!isset($LongUrl)) {
            $LongUrl = Url::create([
                "url" => $url
            ]);
            return $LongUrl->id;
        } else return $LongUrl->id;

    }

    public function short($url) {

        $hashid = new Hashids();
        $response =  $hashid->encodeHex($url);
        return $response;
    }

}
