<?php

namespace App\Repositories;

use App\Models\ShortUrl;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class ShortUrlRepository extends BaseRepository
{

    public function __construct(ShortUrl $model)
    {
        parent::__construct($model);
    }

    public function findByUrlId(int $id): LengthAwarePaginator
    {
        return $this->model->query()->select(["id", "url_id", "short_url", DB::raw("(select count(*) from clicks where clicks.short_url_id = short_urls.id) as total_clicks")])->where("url_id", "=", $id)->paginate(15);
    }

    public function findByBatchId(int $id): LengthAwarePaginator
    {
        return $this->model->query()->select(["id", "url_id", "short_url", DB::raw("(select url from urls where urls.id = short_urls.url_id) as long_url"), DB::raw("(select count(*) from clicks where short_urls.id = clicks.short_url_id) as total_clicks")])->where("batch_id", "=", $id)->paginate(15);
    }

    public function findByUserId(int $id): LengthAwarePaginator
    {
        return $this->model->query()->select(["id", "url_id", "short_url", DB::raw("(select url from urls where urls.id = short_urls.url_id) as long_url"), DB::raw("(select count(*) from clicks where short_urls.id = clicks.short_url_id) as total_clicks")])->where("user_id", "=", $id)->paginate(15);
    }

    public function findByShortUrlWithLongUrlOrFail(string $shortUrl)
    {
        return $this->model->query()->select(["id", "short_url", DB::raw("(select url from urls where urls.id = short_urls.url_id) as long_url")])->where("short_url", "=", $shortUrl)->firstOrFail();
    }

    public function findByIdWithLongUrlAndClicksAmount(int $id)
    {
        return $this->model->query()->select(["*", DB::raw("(select count(*) from clicks where short_urls.id = clicks.short_url_id) as total_clicks")])->with("url")->findOrFail($id);
    }
}