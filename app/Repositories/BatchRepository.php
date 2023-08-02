<?php

namespace App\Repositories;

use App\Models\Batch;
use App\Repositories\Base\BaseRepository;

class BatchRepository extends BaseRepository
{

    public function __construct(Batch $model)
    {
        parent::__construct($model);
    }

}