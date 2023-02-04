<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShopController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\DiscountController;
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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('register', [UserController::class, 'register']);
Route::post('login', [UserController::class, 'login']);
Route::post('send-forgot-password-otp', [UserController::class, 'sendForgotPasswordOtp']);
Route::post('verify-otp', [UserController::class, 'verifyOtp']);
Route::post('forgot-password', [UserController::class, 'forgotPassword']);

Route::group(['middleware' => ['auth:api']], function () {
 Route::post('update-profile', [UserController::class, 'updateProfile']);
 Route::post('shops',[ShopController::class, 'shops']);
 Route::post('shop-add',[ShopController::class, 'shopAdd']);
 
 /**categories route start */
 Route::group(['prefix' => 'categories'],function(){
    Route::get('/get-list', [CategoryController::class, 'list']);
 });
 /**categories route end */

  /**product route start */
  Route::group(['prefix' => 'product'],function(){
   Route::post('/list', [ProductController::class, 'ProductList']);
   Route::post('/create', [ProductController::class, 'store']);
   Route::post('/update', [ProductController::class, 'update']);
   Route::delete('/{id}', [ProductController::class, 'delete']);
   Route::get('details/{id}', [ProductController::class, 'getDetails']);
   Route::post('/add-to-favourite', [ProductController::class, 'addToFavourite']);
   Route::get('get-favourite-list', [ProductController::class, 'getFavouriteList']);
});
Route::group(['prefix' => 'discount'],function(){
   Route::post('/list', [DiscountController::class, 'discountList']);
   Route::post('/create', [DiscountController::class, 'store']);
   Route::delete('/{id}', [DiscountController::class, 'delete']);
   Route::get('details/{id}', [DiscountController::class, 'getDetails']);
});
/**product route end */

});


