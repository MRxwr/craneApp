<?php

use Illuminate\Http\Request;
use Modules\BookingRequest\Http\Livewire\Api\BookingController;
use Modules\BookingRequest\Http\Livewire\Api\UserBookingController;

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
    Route::post('sent_order_request', [BookingController::class, 'sendRequest']);
    Route::post('get_driver_list',[BookingController::class, 'getDriverListRequest']);
    Route::post('place_order_request', [BookingController::class, 'placeOrderRequest']);
    Route::post('change_order_status', [BookingController::class, 'changeOrderStatus']);
    Route::post('save_order_rating', [BookingController::class, 'saveOrderRating']);
    Route::post('cancel_order', [BookingController::class, 'cancelTheOrder']);
    Route::post('client_home', [UserBookingController::class, 'GetClientHome']);
    Route::post('order_details', [BookingController::class, 'getOderDetails']);
    // Driver
    Route::post('get_order_request', [BookingController::class, 'getOrdersRequest']);
    Route::post('save_order_request', [BookingController::class, 'saveOrderRequest']);
    Route::post('driver_home', [UserBookingController::class, 'GetDriverHome']);
    Route::post('get_driver_history', [UserBookingController::class, 'GetDriverHistories']);
    Route::post('order_start_end', [BookingController::class, 'saveOrderStartEnd']);
    Route::post('save_driver_location', [BookingController::class, 'TrackDriverPosition']);
});