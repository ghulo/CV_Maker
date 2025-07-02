<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CvController;
use App\Http\Controllers\ContactController;
use Illuminate\Support\Facades\DB;

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

// Debug routes (must be before catch-all)
Route::get('/route-test', function () {
    return response()->json([
        'message' => 'Routes are working!',
        'timestamp' => now(),
        'url' => request()->url()
    ]);
});

Route::get('/db-test', function () {
    try {
        $tables = DB::select('SHOW TABLES');
        $tableNames = array_map(function($table) {
            return array_values((array)$table)[0];
        }, $tables);
        
        $requiredTables = ['users', 'cvs', 'work_experiences', 'educations', 'skills', 'interests'];
        $missingTables = array_diff($requiredTables, $tableNames);
        
        return response()->json([
            'database_connected' => true,
            'existing_tables' => $tableNames,
            'missing_tables' => $missingTables,
            'all_tables_exist' => empty($missingTables)
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'database_connected' => false,
            'error' => $e->getMessage()
        ]);
    }
});

// Vue.js SPA Catch-All Route (must be last!)
// This serves the Vue.js application for the root URL and all other non-API routes.
// It ensures that navigating to any front-end page directly will load the app.
Route::get('/{any?}', function () {
    return view('layouts.app');
})->where('any', '.*')->name('spa');