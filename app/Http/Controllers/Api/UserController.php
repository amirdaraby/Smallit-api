<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ShortUrl;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{
    public function userClicks(Request $request)
    {

        $user = Auth::user();

        return $this->success($user->LoadCount("clicks"), "reaches for this user (all short urls)");

    }

    public function userShortUrls(Request $request)
    {

        $data = ShortUrl::query()->withCount("clicks")
            ->where("user_id", Auth::id())
            ->paginate(10);

        return $data->isEmpty() ? $this->error("there is no short urls for this user")
            :  $this->success($data, "all short urls for this user (with clicks count)");
    }
    // todo

}
