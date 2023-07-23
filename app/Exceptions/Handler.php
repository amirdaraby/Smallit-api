<?php

namespace App\Exceptions;

use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use App\Http\Controllers\Api\BaseController;

class Handler extends ExceptionHandler
{

    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register(): void
    {

        $this->reportable(function (NotFoundHttpException $e) {
            return BaseController::error("Not found", 404);
        })->stop();

        $this->reportable(function (ModelNotFoundException $e) {
            return BaseController::error("Record not found", 404);
        })->stop();

        $this->reportable(function (AuthorizationException $e) {
            return BaseController::error("Forbidden", 403);
        });

        $this->reportable(function (AuthenticationException $e) {
            return BaseController::error("Unauthorized", 401);
        });

        $this->reportable(function (QueryException $e) {
            return BaseController::error("Server error", 500);
        });
    }

}
