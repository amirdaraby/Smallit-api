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

}