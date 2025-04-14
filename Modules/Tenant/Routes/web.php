<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\Auth\LoginController;
use Modules\Tenant\Http\Controllers\LocaleController;
use Modules\Tenant\Http\Controllers\TenantController;

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

Route::prefix('tenant')->name('tenant.')->group(function() {
    // Guest routes
    Route::middleware('guest')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);

        // Language switcher
        Route::get('locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale');
    });

    // Authenticated routes
    Route::middleware(['auth', 'tenant'])->group(function () {
        Route::get('/', [TenantController::class, 'index'])->name('dashboard');
        Route::post('logout', [LoginController::class, 'destroy'])->name('logout');
    });
});
