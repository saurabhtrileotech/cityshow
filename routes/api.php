<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ShopController;


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
 
});