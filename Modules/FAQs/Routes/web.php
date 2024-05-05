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
    Route::prefix('faqs')->group(function () {
        Route::get('/index', 'faqs\index');
        Route::get('/create', 'FAQsController@create')->name('faqs.create');
        Route::post('/store', 'FAQsController@store')->name('faqs.store');
        Route::get('/{id}/edit', 'FAQsController@edit')->name('faqs.edit');
        Route::put('/{id}', 'FAQsController@update')->name('faqs.update');
    });
});
