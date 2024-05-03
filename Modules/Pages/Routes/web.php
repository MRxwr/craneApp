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
    Route::prefix('pages')->group(function () {
        Route::get('/index', 'pages\index');
        Route::get('/create', 'PagesController@create')->name('pages.create');
        Route::post('/store', 'PagesController@store')->name('pages.store');
        Route::get('/{id}/edit', 'PagesController@edit')->name('pages.edit');
        Route::put('/{id}', 'PagesController@update')->name('pages.update');
    });
});