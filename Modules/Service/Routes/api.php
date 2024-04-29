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

// API Version 1 routes
Route::prefix('v1')->group(function () {
    // Route for registering a new user with OTP verification
    //Route::post('/sendotp', [OTPController::class, 'sendOTP']);
  
    Route::middleware('auth:app_user')->group(function () {
        Route::post('/getservice', [ServiceController::class, 'getServices']);
    });
});