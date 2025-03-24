<?php

use Illuminate\Http\Request;

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
Route::post('login', 'App\Http\Controllers\Api\v1\AuthController@login');

Route::group(['namespace' => 'App\Api\v1\Controllers'], function () {
    Route::group(['middleware' => 'auth:api'], function () {
        Route::get('users', ['uses' => 'UserController@index']);
    });
});
9

use App\Http\Controllers\Api\v1\DashboardApiController;
Route::get('dashboard', [DashboardApiController::class, 'getDashboardData']);
Route::get('paymentsPerMonth', [DashboardApiController::class, 'getPaymentsPerMonth']);
Route::get('paymentsByOffer', [DashboardApiController::class, 'getPaymentsByOffer']);
Route::get('paymentsByTask', [DashboardApiController::class, 'getPaymentsByTask']);
Route::get('paymentsByLead', [DashboardApiController::class, 'getPaymentsByLead']);

use App\Http\Controllers\Api\v1\TestController;
Route::get('test', [TestController::class, 'testApi']);
