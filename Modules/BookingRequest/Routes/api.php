<?php

use Illuminate\Http\Request;
use Modules\BookingRequest\Http\Livewire\Api\BookingController;

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

// API Version 1 routes
Route::prefix('v1')->group(function () {
    //Client
    Route::post('/sent/order/request', [BookingController::class, 'sendRequest']);
    Route::post('get_driver_list', function () {
        echo 'sdasdasdasd';
    });
    Route::post('/place/order/request', [BookingController::class, 'placeOrderRequest']);
    
    Route::post('/change/order/status', [BookingController::class, 'saveDriverRequest']);

    // Driver
    Route::post('/get/order/request', [BookingController::class, 'getOrdersRequest']);
    Route::post('/save/order/request', [BookingController::class, 'saveOrderRequest']);
});