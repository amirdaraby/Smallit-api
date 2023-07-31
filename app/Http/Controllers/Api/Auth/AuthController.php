<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;

class AuthController extends BaseController
{
    private UserRepository $userRepository;
    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    public function login(LoginRequest $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $response['token'] = $auth->createToken('LaravelSanctumAuth')->plainTextToken;
            $response['name'] = $auth->name;
            $response['email'] = $auth->email;
            return responseSuccess($response, "User login", 202);
        } else return responseError("Email or Password is invalid", 401);
    }


    public function register(RegisterRequest $request)
    {

        $user = $this->userRepository->create($request->validationData());

        $response['token'] = $user->createToken("token")->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return responseSuccess($response, 'User registered', 201);

    }

    public function logout()
    {
        $deleted = Auth::user()->tokens()->delete();
        if ($deleted)
            return responseSuccess([], "logged out !");
    }

}
