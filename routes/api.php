<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ShortUrlController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\UrlController;
use App\Http\Controllers\Api\ClickController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::group(["prefix" => "/v1"], function () {

    Route::post("/login", [AuthController::class, "login"])->name("api.login");
    Route::post("/register", [AuthController::class, "register"])->name("api.register");
    Route::get("/click/{short_url}", [ClickController::class, "click"])->name("api.click");

    Route::middleware(["auth:sanctum"])->group(function () {

        Route::group(["prefix" => "/user"], function () {

            Route::get("/show", [UserController::class, "show"])->name("api.user_show");
            Route::put("/update", [UserController::class, "update"])->name("api.user_update");
            Route::delete("/logout", [AuthController::class, "logout"])->name("api.logout");
            Route::delete("/delete", [UserController::class, "delete"])->name("api.user_delete");

            Route::group(["prefix" => "/batches"], function () {
                Route::get("/all", [BatchController::class, "all"])->name("api.batches_all");
                Route::get("/{id}", [BatchController::class, "show"])->name("api.batch_show");
                Route::delete("/{id}", [BatchController::class, "delete"])->name("api.batch_delete");
                Route::get("/{id}/short-urls",[BatchController::class, "showShortUrls"])->name("api.batch_short_urls");
            });

            Route::group(["prefix" => "/urls"], function () {
                Route::get("/all", [UrlController::class, "all"])->name("api.urls_all");
                Route::get("/search", [UrlController::class,"search"])->name("api.url_search");
                Route::get("/{id}", [UrlController::class, "show"])->name("api.url_show");
                Route::delete("/{id}", [UrlController::class, "delete"])->name("api.url_delete");
                Route::get("/{id}/short-urls", [UrlController::class, "showShortUrls"])->name("api.url_short_urls");
            });

            Route::group(["prefix" => "/short-urls"], function () {
                Route::get("/all",[ShortUrlController::class, "all"])->name("api.short_urls_all");
                Route::get("/{id}",[ShortUrlController::class, "show"])->name("api.short_url_show");
                Route::post("/batch", [ShortUrlController::class, "store"])->name("api.short_url_create");
            });
        });

    });


});