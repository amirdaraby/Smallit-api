<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Repositories\UrlRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UrlController extends BaseController
{
    protected UrlRepository $urlRepository;

    public function __construct(UrlRepository $urlRepository)
    {
        $this->urlRepository = $urlRepository;
    }

    public function all()
    {

        $urls = $this->urlRepository->findByUserIdWithShortUrlAmount(Auth::id());

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
