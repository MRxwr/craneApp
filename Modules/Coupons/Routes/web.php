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

Route::middleware(['web', 'auth'])->group(function () {
    Route::prefix('coupons')->group(function () {
        Route::get('/index', 'coupon\index');
        Route::get('/create', 'CouponsController@create')->name('coupons.create');
        Route::post('/store', 'CouponsController@store')->name('coupons.store');
        Route::get('/{id}/edit', 'CouponsController@edit')->name('coupons.edit');
        Route::put('/{id}', 'CouponsController@update')->name('coupons.update');
    });
});