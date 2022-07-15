<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UrlShorterController;
use App\Http\Controllers\Api\Auth\AuthController;

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
    Route::get("/show/{url}", [UrlShorterController::class, "show"])->name("api.show");
    Route::get("/test", [UrlShorterController::class, "test"])->name("api.test");
    Route::get("/click", [UrlShorterController::class,"clickTest"])->name("api.click");


    Route::group(["middleware" => "auth:sanctum"], function () {
        Route::get("/logout", [AuthController::class, "logout"])->name("api.logout");
        Route::resource("/url", UrlShorterController::class)->except(["show"]);
        Route::post("/url/search", [UrlShorterController::class, "search"])->name("api.search");
        Route::get("/url/header", [UrlShorterController::class, "header"])->name("api.header");
        Route::post("/url/find/", [UrlShorterController::class, "find"])->name("api.find");

    });


//    Route::post('/register',[\App\Http\Controllers\AuthController::class,"register"])->name("api.register");
//    Route::get("/index",[\App\Http\Controllers\UrlShorterController::class,"index"])->name("api.index");
//    Route::post("/store",[\App\Http\Controllers\UrlShorterController::class,"store"])->name("api.store");
//    Route::get("/show/{url?}",[\App\Http\Controllers\UrlShorterController::class,"show"])->name("api.show");
});






