<?php

namespace App\Repositories;

use App\Models\Click;
use App\Repositories\Base\BaseRepository;


class ClickRepository extends BaseRepository
{

    public function __construct(Click $model)
    {
        parent::__construct($model);
    }

    public function findBasicInsightsByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()->selectRaw("count(*) as total_clicks, count(distinct uid) as unique_clicks, max(created_at) as last_click_time")
            ->fromSub("select * from clicks where short_url_id = ?", "clicks_data")->setBindings([$shortUrlId])->first();
    }

    public function findByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()->select("*")->where("short_url_id", $shortUrlId)->orderByDesc("created_at")->paginate(10);
    }

    public function findTotalBrowsersInsightByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()
            ->selectRaw("browser, count(*) as total_clicks")
            ->where("short_url_id", $shortUrlId)
            ->groupBy(["browser"])->orderByDesc("total_clicks")
            ->get();
    }

    public function findTotalPlatformsInsightByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()
            ->selectRaw("platform, count(*) as total_clicks")
            ->where("short_url_id", $shortUrlId)
            ->groupBy(["platform"])->orderByDesc("total_clicks")
            ->get();
    }


    public function findAllUniqueClicksByShortUrl(int $shortUrlId){
        return $this->model->query()
            ->select("*")
            ->where("short_url_id", $shortUrlId)
            ->whereNotNull("uid")
            ->orderByDesc("created_at")
            ->paginate(10);
    }

    public function findUniqueBrowsersInsightByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()
            ->selectRaw("browser, count(distinct uid) as unique_clicks")
            ->where("short_url_id", $shortUrlId)->whereNotNull("uid")
            ->groupBy(["browser"])->orderByDesc("unique_clicks")
            ->get();
    }

    public function findUniquePlatformsInsightByShortUrlId(int $shortUrlId)
    {
        return $this->model->query()
            ->selectRaw("platform, count(distinct uid) as unique_clicks")
            ->where("short_url_id", $shortUrlId)->whereNotNull("uid")
            ->groupBy(["platform"])->orderByDesc("unique_clicks")
            ->get();
    }
}