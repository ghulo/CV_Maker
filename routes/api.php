<?php
// routes/api.php (Add this block)

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\CvController;
use App\Http\Controllers\Api\CvTemplateController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\WorkExperienceController;
use App\Http\Controllers\Api\EducationController;
use App\Http\Controllers\Api\SkillController;
use App\Http\Controllers\Api\InterestController;
use App\Http\Controllers\CvPreviewApiController;
use App\Http\Controllers\Api\AIController;
use App\Http\Controllers\ContactController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::get('/templates', [CvTemplateController::class, 'index']);
Route::get('/templates/{template}', [CvTemplateController::class, 'show']);

// Contact form (public route)
Route::post('/contact', [ContactController::class, 'sendApi']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User Profile Management
    Route::get('/user/profile', [UserController::class, 'show']);
    Route::put('/user/profile', [UserController::class, 'update']);
    Route::put('/user/password', [UserController::class, 'updatePassword']);
    Route::post('/user/avatar', [UserController::class, 'uploadAvatar']);
    Route::get('/user/cvs', [UserController::class, 'getUserCvs']);
    Route::get('/user/stats', [UserController::class, 'getUserStats']);
    Route::get('/user/activity', [UserController::class, 'getUserActivity']);
    Route::get('/user/export', [UserController::class, 'exportData']);
    Route::get('/user/cvs/download-all', [UserController::class, 'downloadAllCvs']);
    Route::delete('/user', [UserController::class, 'destroy']);
    
    // Legacy profile routes (keeping for backwards compatibility)
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    
    // CV Management
    Route::get('/my-cvs', [CvController::class, 'userCvs']);
    Route::post('/cvs/debug', [CvController::class, 'debugStore']); // Debug endpoint
    Route::apiResource('cvs', CvController::class);
    Route::post('/cvs/{cv}/duplicate', [CvController::class, 'duplicate']);
    Route::get('/cvs/{cv}/download', [CvController::class, 'download']);
    
    // CV Components
    Route::apiResource('cvs.work-experiences', WorkExperienceController::class);
    Route::apiResource('cvs.educations', EducationController::class);
    Route::apiResource('cvs.skills', SkillController::class);
    Route::apiResource('cvs.interests', InterestController::class);
    
    // AI Features
    Route::prefix('ai')->group(function () {
        Route::post('/skills-suggestions', [AIController::class, 'getSkillSuggestions']);
        Route::post('/generate-summary', [AIController::class, 'generateSummary']);
        Route::post('/experience-suggestions', [AIController::class, 'getExperienceSuggestions']);
        Route::post('/analyze-cv', [AIController::class, 'analyzeCv']);
        Route::post('/interest-suggestions', [AIController::class, 'getInterestSuggestions']);
        Route::post('/chat', [AIController::class, 'chat']);
    });

    // AI endpoints
    Route::post('/ai/cv-analysis', [AIController::class, 'analyzeCv']);
    Route::post('/ai/generate-summary', [AIController::class, 'generateSummary']);
    Route::post('/ai/suggest-skills', [AIController::class, 'suggestSkills']);
    Route::post('/ai/generate-experience', [AIController::class, 'generateExperience']);
    Route::post('/ai/suggest-interests', [AIController::class, 'suggestInterests']);
    Route::post('/ai/chat', [AIController::class, 'chat']);
    Route::get('/ai/test-connection', [AIController::class, 'testConnection']); // New test endpoint
    Route::get('/ai/debug-connection', [AIController::class, 'debugConnection']); // New debug endpoint
    
    // Dashboard AI endpoints
    Route::get('/ai/dashboard-analysis', [AIController::class, 'getDashboardAnalysis']);
    Route::get('/ai/trending-skills', [AIController::class, 'getDashboardTrendingSkills']);
    Route::get('/ai/market-insights', [AIController::class, 'getMarketInsights']);
    Route::get('/ai/connection-status', [AIController::class, 'getConnectionStatus']);
});

// CV Preview route (protected - user must own the CV)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cv-preview/{cv}', [CvPreviewApiController::class, 'show']);
});
