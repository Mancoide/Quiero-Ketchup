<?php

use App\Http\Controllers\Api\FileUploadController;
use App\Http\Controllers\Api\ReconciliationController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/**
 * Health check endpoint
 */
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'service' => 'Contabot API',
        'version' => '1.0.0',
        'timestamp' => now(),
    ]);
});

/**
 * File Upload Endpoints
 */
Route::prefix('files')->group(function () {
    Route::post('upload-bank-statement', [FileUploadController::class, 'uploadBankStatement']);
    Route::post('upload-reconciliation', [FileUploadController::class, 'uploadReconciliationFile']);
});

/**
 * Reconciliation Endpoints
 */
Route::prefix('reconciliation')->group(function () {
    Route::post('reconcile', [ReconciliationController::class, 'reconcile']);
    Route::get('result/{resultId}', [ReconciliationController::class, 'getResult']);
});
