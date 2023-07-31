<?php

namespace App\Repositories\Base;

use Illuminate\Database\Eloquent\Model;

interface BaseRepositoryInterface
{

    public function findById(int $id, array $columns = ["*"], array $relations = [], array $appends = []): ?Model;

    public function create(array $payload): ?Model;

    public function update(int $id, array $payload): bool;

    public function delete(int $id): bool;
}