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

Route::prefix('audit')->name('tenant.audit.')->group(function() {
    Route::get('/', 'AuditController@index')->name('index');
    Route::get('/create', 'AuditController@create')->name('create');
    Route::post('/', 'AuditController@store')->name('store');
    Route::get('/{audit}', 'AuditController@show')->name('show');
    Route::get('/{audit}/edit', 'AuditController@edit')->name('edit');
    Route::put('/{audit}', 'AuditController@update')->name('update');
    Route::delete('/{audit}', 'AuditController@destroy')->name('destroy');
});
