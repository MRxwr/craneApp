<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Livewire\Settings;
use App\Http\Controllers\Login_controller;
use App\Http\Livewire\Languages\Index as LanguagesIndex;
use App\Http\Livewire\Locales\Index as LocalesIndex;
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

Route::get('/', function () {
    // return view('welcome');
    // return view('layouts.main', [
    //     'title' => 'Blank Page | sangcahaya.id'
    // ]);
    return redirect('dashboard');
});

Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/dashboard', [HomeController::class, 'index']);
    Route::get('/settings/languages/index', LanguagesIndex::class)->name('languages.index');
    Route::get('/settings/locales/index', LocalesIndex::class)->name('locales.index');
    Route::get('/settings/index/{rowId}', Settings::class)->name('settings.index');
});
Route::post('/login', [Login_controller::class, 'authenticate']);
Route::get('/login', [Login_controller::class, 'index'])->name('login');
Route::get('logout', function (Request $request) {
    Auth::logout();
    $request->session()->invalidate();
    $request->session()->regenerateToken();
    return redirect('login');
});


