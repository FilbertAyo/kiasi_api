<?php

use App\Http\Controllers\Api\V1\AuthController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ConfigController;
use App\Http\Controllers\Api\V1\ContentController;
use App\Http\Controllers\Api\V1\DeviceController;
use App\Http\Controllers\Api\V1\SettingsController;
use App\Http\Controllers\Api\V1\StatisticsController;
use App\Http\Controllers\Api\V1\SummaryController;
use App\Http\Controllers\Api\V1\SupportController;
use App\Http\Controllers\Api\V1\TransactionController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| All API routes are prefixed with /api by default.
| We add v1 prefix for versioning.
|
*/

Route::prefix('v1')->group(function () {
    // ===========================
    // PUBLIC ROUTES (No Auth)
    // ===========================
    
    // Authentication
    Route::prefix('auth')->group(function () {
        // Registration & Login
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);

        // Email check (for registration validation)
        Route::post('/check-email', [AuthController::class, 'checkEmail']);

        // Password Reset Flow
        Route::post('/forgot-password', [AuthController::class, 'forgotPassword']);
        Route::post('/verify-reset-code', [AuthController::class, 'verifyResetCode']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // App Configuration (Public)
    Route::get('/config', [ConfigController::class, 'index']);
    Route::get('/config/version', [ConfigController::class, 'checkVersion']);
    Route::get('/config/status', [ConfigController::class, 'status']);

    // Static Content (Public)
    Route::prefix('content')->group(function () {
        Route::get('/terms', [ContentController::class, 'terms']);
        Route::get('/privacy', [ContentController::class, 'privacy']);
        Route::get('/about', [ContentController::class, 'about']);
        Route::get('/faq', [ContentController::class, 'faq']);
    });

    // Support - Contact (Public - can be used before login)
    Route::post('/support/contact', [SupportController::class, 'contactSupport']);

    // ===========================
    // PROTECTED ROUTES (Auth Required)
    // ===========================
    Route::middleware('auth:sanctum')->group(function () {
        // Auth routes
        Route::prefix('auth')->group(function () {
            // Session management
            Route::post('/logout', [AuthController::class, 'logout']);
            Route::post('/logout-all', [AuthController::class, 'logoutAll']);

            // Profile
            Route::get('/user', [AuthController::class, 'user']);
            Route::put('/user', [AuthController::class, 'update']);

            // Password
            Route::post('/change-password', [AuthController::class, 'changePassword']);

            // Account deletion
            Route::delete('/delete-account', [AuthController::class, 'deleteAccount']);
        });

        // Transactions CRUD
        Route::apiResource('transactions', TransactionController::class);
        Route::post('transactions/{id}/restore', [TransactionController::class, 'restore'])
            ->name('transactions.restore');

        // Summary endpoints
        Route::prefix('summary')->group(function () {
            Route::get('/daily', [SummaryController::class, 'daily']);
            Route::get('/weekly', [SummaryController::class, 'weekly']);
            Route::get('/monthly', [SummaryController::class, 'monthly']);
            Route::get('/range', [SummaryController::class, 'range']);
            Route::get('/category', [SummaryController::class, 'category']);
        });

        // Statistics endpoints
        Route::prefix('statistics')->group(function () {
            Route::get('/overview', [StatisticsController::class, 'overview']);
        });

        // Categories
        Route::get('/categories', [CategoryController::class, 'index']);

        // Settings & Preferences
        Route::prefix('settings')->group(function () {
            // User Preferences
            Route::get('/preferences', [SettingsController::class, 'getPreferences']);
            Route::put('/preferences', [SettingsController::class, 'updatePreferences']);
            
            // Notification Settings
            Route::put('/notifications', [SettingsController::class, 'updateNotifications']);
            
            // Privacy/Security Settings
            Route::put('/privacy', [SettingsController::class, 'updatePrivacy']);
            Route::post('/verify-pin', [SettingsController::class, 'verifyPin']);
            
            // Data Export
            Route::post('/export-data', [SettingsController::class, 'exportData']);
            Route::get('/export-history', [SettingsController::class, 'exportHistory']);
            
            // App Rating
            Route::get('/should-rate', [SettingsController::class, 'shouldRate']);
            Route::post('/rate-app', [SettingsController::class, 'rateApp']);
        });

        // FAQ Helpful (requires auth to prevent abuse)
        Route::post('/content/faq/{id}/helpful', [ContentController::class, 'markFaqHelpful']);

        // Support - Feedback (requires auth)
        Route::post('/support/feedback', [SupportController::class, 'submitFeedback']);

        // Device Token Management (for Push Notifications)
        Route::prefix('device')->group(function () {
            Route::post('/register', [DeviceController::class, 'register']);
            Route::delete('/unregister', [DeviceController::class, 'unregister']);
            Route::get('/list', [DeviceController::class, 'list']);
        });
    });
});
