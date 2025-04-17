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

use Modules\Document\Http\Controllers\DocumentController;
use Modules\Document\Http\Controllers\FileManagerController;
use Modules\Tenant\Http\Middleware\TenantMiddleware;
use Modules\Tenant\Http\Middleware\XSSMiddleware;

Route::prefix('document')->name('tenant.document.')->middleware(['auth:tenant','XSS', 'tenant'])->group(function() {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::get('/create', [DocumentController::class, 'create'])->name('create');
    Route::get('/templates', [DocumentController::class, 'getTemplates'])->name('templates');
    Route::get('/list', [DocumentController::class, 'list'])->name('list');
    Route::get('/template/data/{templateId}', [DocumentController::class, 'getTemplateData'])->name('template.data');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    Route::get('/{document}/versions', [DocumentController::class, 'versions'])->name('versions');
    Route::get('/{document}/versions/{version}', [DocumentController::class, 'version'])->name('version');
    Route::get('/{document}/preview', [DocumentController::class, 'preview'])->name('preview');
    Route::get('/{document}/download', [DocumentController::class, 'download'])->name('download');
                
            // File Manager Routes
    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::get('/file-manager/config', [FileManagerController::class, 'getConfig'])->name('file-manager.config');
    Route::post('/file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');

});
