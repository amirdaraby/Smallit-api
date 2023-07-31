<?php

namespace App\Http\Controllers\Api;

use App\Http\Requests\Auth\UpdateRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class UserController extends BaseController
{

    private $repository;

    public function __construct(UserRepository $userRepository)
    {
        $this->repository = $userRepository;
    }

    public function show()
    {
        $user = $this->repository->findById(Auth::id(), ["name", "email"])->getAttributes();

        return responseSuccess(["user" => $user], "User Data");
    }

    public function update(UpdateRequest $request)
    {
        $updated = $this->repository->update(Auth::id(), $request->validationData());
        return responseSuccess(["updated" => $updated] , "User Updated", 202);
    }

    public function delete()
    {
        $deleted = $this->repository->delete(Auth::id());
        return responseSuccess(["deleted" => $deleted], "User Deleted", 202);
    }
}
