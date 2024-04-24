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
    Route::prefix('users')->group(function () {
        Route::get('clients', 'users\clients');
        Route::get('drivers', 'users\drivers');
        Route::get('/appuser/create', 'UsersController@create')->name('appuser.create');
        Route::post('/appuser/store', 'UsersController@store')->name('appuser.store');
        Route::get('/appuser/{id}/edit', 'UsersController@edit')->name('appuser.edit');
        Route::put('/appuser/{id}', 'UsersController@update')->name('appuser.update');
    });
});
