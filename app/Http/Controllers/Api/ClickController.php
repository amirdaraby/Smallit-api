<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Click;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;


class ClickController extends BaseController
{

    public function index(ShortUrl $url)
    {
      return $url;
    }


    public function getBrowsers(ShortUrl $url)
    {
        $data = Click::query()->select(["browser", DB::raw("COUNT(*) as count")])
            ->where("shorturl_id", $url->id)
            ->groupBy("browser")
            ->orderBy("count", "desc")
            ->get();
        return $this->success(["shorturl" => $url, "clicks" => $data], "clicks grouped by browser for this shorturl");
    }

    public function getPlatforms(ShortUrl $url)
    {

        $data = Click::query()->select(["platform", DB::raw("COUNT(*) as count")])
            ->where("shorturl_id", $url->id)
            ->groupBy("platform")
            ->orderBy("count", "desc")
            ->get();
        return $this->success(["shorturl" => $url, "clicks" => $data], "clicks grouped by platform for this shorturl");
    }


    public function getBrowsersByDate(ShortUrl $url)
    {
        // todo
    }

    public function getPlatformsByDate(ShortUrl $url)
    {

    }


}
