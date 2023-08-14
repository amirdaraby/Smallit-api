<?php

namespace App\Http\Controllers\Api;

use App\Repositories\UrlRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class UrlController extends BaseController
{
    protected UrlRepository $urlRepository;

    public function __construct(UrlRepository $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    public function all(Request $request)
    {

        $page = $request->get("page") ?? 1;
        $user_id = Auth::id();

        $urls = Cache::tags("user_{$user_id}_urls")->remember("user_urls_{$user_id}_{$page}", 60 * 30, function () use ($user_id) {
            return $this->urlRepository->findByUserIdWithShortUrlAmount($user_id);
        });

        if ($urls->isEmpty())
            return responseSuccess(null, "this user doesn't have any urls", 204);

        return responseSuccess($urls, "all urls");
    }

    public function show(int $id)
    {

    }

    public function delete(int $id)
    {

    }
}
