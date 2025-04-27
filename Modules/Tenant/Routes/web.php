<?php

use Illuminate\Support\Facades\Route;
use Modules\Tenant\Http\Controllers\Auth\LoginController;
use Modules\Tenant\Http\Controllers\LocaleController;
use Modules\Tenant\Http\Controllers\HomeController;
use Modules\Tenant\Http\Controllers\IsoSystemController;
use Modules\Tenant\Http\Controllers\IsoSpecificationItemController;
use Modules\Tenant\Http\Controllers\IsoAttachmentController;
use Modules\Tenant\Http\Controllers\DocumentController;
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

Route::prefix('tenant')->name('tenant.')->middleware(['xss'])->group(function() {
    // Guest routes
    Route::middleware('guest')->group(function () {
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
        // // Document Management
        // Route::resource('documents', DocumentController::class);
        // Route::get('document/history', [DocumentController::class, 'history'])->name('document.history');
        // Route::get('document/{id}/comment', [DocumentController::class, 'comment'])->name('document.comment');
        // Route::get('document/{id}/version-history', [DocumentController::class, 'versionHistory'])->name('document.version.history');

        // // Audit Management
        // Route::resource('audits', AuditController::class);
        
        // // Risk Management
        // Route::resource('risks', RiskController::class);
        
        // // Corrective Actions
        // Route::resource('corrective-actions', CorrectiveActionController::class);
        
        // // Training Management
        // Route::resource('trainings', TrainingController::class);
        
        // // Meeting Management
        // Route::resource('meetings', MeetingController::class);
        
        // // KPI Management
        // Route::resource('kpis', KpiController::class);
        
        // // Signature and Authority
        // Route::resource('signatures', SignatureController::class);

        // // ISO Systems
        // Route::resource('iso-systems', IsoSystemController::class);
        // Route::get('iso-systems/{id}/procedures', [IsoSystemController::class, 'procedures'])->name('iso-systems.procedures');
        // Route::get('iso-systems/{id}/samples', [IsoSystemController::class, 'samples'])->name('iso-systems.samples');

        // // ISO Policies
        // Route::resource('policies', IsoPolicyController::class);
        // Route::get('policies/{id}/attachments', [IsoPolicyController::class, 'attachments'])->name('policies.attachments');

        // // ISO Instructions
        // Route::resource('instructions', IsoInstructionController::class);
        // Route::get('instructions/{id}/attachments', [IsoInstructionController::class, 'attachments'])->name('instructions.attachments');

        // // ISO References
        // Route::resource('references', IsoReferenceController::class);
        // Route::get('references/{id}/attachments', [IsoReferenceController::class, 'attachments'])->name('references.attachments');

        // // Procedures
        // Route::resource('procedures', ProcedureController::class);
        // Route::get('procedures/{id}/configure', [ProcedureController::class, 'configure'])->name('procedures.configure');
        // Route::post('procedures/{id}/save-config', [ProcedureController::class, 'saveConfig'])->name('procedures.save-config');

        // // Samples
        // Route::resource('samples', SampleController::class);
        // Route::get('samples/{id}/configure', [SampleController::class, 'configure'])->name('samples.configure');
        // Route::post('samples/{id}/save-config', [SampleController::class, 'saveConfig'])->name('samples.save-config');

        // // Complaints Management
        // Route::resource('complaints', ComplaintController::class);
        
        // // Compliance Management
        // Route::resource('compliance', ComplianceController::class);

        // // Settings
        // Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
        // Route::post('settings/general', [SettingController::class, 'updateGeneral'])->name('settings.general');
        // Route::post('settings/email', [SettingController::class, 'updateEmail'])->name('settings.email');
        // Route::post('settings/appearance', [SettingController::class, 'updateAppearance'])->name('settings.appearance');

        // // Users
        // Route::resource('users', UserController::class);
        
        // // Categories
        // Route::resource('categories', CategoryController::class);
        // Route::resource('subcategories', SubCategoryController::class);
        
        // // Tags
        // Route::resource('tags', TagController::class);

        // // Countries and Cities
        // Route::resource('countries', CountryController::class);
        // Route::get('countries/{country}/cities', [CountryController::class, 'cities'])->name('countries.cities');

        // // File Manager
        // Route::get('file-manager', [FileManagerController::class, 'index'])->name('file-manager.index');
        // Route::get('file-manager/config', [FileManagerController::class, 'getConfig'])->name('file-manager.config');
        // Route::post('file-manager/upload', [FileManagerController::class, 'upload'])->name('file-manager.upload');
    });

});
