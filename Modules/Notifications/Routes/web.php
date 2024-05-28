<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::prefix('notifications')->group(function() {
    Route::get('/', 'NotificationsController@index');
});
Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('notifications')->group(function () {
        Route::get('notifications', 'notifications\index');
        Route::get('notifications/general', 'notifications\index');
    });
});
