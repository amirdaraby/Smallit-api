<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;

class BaseRepository implements BaseRepositoryInterface
{
    protected Model $model;

    public function __construct(Model $model)
    {
        $this->model = $model;
    }

    public function findById(int $id, array $columns = ["*"], array $relations = [], array $appends = []): ?Model
    {
        return $this->model->query()->select($columns)->with($relations)->findOrFail($id)->append($appends);
    }

    public function create(array $payload): ?Model
    {
        return $this->model->query()->create($payload);
    }

    public function update(int $id, array $payload): bool
    {
        $model = $this->model->query()->findOrFail($id);

        return $model->update($payload);
    }

    public function delete(int $id): bool
    {
        return $this->model->query()->findOrFail($id)->delete();
    }

}