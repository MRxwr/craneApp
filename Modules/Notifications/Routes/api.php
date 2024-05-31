<?php

use Illuminate\Http\Request;
use Modules\Notifications\Http\Livewire\Api\NotificationController;

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
    Route::post('get_notifications', [NotificationController::class, 'getNotifications']);
    Route::post('save_user_notification', [NotificationController::class, 'saveUserNotification']);
});