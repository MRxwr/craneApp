<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Livewire\Settings;
use App\Http\Controllers\Login_controller;
use App\Http\Livewire\Languages\Index as LanguagesIndex;
use App\Http\Livewire\Locales\Index as LocalesIndex;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Session;

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
    return redirect('dashboard');
});
Route::get('/language/{locale}', function ($locale) {
     // Set the application locale
     App::setLocale($locale);
     // Optionally, store the selected locale in the session
     Session::put('locale', $locale);
    return redirect()->back();
});


Route::get('/create-storage-link', function () {
    try {
        Artisan::call('storage:link');
        return 'Storage link created successfully!';
    } catch (\Exception $e) {
        return 'Error creating storage link: ' . $e->getMessage();
    }
});
Route::get('/migrate', function () {
    Artisan::call('migrate');
    return 'Migration completed';
});
Route::get('/install', function () {
    Artisan::call('passport:install');
    Artisan::call('config:cache');
    return 'Migration completed';
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


