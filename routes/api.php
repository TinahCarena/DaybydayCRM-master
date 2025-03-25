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

use App\Http\Controllers\Api\v1\DashboardApiController;
use App\Http\Controllers\Api\v1\DetailApiController;

Route::get('dashboard', [DashboardApiController::class, 'getDashboardData']);
Route::get('paymentsPerMonth', [DashboardApiController::class, 'getPaymentsPerMonth']);
Route::get('paymentsByOffer', [DashboardApiController::class, 'getPaymentsByOffer']);
Route::get('paymentsByTask', [DashboardApiController::class, 'getPaymentsByTask']);
Route::get('paymentsByLead', [DashboardApiController::class, 'getPaymentsByLead']);
Route::get('getAllPayment', [DashboardApiController::class, 'getAllPayment']);
Route::get('/payment/{id}', [DashboardApiController::class, 'getPaymentById']);
Route::post('/updatePayment', [DashboardApiController::class, 'updatePayment']);
Route::post('/deletePayment', [DashboardApiController::class, 'deletePayment']);

//route detail total
Route::get('getAllClient', [DetailApiController::class, 'getAllClient']);
Route::get('getAllProject', [DetailApiController::class, 'getAllProject']);
Route::get('getAllTask', [DetailApiController::class, 'getAllTask']);
Route::get('getAllInvoices', [DetailApiController::class, 'getAllInvoices']);


use App\Http\Controllers\DatabaseResetController;
Route::get('/data/reset-data', [DatabaseResetController::class, 'resetSpecificTables'])->name('data.process');


use App\Http\Controllers\Api\v1\TestController;
Route::get('test', [TestController::class, 'testApi']);
