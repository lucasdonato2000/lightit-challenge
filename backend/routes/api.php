<?php

use App\Http\Controllers\PatientController;
use App\Http\Controllers\DocumentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;



Route::get('/health', function () {
    return response()->json([
        'status' => 'healthy',
        'timestamp' => now()->toIso8601String(),
        'service' => 'Patient Registration API',
    ]);
});

Route::prefix('patients')->group(function () {
    Route::get('/', [PatientController::class, 'index']);
    Route::post('/', [PatientController::class, 'store']);
    Route::get('/{id}', [PatientController::class, 'show']);
});

Route::get('/documents/{path}', [DocumentController::class, 'show'])
    ->where('path', '.*')
    ->name('documents.show');
