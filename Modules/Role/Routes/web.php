<?php

use Illuminate\Support\Facades\Route;


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

Route::prefix('role')->name('tenant.role.')->middleware(['auth:tenant','tenant','XSS'])->group(function() {
    // Roles Management
    Route::resource('roles', 'RoleController');
    
    // Permissions Management
    Route::resource('permissions', 'PermissionController');
    
    // User Management
    Route::resource('users', 'UserController');
    
    // Dashboard
    Route::get('/', 'RoleController@index')->name('dashboard');
});
