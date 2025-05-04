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
use Modules\Document\Http\Controllers\DocumentVersionController;
use Modules\Document\Http\Controllers\DocumentRequestController;
use Modules\Document\Http\Controllers\WorkflowController;
use Modules\Document\Http\Controllers\HistoryLogController;
use Modules\Tenant\Http\Middleware\TenantMiddleware;
use Modules\Tenant\Http\Middleware\XSSMiddleware;
use Modules\Document\Http\Controllers\CategoryController;
use Modules\Document\Http\Controllers\ProcedureController;

Route::prefix('document')->name('tenant.document.')->middleware(['auth:tenant','XSS', 'tenant'])->group(function() {
    Route::get('/', [DocumentController::class, 'index'])->name('index');
    Route::post('/import-dictionary', [DocumentController::class, 'importFromDictionary'])->name('import-dictionary');
    Route::get('/create', [DocumentController::class, 'create'])->name('create');
    Route::get('/create-livewire', [DocumentController::class, 'createWithLivewire'])->name('create-livewire');
    Route::get('/templates', [DocumentController::class, 'getTemplates'])->name('templates');
    Route::get('/list', [DocumentController::class, 'list'])->name('list');
    Route::get('/template/data/{templateId}', [DocumentController::class, 'getTemplateData'])->name('template.data');
    Route::post('/', [DocumentController::class, 'store'])->name('store');
    
    // Specific routes that need to be defined BEFORE the {document} catch-all route
    Route::controller(ProcedureController::class)->prefix('procedures')->name('procedures.')->group(function () {
        Route::get('main', 'mainProcedures')->name('main');
        Route::get('public', 'publicProcedures')->name('public');
        Route::get('private', 'privateProcedures')->name('private');
        Route::get('all', 'all')->name('all');
        Route::post('save/{id?}', 'save')->name('save');
        Route::get('configure/{id}', 'configure')->name('configure');
        Route::post('configure/{id}/save', 'saveConfigure')->name('saveConfigure');;
        Route::post('status/{id}', 'status')->name('status');
    });

    // Catch-all route for documents - must be placed AFTER specific routes
    Route::get('/{document}', [DocumentController::class, 'show'])->name('show');
    Route::get('/{document}/history', [DocumentController::class, 'history'])->name('history');
    Route::get('/{document}/edit', [DocumentController::class, 'edit'])->name('edit');
    Route::put('/{document}', [DocumentController::class, 'update'])->name('update');
    Route::delete('/{document}', [DocumentController::class, 'destroy'])->name('destroy');
    Route::get('/{document}/versions', [DocumentController::class, 'versions'])->name('versions');
    Route::get('/{document}/versions/{version}', [DocumentController::class, 'version'])->name('version');
    
    // File serving routes
    Route::get('/serve/{id}', [DocumentController::class, 'serveFile'])->name('serve');
                
    // File Manager Routes
    Route::get('/file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
    Route::get('/file-manager/config', [FileManagerController::class, 'getConfig'])->name('file-manager.config');
    Route::post('/file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');

    // Document Requests Routes
    Route::prefix('requests')->name('requests.')->group(function () {
        Route::get('/data', [DocumentRequestController::class, 'index'])->name('index');
        Route::get('/my', [DocumentRequestController::class, 'myRequests'])->name('my');
        Route::get('/create/{document}', [DocumentRequestController::class, 'create'])->name('create');
        Route::post('/', [DocumentRequestController::class, 'store'])->name('store');
        Route::get('/{request}', [DocumentRequestController::class, 'show'])->name('show');
        Route::post('/{request}/status', [DocumentRequestController::class, 'updateStatus'])->name('update-status');
        Route::post('/{id}/approve', [DocumentRequestController::class, 'approve'])->name('approve');
        Route::post('/{id}/reject', [DocumentRequestController::class, 'reject'])->name('reject');
        Route::post('/{id}/request-modification', [DocumentRequestController::class, 'requestModification'])->name('request-modification');
    });

    // Document History Routes
    Route::get('/history/show', [HistoryLogController::class, 'index'])->name('history.index');
    Route::get('/history/data', [HistoryLogController::class, 'data'])->name('history.data');

    // Document Workflow Routes
    Route::get('/workflow/all', [WorkflowController::class, 'index'])->name('workflow.index');
    Route::get('/workflow/data', [WorkflowController::class, 'data'])->name('workflow.data');
    Route::put('/workflow/{document}/status', [WorkflowController::class, 'updateStatus'])->name('workflow.status');

    Route::group(['prefix' => 'category', 'as' => 'category.'], function () {
        Route::get('/see', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}', [CategoryController::class, 'show'])->name('show');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
    });

    // Document Versions Routes
    Route::prefix('versions')->name('versions.')->group(function () {
        Route::get('/data', [DocumentVersionController::class, 'data'])->name('data');
        Route::post('/{document}', [DocumentVersionController::class, 'store'])->name('store');
        Route::get('/{version}', [DocumentVersionController::class, 'show'])->name('show');
    });
});
