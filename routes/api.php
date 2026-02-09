<?php

use App\Http\Controllers\Api\V1\DocumentController;
use App\Http\Controllers\Api\V1\DocumentFileController;
use App\Http\Controllers\Api\V1\EndpointController;
use App\Http\Controllers\Api\V1\FilterController;
use App\Http\Controllers\Api\V1\ReportsController;
use App\Http\Controllers\Api\V1\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/search', SearchController::class);
    Route::get('/endpoints/{publicId}', [EndpointController::class, 'show'])->whereUlid('publicId');
    Route::get('/filters', FilterController::class);
    Route::get('/reports/summary', [ReportsController::class, 'summary']);
    Route::post('/reports/generate-pdf', [ReportsController::class, 'generatePdf']);
    Route::get('/documents', [DocumentController::class, 'index'])->name('api.documents.index');
    Route::get('/documents/{document}', [DocumentController::class, 'show'])->name('api.documents.show');

    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/documents', [DocumentController::class, 'store'])->name('api.documents.store');
        Route::delete('/documents/{document}', [DocumentController::class, 'destroy'])->name('api.documents.destroy');
        Route::get('/documents/{document}/file', DocumentFileController::class)->name('api.documents.file');
    });
});
