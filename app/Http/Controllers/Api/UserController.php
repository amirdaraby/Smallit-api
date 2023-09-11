<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\UpdateRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Utils\Response;
use OpenApi\Annotations as OA;

class UserController extends BaseController
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Get(
     *     tags={"user"},
     *     path="/api/v1/user/show",
     *     summary="Show user",
     *     description="Responses user name and email",
     *     operationId="showUser",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *     response="200",
     *     description="Responses user data successfully",
     *     @OA\JsonContent(
     *
     *     @OA\Property(property="status",type="string", example="success"),
     *     @OA\Property(property="data",type="object",
     *     @OA\Property(property="name", type="string", example="yourName"),
     *     @OA\Property(property="email", type="string", format="email", example="user@test.com"),
     *    ),
     *   ),
     *  ),
     *    @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     * )
     */
    public function show(): JsonResponse
    {
        $user = $this->userRepository->findById(Auth::id(), ["name", "email"])->getAttributes();

        return Response::success($user, "User Data");
    }

    /**
     * @OA\Put(
     *     tags={"user"},
     *     path="/api/v1/user/update",
     *     summary="Update user",
     *     description="Updates user and response result as boolean. for changing email, password is required",
     *     operationId="updateUser",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *     response="500",
     *     description="Responses update status as false",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="boolean", example="false"),
     *     @OA\Property(property="message", type="string", example="User update failed")
     *     ),
     *     ),
     *
     *     @OA\Response(
     *     response="202",
     *     description="Responses update status as true",
     *     @OA\JsonContent(
     *     @OA\Property(property="status",type="string", example="success"),
     *     @OA\Property(property="data", type="boolean", example="true"),
     *     @OA\Property(property="message", type="string", example="User Updated")
     *     ),
     *   ),
     *     @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *
     *     @OA\Response(
     *     response="422",
     *     description="Validation failed",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="name", type="array", description="this error throws when request body did not contain name or email for update",
     *     @OA\Items(type="string", example={"update cannot perform with empty request"}),
     *     ),
     *     @OA\Property(property="email", type="array", @OA\Items(type="string", example={"The email must be a valid email address."})),
     *     @OA\Property(property="password", type="array", @OA\Items(type="string", example={"The password is incorrect."}))
     *
     *      ),
     *     ),
     *   ),
     * )
     */
    public function update(UpdateRequest $request): JsonResponse
    {
        $updated = $this->userRepository->update(Auth::id(), $request->validationData());


        return Response::success($updated, "User Updated", 202);
    }

    /**
     * @OA\Delete(
     *     tags={"user"},
     *     path="/api/v1/user/delete",
     *     summary="Delete user",
     *     description="Deletes user and response",
     *     operationId="deleteUser",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *     response="202",
     *     description="User deleted successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="boolean", example="true"),
     *     @OA\Property(property="message", type="string", example="User Deleted")
     *     )
     *   ),
     *    @OA\Response(response="401", ref="#/components/responses/Unauthorized"),
     *
     *    @OA\Response(response="500", description="Server failed to delete user at the moment",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *     @OA\Property(property="data", type="boolean", example="false"),
     *     @OA\Property(property="message", type="string",example="User delete failed")
     *     ),
     *   ),
     * )
     */
    public function delete(): JsonResponse
    {
        $deleted = $this->userRepository->delete(Auth::id());

        return Response::success($deleted, "User Deleted", 202);

    }
}
