<?php
use Illuminate\Http\Request;
use Modules\AppUser\Http\Livewire\Api\OTPController;
use Modules\AppUser\Http\Livewire\Api\RegisterController;
use Modules\AppUser\Http\Livewire\Api\LoginController;

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
    Route::post('/register/client', [RegisterController::class, 'registerClient']);
    Route::post('/register/driver', [RegisterController::class, 'registerDriver']);
    Route::post('/login', [LoginController::class, 'AppUserLogin']);

    Route::middleware('auth:app_user')->group(function () {
        //Route::post('/profile', [ServiceController::class, 'getServices']);
        //Route::post('/profile/update', [ServiceController::class, 'getServices']);
    });

});
