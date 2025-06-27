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

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    // Authentication
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // User Profile
    Route::get('/profile', [UserController::class, 'show']);
    Route::put('/profile', [UserController::class, 'update']);
    
    // CV Management
    Route::get('/my-cvs', [CvController::class, 'userCvs']);
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
    });
});

// CV Preview route (protected - user must own the CV)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/cv-preview/{cv}', [CvPreviewApiController::class, 'show']);
});
