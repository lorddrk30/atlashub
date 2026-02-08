<?php

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
});
