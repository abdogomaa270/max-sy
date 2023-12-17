<?php

use App\src\Account\Controllers\AccSettings;
use App\src\Admin\Controllers\AdminController;
use App\src\Auth\Controllers\AuthController;
use App\src\Marketing\Controllers\MarketController;
use App\src\News\Controllers\NewController;
use App\src\Event\Controllers\EventController;
use App\src\Products\Controllers\ProductController;
use App\src\Transaction\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

//tables-models :  user  transactions  allSigned
//controllers :   markting       account
//

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
//---------------------------------------------------------------------------------------------------------------------//
Route::prefix("auth")->controller(AuthController::class)->group(function () {
    Route::post('login','login');
    Route::post('register','register');
    
    Route::middleware(['verifyJwt'])->group(function () {
        Route::put('changePassword','changePassword');
        Route::get('logout','logout');
        Route::get('refresh','refresh');
    });
});
Route::post('auth/createEmployee',[AuthController::class,'createEmployee']);
//--------------------------------------------------------------------------------------------------------------------//
Route::prefix("market")->middleware(['verifyJwt'])->controller(MarketController::class)->group(function () {
    Route::post('createUser','createUser')->middleware('dateFormatter');
    Route::post('updateUser/{userId}','updateUser')->middleware('dateFormatter');
    Route::get('getUsersByWeek/{weekNumber?}','getUsersByWeek');

});
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix("acc")->middleware(['verifyJwt'])->controller(AccSettings::class)->group(function () {
    Route::get('getMe','getMe');
    Route::get('getMyTree/{userId}','getChildrenAndGrandchildren')->middleware('treeAuthority');
    Route::put('chrageMyAcc','chrageMyAcc')->middleware(['isAdmin']);
    Route::post('chrageUserAcc','chrageUserAcc')->middleware(['isAdmin']);
    Route::put('transferePoints','transferePoints');

    Route::put('setBucketPassword','setBucketPassword');
    Route::put('chnageBucketPassword','chnageBucketPassword');

    Route::get('getMyTransactions/{option?}','getMyTransactions');
    Route::get('getMyProfitsTransactions','getMyProfitsTransactions');

    Route::get('getMyProfitsTransactions','getMyProfitsTransactions');

});
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix('admin')->middleware(['verifyJwt','isAuthorized'])->controller(AdminController::class)->group(function (){
    Route::get('getUser/{userId}','getUser');
    Route::get('searchUser/{id}','searchUser');
    Route::put('punishUser/{userId}','punishUser');
    Route::put('unPunishUser/{userId}','unPunishUser');
    Route::get('getPunishedUsers','getPunishedUsers');

    Route::get('checkSiteAvailability','checkSiteAvailability'); 
    Route::put('changeUserPassword/{us  erId}','changeUserPassword');

    Route::put('changeUserBocketPassword/{userId}','changeUserBocketPassword');
    
});
Route::get('admin/allUsers/{flag?}',[AdminController::class,'getAllusers'])->middleware(['verifyJwt','isAdmin']);
Route::get('getUserName/{userId}',[AdminController::class,'getUserName']);
Route::put('admin/openProfitCalculation',[AdminController::class,'calculateTotalProfitsForAllUsers'])->middleware(['verifyJwt','isAdmin']);
Route::put('admin/openSite',[AdminController::class,'openSite'])->middleware(['verifyJwt','isAdmin']);
Route::put('admin/closeSite',[AdminController::class,'closeSite'])->middleware(['verifyJwt','isAdmin']);
Route::get('admin/getBoard',[AdminController::class,'getBoard'])->middleware(['verifyJwt','isAdmin']);
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix('product')->middleware(['verifyJwt','isAuthorized'])->controller(ProductController::class)->group(function (){
    Route::post('/','createProduct');
    Route::delete('/{id}','deleteProduct');
});

Route::get('product',[ProductController::class,'getall'])->middleware(['verifyJwt']);
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix('transactions')->middleware(['verifyJwt','isAdmin'])->controller(TransactionController::class)->group(function (){
    Route::get('/profits/{weekNumber}/{userId?}','getAllProfitTransactions');
    Route::get('/{direction}/{weekNumber?}','getAllTransactions');
});
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix('news')->middleware(['verifyJwt','isAuthorized'])->controller(NewController::class)->group(function (){
    Route::post('/','storeNew');
    Route::get('/','getall');
    Route::delete('/{id}','deleteNew');
});
//----------------------------------------------------------------------------------------------------------------------//
Route::prefix('events')->middleware(['verifyJwt','isAuthorized'])->controller(EventController::class)->group(function (){
    Route::post('/','create');
    Route::get('/','getall');
    Route::delete('/{id}','delete');
});

