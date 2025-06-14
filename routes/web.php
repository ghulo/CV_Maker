<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CvController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| Web Routes - Vue.js SPA
|--------------------------------------------------------------------------
|
| Since we're using Vue.js SPA with API routes, we only need:
| 1. Catch-all route to serve Vue.js app
| 2. Server-side routes for file downloads/PDFs
| 3. Contact form (server-side email processing)
|
*/

// Contact Form Submission (Server-side email processing)
Route::post('/contact', [ContactController::class, 'send'])->name('contact.send');

// CV PDF Download (Server-side PDF generation)
Route::middleware(['auth'])->group(function () {
    Route::get('/cv/{cv}/download', [CvController::class, 'download'])->name('cv.download');
    Route::get('/cv/{cv}/pdf', [CvController::class, 'generatePdf'])->name('cv.pdf');
});

// Vue.js SPA Catch-All Route
// This serves the Vue.js application for the root URL and all other non-API routes.
// It ensures that navigating to any front-end page directly will load the app.
Route::get('/{any?}', function () {
    return view('layouts.app');
})->where('any', '.*')->name('spa');