<?php

use Illuminate\Http\Request;
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

Route::group(["prefix" => "/v1"],function (){

    Route::resource("/url", \App\Http\Controllers\Api\UrlShorterController::class);



//    Route::post('/register',[\App\Http\Controllers\AuthController::class,"register"])->name("api.register");
//    Route::get("/index",[\App\Http\Controllers\UrlShorterController::class,"index"])->name("api.index");
//    Route::post("/store",[\App\Http\Controllers\UrlShorterController::class,"store"])->name("api.store");
//    Route::get("/show/{url?}",[\App\Http\Controllers\UrlShorterController::class,"show"])->name("api.show");
});
Route::post("/auth/register", [\App\Http\Controllers\AuthController::class,"register"])->name("api.register");
Route::post("/auth/login", [\App\Http\Controllers\AuthController::class,"login"])->name("api.login");

Route::middleware('auth:sanctum')->get("/user", function (Request $request){
    return $request->user();
});



