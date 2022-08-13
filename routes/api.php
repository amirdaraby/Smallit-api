<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\UrlShorterController;
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
    Route::get("/show/{url}", [UrlShorterController::class, "show"])->name("api.show");


    /*
     *
     * # Url and Short url
     *
     * */

    Route::group(["middleware" => "auth:sanctum"], function () {
        Route::get("/logout", [AuthController::class, "logout"])->name("api.logout");

        Route::group(["prefix" => "/url"], function () {
            Route::resource("/", UrlShorterController::class)->except(["show"]);
            Route::get("/{id}/stats", [UrlShorterController::class, "urlStats"])->name("api.url_stats");
            Route::post("/search", [UrlShorterController::class, "search"])->name("api.search"); // todo change to get method - done
            Route::get("/header", [UrlShorterController::class, "header"])->name("api.header");
            Route::post("/find", [UrlShorterController::class, "find"])->name("api.find"); // todo change to get method - done


        });

        /*
         * # Clicks
         */
        Route::group(["prefix" => "/reach"], function () {

            Route::get("/{url}", [\App\Http\Controllers\Api\ClickController::class, "index"])->name("api.reach");
            Route::get("/{url}/browsers/", [\App\Http\Controllers\Api\ClickController::class, "getBrowsers"])->name("api.reach_browsers");
            Route::get("/{url}/platforms/", [\App\Http\Controllers\Api\ClickController::class, "getPlatforms"])->name("api.reach_platforms");
            Route::get("/{url}/count", [\App\Http\Controllers\Api\ClickController::class, "getShorturlWithCount"])->name("api.reach_count");
            Route::get("/{url}/all", [\App\Http\Controllers\Api\ClickController::class, "getAll"])->name("api.reach_all");
            Route::get("/{url}/all/{from}/{to}", [\App\Http\Controllers\Api\ClickController::class, "getByTime"]);


            //            Route::get("/{url}/platforms/{}");
        });

        /*
         * # User
         *
         * */

        Route::group(["prefix" => "/user"], function (){
            Route::get("/clicks/count",[\App\Http\Controllers\Api\UserController::class,"userClicks"])->name("api.user_clicks");
            Route::get("/url/all",[\App\Http\Controllers\Api\UserController::class,"userShortUrls"])->name("api.user_shorturls");
        });

    });


});






