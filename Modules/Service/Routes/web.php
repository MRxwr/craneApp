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
    Route::prefix('services')->group(function () {
        Route::get('/index', 'service\index');
        Route::get('/create', 'ServiceController@create')->name('services.create');
        Route::post('/store', 'ServiceController@store')->name('services.store');
        Route::get('/{id}/edit', 'ServiceController@edit')->name('services.edit');
        Route::put('/{id}', 'ServiceController@update')->name('services.update');
    });
});
//ServiceController
