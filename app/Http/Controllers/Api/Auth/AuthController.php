<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegisterRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class AuthController extends BaseController
{
    public function login(LoginRequest $request)
    {


        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth             = Auth::user();
            $success['token'] = $auth->createToken('LaravelSanctumAuth')->plainTextToken;
            $success['name']  = $auth->name;


            return $this->success($success, "user login", 202);
        } else return $this->error("user not found", 400);
    }


    public function register(RegisterRequest $request)
    {


        $input             = $request->all();
        $input['password'] = bcrypt($input['password']);

        $user = User::create($input);


        $success['token'] = $user->createToken("LaravelSanctumAuth")->plainTextToken;
        $success['name']  = $user->name;

        return $this->success($success, 'User registered', 201);

    }


}
