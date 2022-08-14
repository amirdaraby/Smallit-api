<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;

//use Hashids\Hashids;
use App\Models\Url;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;

class BaseController extends Controller
{
    public static function success($data, $message, $code = 200): object
    {
        return response()->json(["status" => "ok", 'data' => $data, "message" => $message], $code);
    }


    public static function error($message, $code = 422, $data = null): object
    {
        return response()->json(["status" => "error", "message" => $message, "data" => $data], $code);
    }

    /*
     *
     * Find Or New Domain
     * returns Domain ID
     *
     */

    public function FindOrNewUrl($url)
    {
        $LongUrl = Url::where([["url", "=", $url], ["user_id", "=", Auth::id()]])->first();


        if (!isset($LongUrl)) {
            $LongUrl = Url::create([
                "url"     => $url,
                "user_id" => Auth::id(),
//                "created_at" => now()
            ]);

            return $LongUrl->id;
        } else return $LongUrl->id;

    }


    public static function regexUrl($url): bool
    {

        if (preg_match("/\/+$/m", $url))
            return 0;
        return 1;

    }


}
