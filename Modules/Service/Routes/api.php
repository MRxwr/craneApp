<?php

use Illuminate\Http\Request;
use Modules\Service\Http\Livewire\Api\ServiceController;
/*Modules\Service\Http\Livewire\Api
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
    Route::get('/services', [ServiceController::class, 'getServices']);
});