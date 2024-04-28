<?php

use App\Http\Controllers\Auth\RegisterController;
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

// Route::middleware('auth:api')->get('/appuser', function (Request $request) {
//     return $request->user();
// });


// API Version 1 routes
Route::prefix('v1')->group(function () {
    // Route for registering a new user with OTP verification
    Route::post('/sendotp', [OTPController::class, 'sendOTP']);

    // Route for verifying OTP
    Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);
});
