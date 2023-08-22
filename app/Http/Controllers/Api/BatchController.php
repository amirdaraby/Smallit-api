<?php

namespace App\Http\Controllers\Api;

use App\Repositories\BatchRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;

class BatchController extends BaseController
{
    protected BatchRepository $batchRepository;

    public function __construct(BatchRepository $batchRepository)
    {
        $this->batchRepository = $batchRepository;
    }

    public function all(): object
    {
        $batches = $this->batchRepository->findByUserId(Auth::id());

        return !$batches->isEmpty() ? responseSuccess($batches->toArray(), "all batches created by user", 200)
            : responseError("this user doesn't have any batches", 404);
    }

    public function show(int $id): object
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        return responseSuccess($batch->toArray(), "batch's data", 200);
    }

    public function delete(int $id)
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        if ($batch->status != "success")
            return responseError("cannot delete a unsuccessful batch", 400);

        return $this->batchRepository->delete($id) ? responseSuccess(true, "batch deleted successfully", 200)
            : responseError("batch delete failed", 500, false);

    }
}
