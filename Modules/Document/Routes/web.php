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
use Modules\Document\Http\Controllers\SupportingDocumentController;
use Modules\Document\Http\Controllers\DocumentDatatableController;

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
        Route::post('create', 'create')->name('create');
        Route::post('store', 'store')->name('store');
        Route::get('configure/{id}', 'configure')->name('configure');
        Route::post('configure/{id}/save', 'saveConfigure')->name('saveConfigure');;
    });


    Route::controller(SupportingDocumentController::class)->prefix('supporting-documents')->name('supporting-documents.')->group(function () {
        Route::get('/', 'index')->name('index');
        Route::get('/create', 'create')->name('create');
        Route::post('/', 'store')->name('store');
        Route::get('/download/{id}', 'download')->name('download');
        Route::get('/{id}', 'show')->name('show');
        Route::get('/category/{id}', 'categoryDetail')->name('category-detail');
    });

   

    // Document DataTable Route - MUST be placed BEFORE the catch-all route
    Route::get('/datatable', [DocumentDatatableController::class, 'index'])->name('datatable');

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

    // Document DataTable Route - MUST be placed BEFORE the catch-all route

    // Document Workflow Routes
    Route::get('/workflow/all', [WorkflowController::class, 'index'])->name('workflow.index');
    Route::get('/workflow/data', [WorkflowController::class, 'data'])->name('workflow.data');
    Route::put('/workflow/{document}/status', [WorkflowController::class, 'updateStatus'])->name('workflow.status');

    // Main route group already includes prefix 'document' and name 'tenant.document.'
    Route::group(['prefix' => 'categories', 'as' => 'categories.'], function () {
        Route::get('/', [CategoryController::class, 'index'])->name('index');
        Route::get('/create', [CategoryController::class, 'create'])->name('create');
        Route::post('/', [CategoryController::class, 'store'])->name('store');
        Route::get('/{category}/edit', [CategoryController::class, 'edit'])->name('edit');
        Route::put('/{category}', [CategoryController::class, 'update'])->name('update');
        Route::delete('/{category}', [CategoryController::class, 'destroy'])->name('destroy');
        Route::get('/get-subcategory/{category_id}', [CategoryController::class, 'getSubcategory'])->name('get-subcategory');
        
        // AJAX routes for category management
        Route::post('/ajax-store', [CategoryController::class, 'storeAjax'])->name('ajax-store');
        Route::put('/ajax-update/{id}', [CategoryController::class, 'updateAjax'])->name('ajax-update');
    });

    // Document Versions Routes
    Route::prefix('versions')->name('versions.')->group(function () {
        Route::get('/data', [DocumentVersionController::class, 'data'])->name('data');
        Route::post('/{document}', [DocumentVersionController::class, 'store'])->name('store');
        Route::get('/{version}', [DocumentVersionController::class, 'show'])->name('show');
    });
    
    // // Document Reminders Routes
    // Route::prefix('reminders')->name('reminders.')->group(function () {
    //     Route::get('/', [DocumentReminderController::class, 'index'])->name('index');
    //     Route::get('/create', [DocumentReminderController::class, 'create'])->name('create');
    //     Route::post('/', [DocumentReminderController::class, 'store'])->name('store');
    //     Route::get('/{id}', [DocumentReminderController::class, 'show'])->name('show');
    //     Route::get('/{id}/edit', [DocumentReminderController::class, 'edit'])->name('edit');
    //     Route::put('/{id}', [DocumentReminderController::class, 'update'])->name('update');
    //     Route::delete('/{id}', [DocumentReminderController::class, 'destroy'])->name('destroy');
    //     Route::post('/add-default', [DocumentReminderController::class, 'addDefaultReminder'])->name('add-default');
    //     Route::post('/{id}/toggle-active', [DocumentReminderController::class, 'toggleActive'])->name('toggle-active');
    // });
});
