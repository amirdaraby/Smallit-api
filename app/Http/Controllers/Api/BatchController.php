<?php

namespace App\Http\Controllers\Api;

use App\Repositories\BatchRepository;
use App\Repositories\ShortUrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Utils\Response;

class BatchController extends BaseController
{
    protected BatchRepository $batchRepository;
    protected ShortUrlRepository $shortUrlRepository;
    public function __construct(BatchRepository $batchRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->batchRepository = $batchRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    public function all(): JsonResponse
    {
        $batches = $this->batchRepository->findByUserId(Auth::id());

        return !$batches->isEmpty() ? Response::success($batches->toArray(), "all batches created by user", 200)
            : Response::error("this user doesn't have any batches", 404);
    }

    public function show(int $id): JsonResponse
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        return Response::success($batch->toArray(), "batch's data", 200);
    }

    public function delete(int $id): JsonResponse
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        if ($batch->status != "success")
            return Response::error("cannot delete a unsuccessful batch", 400);

        return $this->batchRepository->delete($id) ? Response::success(true, "batch deleted successfully", 200)
            : Response::error("batch delete failed", 500, false);

    }

    public function showShortUrls(int $id): JsonResponse{
        $batch = $this->batchRepository->findById($id);
        Gate::authorize("batch-owner", $batch);

        if ($batch->status != "success")
            return Response::error("please wait until this batch completes creating short urls", 409, null);

        $batchWithShortUrls = $this->shortUrlRepository->findByBatchId($batch->id);

        return Response::success($batchWithShortUrls, "short urls of batch", 200);
    }
}
