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

}