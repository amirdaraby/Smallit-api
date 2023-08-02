<?php

namespace App\Repositories;

use App\Models\Url;
use App\Repositories\Base\BaseRepository;

class UrlRepository extends BaseRepository
{

    public function __construct(Url $model)
    {
        parent::__construct($model);
    }

    public function findOrNew($payload){

    }

}