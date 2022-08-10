<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\ClickResource;
use App\Models\Click;
use App\Models\ShortUrl;
use App\Models\User;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\UnauthorizedException;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;


class ClickController extends BaseController
{

    public function getAll(ShortUrl $url)
    {
        $reach = Click::all();
        // todo resource
        return $reach->load("shorturl");
    }

    /**
     * @throws AuthenticationException
     */
    public function getBrowsers(ShortUrl $url)
    {

        if (!Gate::allows("reach", $url))
            throw new AuthorizationException();

        $data = Click::query()->select(["browser", DB::raw("COUNT(*) as count")])
            ->where("shorturl_id", $url->id)
            ->groupBy("browser")
            ->orderBy("count", "desc")
            ->get()
            ->toArray();

        return !empty($data) ? $this->success(["shorturl" => $url, "clicks" => $data], "clicks grouped by browser for this shorturl")
            : $this->error("there is no reaches sorted by browser", 404);
    }

    public function getPlatforms(ShortUrl $url)
    {

        if (!Gate::allows("reach", $url))
            throw new AuthorizationException();

        $data = Click::query()->select(["platform", DB::raw("COUNT(*) as count")])
            ->where("shorturl_id", $url->id)
            ->groupBy("platform")
            ->orderBy("count", "desc")
            ->get()
            ->toArray();

        return !empty($data) ? $this->success(["shorturl" => $url, "clicks" => $data], "clicks grouped by platform for this shorturl")
            : $this->error("there is no reaches sorted by platform", 404);
    }


    public function getCount(ShortUrl $url){
        return $url->loadCount("clicks");
    }


    public function getBrowsersByDate(ShortUrl $url)
    {
        // todo
    }

    public function getPlatformsByDate(ShortUrl $url)
    {

    }


}
