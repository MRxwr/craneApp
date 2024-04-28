<?php
use Illuminate\Http\Request;
use Modules\AppUser\Http\Livewire\Api\OTPController;
use Modules\AppUser\Http\Livewire\Api\RegisterController;

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
    Route::post('/register/client', [OTPController::class, 'verifyOTP']);
   
    Route::middleware('auth:app_user')->get('/profile', function (Request $request) {
        return $request->user();
    });
});
