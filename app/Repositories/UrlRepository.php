<?php

namespace App\Repositories;

use App\Models\Url;
use App\Repositories\Base\BaseRepository;
use Illuminate\Support\Facades\DB;

class UrlRepository extends BaseRepository
{

    public function __construct(Url $model)
    {
        parent::__construct($model);
    }

    public function findOrNew($payload)
    {
        return $this->model->query()->where($payload)->firstOrCreate($payload);
    }

    public function findByUserIdWithShortUrlAmount(int $id)
    {
        return $this->model->query()->select(["id", "url", DB::raw("(select count(*) from short_urls where url_id = urls.id) as short_url_amount"), "created_at"])
            ->where("user_id", "=", $id)
            ->groupBy(["id", "url"])
            ->orderBy("short_url_amount", "desc")
            ->paginate(10);
    }
}