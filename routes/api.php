<?php

use App\Http\Controllers\AccSettings;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\MarketController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix("auth")->controller(AuthController::class)->group(function () {
    Route::post('login','login');
    Route::post('register','register');

    Route::middleware(['verifyJwt'])->group(function () {
        Route::get('logout','logout');
        Route::get('refresh','refresh');
    });
});
Route::prefix("market")->middleware(['verifyJwt'])->controller(MarketController::class)->group(function () {
    Route::post('createUser','createUser')->middleware('dateFormatter');
//    Route::get('logout','logout');
//    Route::get('refresh','refresh');

});
Route::prefix("acc")->middleware(['verifyJwt'])->controller(AccSettings::class)->group(function () {
    Route::get('getMe','getMe');
    Route::get('getMyTree/{userId}','getChildrenAndGrandchildren');
    Route::put('chrageMyAcc','chrageMyAcc');
    Route::put('transferePoints','transferePoints');

    Route::put('setBucketPassword','setBucketPassword');
    Route::put('chnageBucketPassword','chnageBucketPassword');

    Route::get('getMyTransactions/{option?}','getMyTransactions');




//    Route::get('logout','logout');
//    Route::get('refresh','refresh');

});
