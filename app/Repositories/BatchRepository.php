<?php

namespace App\Repositories;

use App\Models\Batch;
use App\Repositories\Base\BaseRepository;
use Illuminate\Database\Eloquent\Model;

class BatchRepository extends BaseRepository
{

    public function __construct(Batch $model)
    {
        parent::__construct($model);
    }

    public function findByUserId(int $userId)
    {
        return $this->model->query()->where("user_id" , "=", $userId)->paginate(10);
    }
}