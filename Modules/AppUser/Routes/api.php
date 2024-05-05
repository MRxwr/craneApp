<?php
use Illuminate\Http\Request;
use Modules\AppUser\Http\Livewire\Api\OTPController;
use Modules\AppUser\Http\Livewire\Api\RegisterController;
use Modules\AppUser\Http\Livewire\Api\LoginController;
use Modules\AppUser\Http\Livewire\Api\UserController;

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
    Route::post('/sendotp', [OTPController::class, 'sendOTP']);
    Route::post('/verify-otp', [OTPController::class, 'verifyOTP']);
    Route::post('/register/client', [RegisterController::class, 'registerClient']);
    Route::post('/register/driver', [RegisterController::class, 'registerDriver']);
    Route::post('/login', [LoginController::class, 'AppUserLogin']);
    Route::post('/profile', [UserController::class, 'userProfile']);
    Route::post('/profile/update', [UserController::class, 'updateProfile']);
    Route::post('/profile/settings', [UserController::class, 'getProfileSetting']);
    Route::post('/profile/settings/update', [UserController::class, 'updateProfileSetting']);
});
