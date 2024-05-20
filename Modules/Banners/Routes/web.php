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
    Route::prefix('banners')->group(function () {
        Route::get('/index', 'banners\index');
        Route::get('/create', 'BannersController@create')->name('banners.create');
        Route::post('/store', 'BannersController@store')->name('banners.store');
        Route::get('/{id}/edit', 'BannersController@edit')->name('banners.edit');
        Route::put('/{id}', 'BannersController@update')->name('banners.update');
    });
});
