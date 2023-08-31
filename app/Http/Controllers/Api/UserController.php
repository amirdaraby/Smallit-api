<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\UpdateRequest;
use App\Repositories\UserRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Utils\Response;

class UserController extends BaseController
{

    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function show(): JsonResponse
    {
        $user = $this->userRepository->findById(Auth::id(), ["name", "email"])->getAttributes();

        return Response::success(["user" => $user], "User Data");
    }

    public function update(UpdateRequest $request): JsonResponse
    {
        $updated = $this->userRepository->update(Auth::id(), $request->validationData());
        return Response::success(["updated" => $updated], "User Updated", 202);
    }

    public function delete(): JsonResponse
    {
        $deleted = $this->userRepository->delete(Auth::id());
        return Response::success(["deleted" => $deleted], "User Deleted", 202);
    }
}
