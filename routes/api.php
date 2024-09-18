<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\HomeController;
use App\Http\Controllers\Api\SettingController;
use App\Http\Controllers\Api\NotificationController;
use App\Http\Controllers\Api\FirebaseAuthController;
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
    Route::post('/home', [HomeController::class, 'getHome']);
    Route::post('/setting', [SettingController::class, 'getSetting']);
    Route::post('/send_notification', [NotificationController::class, 'sendNotification']);
    Route::get('/request_token', [FirebaseAuthController::class, 'getAccessToken']);
    Route::post('/test_notification', [SettingController::class, 'testFirebaseNotification']);
    Route::get('/cron_notification', [SettingController::class, 'CronForCompleteTrip']);
});
