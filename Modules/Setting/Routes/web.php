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

use Illuminate\Support\Facades\Route;
use Modules\Setting\Http\Controllers\SettingController;
use Modules\Setting\Http\Controllers\CompanyProfileController;
use Modules\Setting\Http\Controllers\OrganizationController;
use Modules\Setting\Http\Controllers\ConsultantController;
use Modules\Setting\Http\Controllers\BackupController;
use Modules\Tenant\Http\Middleware\TenantMiddleware;
use Modules\Tenant\Http\Middleware\XSSMiddleware;



Route::prefix('setting')->name('tenant.setting.')->middleware(['auth:tenant', 'XSS','tenant'])->group(function() {
    // Company Profile
    Route::get('/', [CompanyProfileController::class, 'index'])->name('index');
    Route::post('/', [CompanyProfileController::class, 'store'])->name('store');
    Route::put('/', [CompanyProfileController::class, 'update'])->name('update');

    // Organization Structure
    Route::prefix('organization')->name('organization.')->group(function() {
        Route::get('/', [OrganizationController::class, 'index'])->name('index');
        Route::get('/chart', [OrganizationController::class, 'getOrganizationChart'])->name('chart');
        Route::delete('/{type}/{id}', [OrganizationController::class, 'destroy'])->name('destroy');
        
        // Departments
        Route::post('/departments', [OrganizationController::class, 'storeDepartment'])->name('departments.store');
        Route::put('/departments/{id}', [OrganizationController::class, 'updateDepartment'])->name('departments.update');
        Route::delete('/departments/{id}', [OrganizationController::class, 'destroyDepartment'])->name('departments.destroy');
        Route::get('/departments/{id}', [OrganizationController::class, 'showDepartment'])->name('departments.show');
        
        // Positions
        Route::post('/positions', [OrganizationController::class, 'storePosition'])->name('positions.store');
        Route::put('/positions/{id}', [OrganizationController::class, 'updatePosition'])->name('positions.update');
        Route::delete('/positions/{id}', [OrganizationController::class, 'destroyPosition'])->name('positions.destroy');
        Route::get('/positions/{id}', [OrganizationController::class, 'showPosition'])->name('positions.show');
        
        // Employees
        Route::post('/employees', [OrganizationController::class, 'storeEmployee'])->name('employees.store');
        Route::put('/employees/{id}', [OrganizationController::class, 'updateEmployee'])->name('employees.update');
        Route::delete('/employees/{id}', [OrganizationController::class, 'destroyEmployee'])->name('employees.destroy');
        Route::get('/employees/{id}', [OrganizationController::class, 'showEmployee'])->name('employees.show');
    });

    // Consultants
    Route::get('/consultants/all', [ConsultantController::class, 'index'])->name('consultants');
    Route::post('/consultants', [ConsultantController::class, 'store'])->name('consultants.store');
    Route::put('/consultants/{id}', [ConsultantController::class, 'update'])->name('consultants.update');
    Route::delete('/consultants/{id}', [ConsultantController::class, 'destroy'])->name('consultants.destroy');

    // Backup
    Route::get('/backup', [BackupController::class, 'index'])->name('backup');
    Route::post('/backup/create', [BackupController::class, 'create'])->name('backup.create');
    Route::get('/backup/download/{filename}', [BackupController::class, 'download'])->name('backup.download');
    Route::delete('/backup/delete/{filename}', [BackupController::class, 'delete'])->name('backup.delete');
});
