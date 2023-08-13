<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\ShortUrlController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\BatchController;
use App\Http\Controllers\Api\UrlController;
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
    Route::get("/show/{url}", [ShortUrlController::class, "show"])->name("api.show");

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
            });

            Route::group(["prefix" => "/urls"], function () {
                Route::get("/all", [UrlController::class, "all"])->name("api.urls_all");
                Route::get("/{id}", [UrlController::class, "show"])->name("api.url_show");
                Route::delete("/{id}", [UrlController::class, "delete"])->name("api.user_url_delete");
            });

            Route::group(["prefix" => "/short-urls"], function () {
                Route::post("/batch", [ShortUrlController::class, "store"])->name("api.short_url_create");
            });
        });

        Route::group(["prefix" => "/shorturl"], function () {

        });


        Route::group(["prefix" => "/url"], function () {

            Route::resource("/", ShortUrlController::class)->except(["show"]);
            Route::get("/{id}/stats", [ShortUrlController::class, "urlStats"])->name("api.url_stats");
            Route::post("/search", [ShortUrlController::class, "search"])->name("api.search");
            Route::get("/header", [ShortUrlController::class, "header"])->name("api.header");
            Route::post("/find", [ShortUrlController::class, "find"])->name("api.find");

        });


        Route::group(["prefix" => "/views"], function () {

            Route::get("/{url}", [\App\Http\Controllers\Api\ViewController::class, "index"])->name("api.view");
            Route::get("/{url}/browsers/", [\App\Http\Controllers\Api\ViewController::class, "getBrowsers"])->name("api.view_browsers");
            Route::get("/{url}/platforms/", [\App\Http\Controllers\Api\ViewController::class, "getPlatforms"])->name("api.view_platforms");
            Route::get("/{url}/count", [\App\Http\Controllers\Api\ViewController::class, "getShorturlWithCount"])->name("api.view_count");
            Route::get("/{url}/all", [\App\Http\Controllers\Api\ViewController::class, "getAll"])->name("api.view_all");
            Route::get("/{url}/all/{from}/{to}", [\App\Http\Controllers\Api\ViewController::class, "getByTime"]);

        });

    });


});