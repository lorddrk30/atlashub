<?php

use App\Http\Controllers\Api\V1\EndpointController;
use App\Http\Controllers\Api\V1\FilterController;
use App\Http\Controllers\Api\V1\SearchController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->group(function (): void {
    Route::get('/search', SearchController::class);
    Route::get('/endpoints/{id}', [EndpointController::class, 'show'])->whereNumber('id');
    Route::get('/filters', FilterController::class);
});
