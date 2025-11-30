<?php

use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\StaticContentController;
use App\Http\Controllers\Admin\TransactionController as AdminTransactionController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;

// Maintenance Routes (for development/testing purposes only)
// Remove or secure before going to production!
Route::get('/maintenance/clear', function () {
    Artisan::call('cache:clear');
    Artisan::call('config:clear');
    Artisan::call('route:clear');
    Artisan::call('view:clear');
    Artisan::call('optimize:clear');
    return 'Application cache, config, route, view and optimize cleared!';
});

Route::get('/maintenance/optimize', function () {
    Artisan::call('optimize');
    return 'Application optimized!';
});

Route::get('/maintenance/config-cache', function () {
    Artisan::call('config:cache');
    return 'Config cache created!';
});

// Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

// Admin Routes
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Admins Management
    Route::get('/admins', [AdminController::class, 'index'])->name('admins.index');
    Route::get('/admins/create', [AdminController::class, 'create'])->name('admins.create');
    Route::post('/admins', [AdminController::class, 'store'])->name('admins.store');
    Route::get('/admins/{admin}/edit', [AdminController::class, 'edit'])->name('admins.edit');
    Route::put('/admins/{admin}', [AdminController::class, 'update'])->name('admins.update');
    Route::post('/admins/{admin}/disable', [AdminController::class, 'disable'])->name('admins.disable');
    Route::post('/admins/{admin}/enable', [AdminController::class, 'enable'])->name('admins.enable');

    // Users Management
    Route::get('/users', [UserController::class, 'index'])->name('users.index');
    Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    Route::post('/users/{user}/block', [UserController::class, 'block'])->name('users.block');
    Route::post('/users/{user}/unblock', [UserController::class, 'unblock'])->name('users.unblock');

    // Transactions
    Route::get('/transactions', [AdminTransactionController::class, 'index'])->name('transactions.index');

    // Categories Management
    Route::resource('categories', AdminCategoryController::class)->except(['show']);

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/transactions', [ReportController::class, 'exportTransactions'])->name('reports.transactions');
    Route::get('/reports/users', [ReportController::class, 'exportUsers'])->name('reports.users');
    Route::get('/reports/monthly', [ReportController::class, 'exportMonthlySummary'])->name('reports.monthly');

    // Static Content Management (Terms, Privacy, About)
    Route::resource('content', StaticContentController::class)->except(['show']);

    // FAQ Management
    Route::get('/faq', [FaqController::class, 'index'])->name('faq.index');
    Route::get('/faq/categories/create', [FaqController::class, 'createCategory'])->name('faq.categories.create');
    Route::post('/faq/categories', [FaqController::class, 'storeCategory'])->name('faq.categories.store');
    Route::get('/faq/categories/{category}', [FaqController::class, 'showCategory'])->name('faq.category');
    Route::get('/faq/categories/{category}/edit', [FaqController::class, 'editCategory'])->name('faq.categories.edit');
    Route::put('/faq/categories/{category}', [FaqController::class, 'updateCategory'])->name('faq.categories.update');
    Route::delete('/faq/categories/{category}', [FaqController::class, 'destroyCategory'])->name('faq.categories.destroy');
    
    // FAQ Questions
    Route::get('/faq/categories/{category}/questions/create', [FaqController::class, 'createQuestion'])->name('faq.questions.create');
    Route::post('/faq/categories/{category}/questions', [FaqController::class, 'storeQuestion'])->name('faq.questions.store');
    Route::get('/faq/questions/{question}/edit', [FaqController::class, 'editQuestion'])->name('faq.questions.edit');
    Route::put('/faq/questions/{question}', [FaqController::class, 'updateQuestion'])->name('faq.questions.update');
    Route::delete('/faq/questions/{question}', [FaqController::class, 'destroyQuestion'])->name('faq.questions.destroy');

    // Settings / App Config
    Route::get('/settings', [SettingsController::class, 'index'])->name('settings.index');
    Route::put('/settings/{key}', [SettingsController::class, 'update'])->name('settings.update');
});

require __DIR__.'/auth.php';
