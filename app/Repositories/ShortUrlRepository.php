<?php

namespace App\Repositories;

use App\Models\ShortUrl;
use App\Repositories\Base\BaseRepository;

class ShortUrlRepository extends BaseRepository
{

    public function __construct(ShortUrl $model)
    {
        parent::__construct($model);
    }

    public function findByUrlId(int $id){
        return $this->model->query()->select("*")->where("url_id", "=", $id)->paginate(15);
    }

}