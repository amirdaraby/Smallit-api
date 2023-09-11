<?php

namespace App\Http\Controllers\Api;

use App\Repositories\BatchRepository;
use App\Repositories\ShortUrlRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use App\Utils\Response;
use OpenApi\Annotations as OA;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class BatchController extends BaseController
{
    protected BatchRepository $batchRepository;
    protected ShortUrlRepository $shortUrlRepository;
    public function __construct(BatchRepository $batchRepository, ShortUrlRepository $shortUrlRepository)
    {
        $this->batchRepository = $batchRepository;
        $this->shortUrlRepository = $shortUrlRepository;
    }

    /**
     * @OA\Get(
     *     tags={"batches"},
     *     path="/api/v1/user/batches/all",
     *     summary="All batches",
     *     description="Returns all batches created by user as paginated response",
     *     operationId="allBatches",
     *     security={{"bearerAuth":{}}},
     *
     *      @OA\Parameter(
     *      name="page",
     *      description="specifies page of pagination",
     *      in="query",
     *      required=false,
     *      example="1"
     *      ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound", description="user doesn't have enough resource to show in this page"),
     *
     *
     *     @OA\Response(response="200", description="returns all batches created by user successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="current_page", type="int", example=1),
     *     @OA\Property(property="data", type="array",
     *     @OA\Items(type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="name", type="string", example="batchName"),
     *     @OA\Property(property="status", type="string", example="success", description="batch's can be queue, success or failed"),
     *     @OA\Property(property="user_id", type="int", example="1"),
     *     @OA\Property(property="url_id", type="int", example="1"),
     *     @OA\Property(property="amount", type="int", example="1000"),
     *     @OA\Property(property="created_at", type="string", example="2023-09-09T13:32:40.000000Z"),
     *     @OA\Property(property="updated_at", type="string", example="2023-09-09T13:32:40.000000Z")
     *     ),
     *     ),
     *     @OA\Property(property="first_page_url", type="string", example="https://example.com/api/v1/user/batches/all?page=1"),
     *     @OA\Property(property="from", type="int", example="1"),
     *     @OA\Property(property="last_page", type="int", example="1"),
     *     @OA\Property(property="last_page_url", type="string", example="https://example.com/api/v1/user/batches/all?page=1"),
     *
     *     @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *     @OA\Items(type="object",
     *     @OA\Property(property="url", type="string", example="https://example.com/api/v1/user/batches/all?page=2"),
     *     @OA\Property(property="label", type="string", example="&laquo; Next"),
     *     @OA\Property(property="active", type="boolean", example="true"),
     *     ),
     *     ),
     *     @OA\Property(property="next_page_url", type="string", example="https://example.com/api/v1/user/batches/all?page=2"),
     *     @OA\Property(property="path", type="string", example="https://example.com/api/v1/user/batches/all"),
     *     @OA\Property(property="per_page", type="int", example="10"),
     *     @OA\Property(property="prev_page_url", type="string", example="https://example.com/api/v1/user/batches/all?page=0"),
     *     @OA\Property(property="to", type="int", example="10"),
     *     @OA\Property(property="total", type="int", example="20"),
     *     ),
     *     @OA\Property(property="message", type="string", example="all batches created by user"),
     *     )
     *  ),
     * )
     */
    public function all(): JsonResponse
    {
        $batches = $this->batchRepository->findByUserId(Auth::id());

        return !$batches->isEmpty() ? Response::success($batches->toArray(), "all batches created by user", 200)
            : throw new NotFoundHttpException();
    }

    /**
     * @OA\Get(
     *     tags={"batches"},
     *     path="/api/v1/user/batches/{id}",
     *     summary="Show batch",
     *     description="get batch by id",
     *     operationId="showBatch",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of batch",
     *     required=true,
     *     example="1"
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#components/responses/NotFound"),
     *     @OA\Response(response="200", description="responses requested batch",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="name", type="string", example="batchName"),
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="user_id", type="int", example="1"),
     *     @OA\Property(property="url_id", type="int", example="1"),
     *     @OA\Property(property="amount", type="int", example="1000"),
     *     @OA\Property(property="created_at", type="string", example="2023-09-09T13:32:40.000000Z"),
     *     @OA\Property(property="updated_at", type="string", example="2023-09-09T13:32:40.000000Z"),
     *     ),
     *     @OA\Property(property="message", type="string", example="batch's data"),
     *     ),
     *     )
     * )
     *
     */
    public function show(int $id): JsonResponse
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        return Response::success($batch->toArray(), "batch's data", 200);
    }

    /**
     * @OA\Delete(
     *     tags={"batches"},
     *     path="/api/v1/user/batches/{id}",
     *     summary="Delete batch",
     *     description="Deletes requested batch and response result as boolean",
     *     operationId="deleteBatch",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of batch",
     *     required=true,
     *     example="1"
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *
     *     @OA\Response(response="202", description="successfully deletes batch",
     *     @OA\JsonContent(
     *       @OA\Property(property="status", type="string", example="success"),
     *       @OA\Property(property="data", type="boolean", example="true"),
     *       @OA\Property(property="message", type="string", example="Batch deleted"),
     *     ),
     *     ),
     *     @OA\Response(response="500", description="server cannot delete this batch",
     *      @OA\JsonContent(
     *        @OA\Property(property="status", type="string", example="error"),
     *        @OA\Property(property="data", type="boolean", example="false"),
     *        @OA\Property(property="message", type="string", example="batch delete failed"),
     *      ),
     *     ),
     *     @OA\Response(response="400", description="cannot delete when batch's status is not success",
     *      @OA\JsonContent(
     *        @OA\Property(property="status", type="string", example="error"),
     *        @OA\Property(property="data", type="boolean", example="false"),
     *        @OA\Property(property="message", type="string", example="cannot delete a unsuccessful batch"),
     *      ),
     *     )
     *
     * )
     *
     */
    public function delete(int $id): JsonResponse
    {
        $batch = $this->batchRepository->findById($id);

        Gate::authorize("batch-owner", $batch);

        if ($batch->status != "success")
            return Response::error("cannot delete a unsuccessful batch", 400);

        $deleted = $this->batchRepository->delete($id);
        return Response::success($deleted, "Batch deleted", 202);

    }

    /**
     * @OA\Get(
     *     tags={"batches", "short urls"},
     *     path="/api/v1/user/batches/{id}/short-urls",
     *     summary="Show batch short urls",
     *     description="responses all short urls created by this batch paginated",
     *     operationId="showBatchShortUrls",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\PathParameter(
     *     name="id",
     *     description="id of batch",
     *     required=true,
     *     example="1"
     *     ),
     *
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *     @OA\Response(response="403", ref="#/components/responses/Forbidden"),
     *     @OA\Response(response="404", ref="#/components/responses/NotFound"),
     *
     *     @OA\Response(response="409",
     *     description="cannot show short-urls of batch when batch is not completed",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *     @OA\Property(property="data", example=null),
     *     @OA\Property(property="message", type="string", example="please wait until this batch completes creating short urls"),
     *     ),
     *     ),
     *     @OA\Response(response="200", description="return all short urls created by batch",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="current_page", type="int", example="1"),
     *     @OA\Property(property="data", type="array",
     *     @OA\Items(type="object",
     *     @OA\Property(property="id", type="int", example="1"),
     *     @OA\Property(property="url_id", type="int", example="1"),
     *     @OA\Property(property="short_url", type="string", example="success", description="batch's can be queue, success or failed"),
     *     @OA\Property(property="long_url", type="int", example="1"),
     *     @OA\Property(property="total_click", type="int", example="100"),
     *     ),
     *     ),
     *     @OA\Property(property="first_page_url", type="string", example="https://example.com/api/v1/user/batches/1/short-urls?page=1"),
     *     @OA\Property(property="from", type="int", example="1"),
     *     @OA\Property(property="last_page", type="int", example="1"),
     *     @OA\Property(property="last_page_url", type="string", example="https://example.com/api/v1/user/batches/1/short-urls?page=1"),
     *
     *     @OA\Property(property="links" , type="array", collectionFormat="multi", description="presents the links of pagination",
     *     @OA\Items(type="object",
     *     @OA\Property(property="url", type="string", example="https://example.com/api/v1/user/batches/1/short-urls?page=2"),
     *     @OA\Property(property="label", type="string", example="&laquo; Next"),
     *     @OA\Property(property="active", type="boolean", example="true"),
     *     ),
     *     ),
     *     @OA\Property(property="next_page_url", type="string", example="https://example.com/api/v1/user/batches/short-urls?page=2"),
     *     @OA\Property(property="path", type="string", example="https://example.com/api/v1/user/batches/short-urls"),
     *     @OA\Property(property="per_page", type="int", example="15"),
     *     @OA\Property(property="prev_page_url", type="string", example="https://example.com/api/v1/user/batches/short-urls?page=0"),
     *     @OA\Property(property="to", type="int", example="15"),
     *     @OA\Property(property="total", type="int", example="20"),
     *     ),
     *     @OA\Property(property="message", type="string", example="short urls of batch"),
     *     )
     *   ),
     * )
     *
     */
    public function showShortUrls(int $id): JsonResponse{
        $batch = $this->batchRepository->findById($id);
        Gate::authorize("batch-owner", $batch);

        if ($batch->status != "success")
            return Response::error("please wait until this batch completes creating short urls", 409, null);

        $batchWithShortUrls = $this->shortUrlRepository->findByBatchId($batch->id);

        return Response::success($batchWithShortUrls, "short urls of batch", 200);
    }
}
