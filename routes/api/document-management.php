<?php

use App\Http\Controllers\DocumentManagement\DocumentController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth:sanctum'])->group(function () {
    Route::prefix('documents')->group(function () {
        Route::post('/', [DocumentController::class, 'create']);
        Route::put('/{id}', [DocumentController::class, 'update']);
        Route::get('/{id}/preview', [DocumentController::class, 'preview']);
        Route::post('/{version}/submit-for-approval', [DocumentController::class, 'submitForApproval']);
        Route::post('/{version}/approve', [DocumentController::class, 'approve']);
        Route::post('/{document}/archive', [DocumentController::class, 'archive']);
    });
});
