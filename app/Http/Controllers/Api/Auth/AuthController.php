<?php

namespace App\Http\Controllers\Api\Auth;

use App\Http\Controllers\Api\BaseController;
use App\Http\Requests\Auth\LoginRequest;
use App\Http\Requests\Auth\RegisterRequest;
use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use App\Utils\Response;
use OpenApi\Annotations as OA;


class AuthController extends BaseController
{
    private UserRepository $userRepository;

    public function __construct(UserRepository $userRepository)
    {
        $this->userRepository = $userRepository;
    }

    /**
     * @OA\Post(
     *     tags={"auth"},
     *     path="/api/v1/register",
     *     summary="Register",
     *     description="Registers user and Response name, email and token",
     *     operationId="authRegister",
     *     @OA\RequestBody(
     *     required=true,
     *     description="Registers new user",
     *     @OA\JsonContent(
     *     required={"name","email", "password"},
     *     @OA\Property(property="name", type="string", example="yourName"),
     *     @OA\Property(property="email", type="string", format="email", example="user@test.com"),
     *     @OA\Property(property="password", type="string", format="password", example="123456789")
     *     ),
     * ),
     *     @OA\Response(
     *     response="422",
     *     description="Validation Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *
     *     @OA\Property(property="data", type="object", description="returns validation errors list",
     *     @OA\Property(property="name", type="array", @OA\Items(type="string", example={"The name field is required."})),
     *     @OA\Property(property="email", type="array", @OA\Items(type="string", format="email", example={"The email field is required."})),
     *     @OA\Property(property="password", type="array", @OA\Items(type="string", format="password", example={"The password field is required."}))
     *
     *     ),
     *     @OA\Property(property="message", type="string", example="Validation failed")
     *     ),
     *   ),
     *     @OA\Response(
     *     response="201",
     *     description="User register",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", type="json", example={"token"="laravel_sanctum_...", "name"="yourName", "email"="user@test.com"}),
     *     @OA\Property(property="message", type="string", example="User registered"),
     *    )
     *   )
     *  ),
     * )
     */
    public function register(RegisterRequest $request)
    {

        $user = $this->userRepository->create($request->validationData());

        $response['token'] = $user->createToken("token")->plainTextToken;
        $response['name'] = $user->name;
        $response['email'] = $user->email;

        return Response::success($response, 'User registered', 201);

    }

    /**
     * @OA\Post(
     *     tags={"auth"},
     *     path="/api/v1/login",
     *     summary="Login",
     *     description="Login user and Response name, email and token",
     *     operationId="authLogin",
     *     @OA\RequestBody(
     *     required=true,
     *     description="use email and password to login and get bearer token",
     *     @OA\JsonContent(
     *     required={"email", "password"},
     *     @OA\Property(property="email", type="string", format="email", example="user@test.com"),
     *     @OA\Property(property="password", type="string", format="password", example="123456789")
     *     ),
     * ),
     *
     *     @OA\Response(
     *     response="422",
     *     description="Validation Error",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="email", type="array", collectionFormat="multi", @OA\Items(type="string", example={"The email field is required."})),
     *     @OA\Property(property="password", type="array", collectionFormat="multi", @OA\Items(type="string", example={"The password field is required."}))
     *      ),
     *
     *     @OA\Property(property="message", type="string", example="Validation failed")
     *      ),
     *    ),
     *
     *     @OA\Response(
     *     response="401",
     *     description="Invalid credentials response",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *     @OA\Property(property="data", example="null"),
     *     @OA\Property(property="message", type="string", example="Email or password is invalid")
     *     ),
     *   ),
     *     @OA\Response(
     *     response="202",
     *     description="User login",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *
     *     @OA\Property(property="data", type="object",
     *     @OA\Property(property="token", type="string", example="laravel_sanctum_..."),
     *     @OA\Property(property="name", type="string", example="yourName"),
     *     @OA\Property(property="email", type="string",format="email", example="user@test.com"),
     *     ),
     *
     *     @OA\Property(property="message", type="string", example="User login"),
     *    )
     *   )
     *  ),
     * )
     */
    public function login(LoginRequest $request)
    {

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $auth = Auth::user();
            $response['token'] = $auth->createToken('LaravelSanctumAuth')->plainTextToken;
            $response['name'] = $auth->name;
            $response['email'] = $auth->email;
            return Response::success($response, "User login", 202);
        } else return Response::error("Email or Password is invalid", 401);
    }

    /**
     * @OA\Delete(
     *     tags={"user","auth"},
     *     path="/api/v1/user/logout",
     *     summary="Logout",
     *     description="Logout user and invalidate token",
     *     operationId="authLogout",
     *     security={{"bearerAuth":{}}},
     *
     *     @OA\Response(
     *     response="202",
     *     description="User logged out successfully",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="success"),
     *     @OA\Property(property="data", example=null),
     *     @OA\Property(property="message", type="string", example="logged out")
     *    )
     * ),
     *    @OA\Response(
     *     response="401",
     *     description="Authentication error",
     *     @OA\JsonContent(
     *     @OA\Property(property="status", type="string", example="error"),
     *     @OA\Property(property="data", example=null),
     *     @OA\Property(property="message", type="string", example="Unauthorized")
     *     ),
     *  ),
     * )
     */
    public function logout()
    {
        $deleted = Auth::user()->tokens()->delete();
        if ($deleted)
            return Response::success([], "logged out", 202);
    }

}
