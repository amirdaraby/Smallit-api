<?php

namespace App\Http\Controllers\Api;

use App\Repositories\ShortUrlRepository;
use App\Repositories\UrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Gate;
use App\Utils\Response;
class UrlController extends BaseController
{
    protected UrlRepository $urlRepository;
    protected ShortUrlRepository $shortUrlRepository;

    public function __construct(UrlRepository $urlRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->urlRepository = $urlRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function all(Request $request): JsonResponse
    {

        $page = $request->get("page") ?? 1;
        $user_id = Auth::id();

        $urls = Cache::tags("user_{$user_id}_urls")->remember("user_urls_{$user_id}_{$page}", 60 * 30, function () use ($user_id) {
            return $this->urlRepository->findByUserIdWithShortUrlAmount($user_id);
        });

        if ($urls->isEmpty())
            return Response::success(null, "this user doesn't have any urls", 404);

        return Response::success($urls, "all urls");
    }

    public function show(int $id): JsonResponse
    {
        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        return Response::success($url->toArray(), "url found successfully", 200);
    }

    public function delete(int $id): JsonResponse
    {
        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        $deleted = $this->urlRepository->delete($url->id);

        if ($deleted)
            return Response::success($deleted, "url and url's deleted successfully", 200);
        return Response::error("server error, try again later", 500, null);
    }

    public function showShortUrls(int $id) :JsonResponse
    {

        $url = $this->urlRepository->findById($id);

        Gate::authorize("url-owner", $url);

        $shortUrls = $this->shortUrlRepository->findByUrlId($url->id);

        if ($shortUrls->isEmpty())
            return Response::error("this url has no short urls", 404);

        return Response::success($shortUrls, "all short urls of url: $url->url", 200);
    }

    public function search(Request $request) :JsonResponse
    {

        $urls = $this->urlRepository->searchByUrl($request->get("q"), \auth()->user()->id);

        if ($urls->isEmpty())
            return Response::error("Not Found", 404);
        return Response::success($urls->toArray(), "search results");
    }
}
