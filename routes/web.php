<?php

use App\Http\Controllers\BackofficeEntryController;
use App\Http\Controllers\Api\V1\DocumentFileController;
use App\Http\Controllers\Portal\PortalController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', PortalController::class)->name('portal.home');
Route::get('/reports', PortalController::class)->name('portal.reports');
Route::get('/systems/{systemId}', PortalController::class)->whereNumber('systemId')->name('portal.system');
Route::get('/endpoints/{publicId}', PortalController::class)->whereUlid('publicId')->name('portal.endpoint');
Route::get('/backoffice', BackofficeEntryController::class)->name('backoffice.entry');
Route::get('/backoffice/forbidden', [BackofficeEntryController::class, 'forbidden'])->name('backoffice.forbidden');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/documents/{document}/file', DocumentFileController::class)->name('documents.file');
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
