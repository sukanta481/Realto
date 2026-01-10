<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DashboardController;
use App\Http\Controllers\Api\LeadController;
use App\Http\Controllers\Api\PropertyController;
use App\Http\Controllers\Api\FollowUpController;
use App\Http\Controllers\Api\ClientController;
use App\Http\Controllers\Api\DealController;
use App\Http\Controllers\Api\SearchController;
use App\Http\Controllers\Api\ExportController;
use App\Http\Controllers\Api\ReportController;
use App\Http\Controllers\Api\DeployController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Real Estate CRM API Routes
| All routes are prefixed with /api
|
*/

// Public routes (no authentication required)
Route::prefix('auth')->group(function () {
    Route::post('/register', [AuthController::class, 'register']);
    Route::post('/login', [AuthController::class, 'login']);
});

// Deployment webhook (GitHub)
Route::post('/deploy', [DeployController::class, 'deploy']);

// Protected routes (authentication required)
Route::middleware(['auth:sanctum', 'company'])->group(function () {
    
    // Auth
    Route::prefix('auth')->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);
        Route::put('/profile', [AuthController::class, 'updateProfile']);
        Route::put('/password', [AuthController::class, 'changePassword']);
        Route::post('/onboarding', [AuthController::class, 'completeOnboarding']);
    });

    // Global Search
    Route::get('/search', [SearchController::class, 'search']);

    // Dashboard
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/dashboard/quick-stats', [DashboardController::class, 'quickStats']);

    // Leads
    Route::prefix('leads')->group(function () {
        Route::get('/', [LeadController::class, 'index']);
        Route::get('/kanban', [LeadController::class, 'kanban']);
        Route::get('/sources', [LeadController::class, 'sources']);
        Route::post('/', [LeadController::class, 'store']);
        Route::get('/{id}', [LeadController::class, 'show']);
        Route::put('/{id}', [LeadController::class, 'update']);
        Route::patch('/{id}/status', [LeadController::class, 'updateStatus']);
        Route::post('/{id}/convert', [LeadController::class, 'convertToClient']);
        Route::delete('/{id}', [LeadController::class, 'destroy']);
    });

    // Properties
    Route::prefix('properties')->group(function () {
        Route::get('/', [PropertyController::class, 'index']);
        Route::get('/stats', [PropertyController::class, 'stats']);
        Route::get('/types', [PropertyController::class, 'types']);
        Route::post('/', [PropertyController::class, 'store']);
        Route::get('/{id}', [PropertyController::class, 'show']);
        Route::put('/{id}', [PropertyController::class, 'update']);
        Route::patch('/{id}/status', [PropertyController::class, 'updateStatus']);
        Route::post('/{id}/images', [PropertyController::class, 'uploadImages']);
        Route::delete('/{id}/images/{imageId}', [PropertyController::class, 'deleteImage']);
        Route::patch('/{id}/images/{imageId}/primary', [PropertyController::class, 'setPrimaryImage']);
        Route::delete('/{id}', [PropertyController::class, 'destroy']);
    });

    // Follow-ups
    Route::prefix('follow-ups')->group(function () {
        Route::get('/', [FollowUpController::class, 'index']);
        Route::get('/calendar', [FollowUpController::class, 'calendar']);
        Route::get('/today', [FollowUpController::class, 'today']);
        Route::get('/overdue', [FollowUpController::class, 'overdue']);
        Route::post('/', [FollowUpController::class, 'store']);
        Route::get('/{id}', [FollowUpController::class, 'show']);
        Route::put('/{id}', [FollowUpController::class, 'update']);
        Route::patch('/{id}/complete', [FollowUpController::class, 'complete']);
        Route::patch('/{id}/reschedule', [FollowUpController::class, 'reschedule']);
        Route::patch('/{id}/cancel', [FollowUpController::class, 'cancel']);
        Route::delete('/{id}', [FollowUpController::class, 'destroy']);
    });

    // Clients
    Route::prefix('clients')->group(function () {
        Route::get('/', [ClientController::class, 'index']);
        Route::post('/', [ClientController::class, 'store']);
        Route::get('/{id}', [ClientController::class, 'show']);
        Route::put('/{id}', [ClientController::class, 'update']);
        Route::delete('/{id}', [ClientController::class, 'destroy']);
    });

    // Deals
    Route::prefix('deals')->group(function () {
        Route::get('/', [DealController::class, 'index']);
        Route::get('/pipeline', [DealController::class, 'pipeline']);
        Route::get('/stats', [DealController::class, 'stats']);
        Route::post('/', [DealController::class, 'store']);
        Route::get('/{id}', [DealController::class, 'show']);
        Route::put('/{id}', [DealController::class, 'update']);
        Route::patch('/{id}/stage', [DealController::class, 'updateStage']);
        Route::patch('/{id}/payment', [DealController::class, 'updatePayment']);
        Route::delete('/{id}', [DealController::class, 'destroy']);
    });

    // Exports
    Route::prefix('export')->group(function () {
        Route::get('/leads', [ExportController::class, 'exportLeads']);
        Route::get('/properties', [ExportController::class, 'exportProperties']);
        Route::get('/deals', [ExportController::class, 'exportDeals']);
        Route::get('/monthly-summary', [ExportController::class, 'exportMonthlySummary']);
    });

    // Reports & Analytics
    Route::prefix('reports')->group(function () {
        Route::get('/lead-analytics', [ReportController::class, 'leadAnalytics']);
        Route::get('/revenue-analytics', [ReportController::class, 'revenueAnalytics']);
        Route::get('/property-analytics', [ReportController::class, 'propertyAnalytics']);
        Route::get('/team-performance', [ReportController::class, 'teamPerformance']);
    });

    // Content Management (CMS)
    Route::prefix('content')->group(function () {
        Route::get('/', [\App\Http\Controllers\Api\PageContentController::class, 'index']);
        Route::get('/pages', [\App\Http\Controllers\Api\PageContentController::class, 'getPages']);
        Route::get('/{pageKey}', [\App\Http\Controllers\Api\PageContentController::class, 'getPage']);
        Route::put('/{pageKey}/{sectionKey}', [\App\Http\Controllers\Api\PageContentController::class, 'update']);
        Route::post('/{pageKey}/bulk', [\App\Http\Controllers\Api\PageContentController::class, 'bulkUpdate']);
        Route::post('/upload-image', [\App\Http\Controllers\Api\PageContentController::class, 'uploadImage']);
        Route::delete('/{id}', [\App\Http\Controllers\Api\PageContentController::class, 'destroy']);
    });

    // Property publish toggle
    Route::patch('/properties/{id}/publish', [PropertyController::class, 'togglePublish']);
});

// Public API routes (no authentication required)
Route::prefix('public')->group(function () {
    Route::get('/properties', [\App\Http\Controllers\Api\PublicController::class, 'getProperties']);
    Route::get('/properties/featured', [\App\Http\Controllers\Api\PublicController::class, 'getFeaturedProperties']);
    Route::get('/properties/{id}', [\App\Http\Controllers\Api\PublicController::class, 'getProperty']);
    Route::get('/content/{pageKey}', [\App\Http\Controllers\Api\PublicController::class, 'getPageContent']);
    Route::get('/stats', [\App\Http\Controllers\Api\PublicController::class, 'getStats']);
});

