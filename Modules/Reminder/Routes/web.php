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

use Modules\Reminder\Http\Controllers\ReminderController;

Route::prefix('reminder')->name('tenant.reminder.')->middleware(['auth:tenant','web','tenant'])->group(function() {
    Route::get('/', [ReminderController::class, 'index'])->name('index');
    Route::get('/create', [ReminderController::class, 'create'])->name('create');
    Route::post('/', [ReminderController::class, 'store'])->name('store');
    Route::get('/{id}', [ReminderController::class, 'show'])->name('show');
    Route::get('/{id}/edit', [ReminderController::class, 'edit'])->name('edit');
    Route::put('/{id}', [ReminderController::class, 'update'])->name('update');
    Route::delete('/{id}', [ReminderController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle-active', [ReminderController::class, 'toggleActive'])->name('toggle-active');
    Route::get('/my/list', [ReminderController::class, 'myReminders'])->name('my');
    
    // Document-specific reminders
    Route::post('/document-expiry', [ReminderController::class, 'createDocumentExpiryReminder'])->name('document-expiry');
});

// Document module integration
Route::prefix('document/reminders')->name('tenant.document.reminders.')->middleware(['auth:tenant','web','tenant'])->group(function() {
    Route::get('/', [ReminderController::class, 'index'])->name('index')->defaults('filter', 'document_expiry');
    Route::get('/create', [ReminderController::class, 'create'])->name('create')->defaults('type', 'document_expiry');
});
