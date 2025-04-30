<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\Auth\LoginController;
use Modules\Tenant\Http\Controllers\LocaleController;
use Modules\Tenant\Http\Controllers\HomeController;
use Modules\Tenant\Http\Controllers\IsoSystemController;
use Modules\Tenant\Http\Controllers\IsoSpecificationItemController;
use Modules\Tenant\Http\Controllers\IsoAttachmentController;
use Modules\Tenant\Http\Controllers\DocumentController;
use Modules\Tenant\Http\Controllers\DocumentHistoryController;
use Modules\Tenant\Http\Controllers\ProcedureController;
use Modules\Tenant\Http\Controllers\SampleController;
use Modules\Tenant\Http\Controllers\CountryController;
use Modules\Tenant\Http\Controllers\CategoryController;
use Modules\Tenant\Http\Controllers\SubCategoryController;
use Modules\Tenant\Http\Controllers\TagController;
use Modules\Tenant\Http\Controllers\UserController;
use Modules\Tenant\Http\Controllers\AuditController;
use Modules\Tenant\Http\Controllers\RiskController;
use Modules\Tenant\Http\Controllers\CorrectiveActionController;
use Modules\Tenant\Http\Controllers\TrainingController;
use Modules\Tenant\Http\Controllers\MeetingController;
use Modules\Tenant\Http\Controllers\KpiController;
use Modules\Tenant\Http\Controllers\SignatureController;
use Modules\Tenant\Http\Controllers\IsoPolicyController;
use Modules\Tenant\Http\Controllers\IsoInstructionController;
use Modules\Tenant\Http\Controllers\IsoReferenceController;
use Modules\Tenant\Http\Controllers\SettingController;
use Modules\Tenant\Http\Controllers\ComplaintController;
use Modules\Tenant\Http\Controllers\ComplianceController;
use Modules\Document\Http\Controllers\NotificationController;
use Modules\Tenant\Http\Controllers\DocumentRequestController;

Route::prefix('tenant')->name('tenant.')->middleware(['xss'])->group(function() {
    // Guest routes
    Route::middleware('guest:tenant')->group(function () {
        Route::get('login', [LoginController::class, 'showLoginForm'])->name('login');
        Route::post('login', [LoginController::class, 'login']);
    });

    // Language switcher
    Route::get('locale/{locale}', [LocaleController::class, 'setLocale'])->name('locale');

    // Authenticated routes
    Route::middleware(['auth:tenant', 'XSS','tenant'])->group(function () {
        Route::get('/', [HomeController::class, 'index'])->name('dashboard');
        Route::post('logout', [LoginController::class, 'logout'])->name('logout');
        Route::get('notifications', [NotificationController::class, 'index'])->name('notifications.index');
        
        // Document routes
        Route::prefix('documents')->name('documents.')->middleware(['auth:tenant'])->group(function () {
            Route::get('/', [DocumentController::class, 'index'])->name('index');
            Route::get('/create', [DocumentController::class, 'create'])->name('create');
            Route::post('/', [DocumentController::class, 'store'])->name('store');
            Route::get('/{id}', [DocumentController::class, 'show'])->name('show');
            Route::get('/{id}/edit', [DocumentController::class, 'edit'])->name('edit');
            Route::put('/{id}', [DocumentController::class, 'update'])->name('update');
            Route::delete('/{id}', [DocumentController::class, 'destroy'])->name('destroy');
            
            // Document history routes
            Route::get('/{id}/approval-history', [DocumentHistoryController::class, 'approvalHistory'])->name('approval-history');
            Route::get('/{id}/approval-timeline-data', [DocumentHistoryController::class, 'getApprovalTimelineData'])->name('approval-timeline-data');
        });

        // Notifications
        Route::prefix('notifications')->name('notifications.')->middleware('auth:tenant')->group(function () {
            Route::get('/', [NotificationController::class, 'index'])->name('index');
            Route::get('/get-unread', [NotificationController::class, 'getUnread'])->name('get-unread');
            Route::post('/mark-as-read/{id}', [NotificationController::class, 'markAsRead'])->name('mark-as-read');
            Route::post('/mark-all-as-read', [NotificationController::class, 'markAllAsRead'])->name('mark-all-as-read');
        });
       
    });

});
