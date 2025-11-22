<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\DueController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\DailyClosingController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\StorageLocationController;

Route::middleware('auth')->group(function () {

    // Products API
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::post('/products', [ProductController::class, 'apiStore']);
    // Product search moved to web routes for proper session authentication
    Route::post('/products/quick-stock', [ProductController::class, 'quickAddStock']); // Phase 3B
    Route::post('/products/{product}', [ProductController::class, 'apiUpdate']);
    Route::delete('/products/{product}', [ProductController::class, 'apiDestroy']);

    // Batches API
    Route::post('/products/{product}/batches', [BatchController::class, 'apiStore']);
    Route::get('/batches/expiring', [BatchController::class, 'expiring']);
    Route::get('/batches/expired', [BatchController::class, 'expired']);

    // Transactions API
    Route::post('/transactions', [TransactionController::class, 'complete']);
    Route::get('/transactions/today', [TransactionController::class, 'today']);
    Route::get('/transactions/recent', [TransactionController::class, 'recent']);
    Route::get('/transactions/{transaction}', [TransactionController::class, 'apiShow']);
    Route::post('/transactions/{transaction}/return', [TransactionController::class, 'processReturn']);

    // Users API
    Route::get('/users', [UserController::class, 'apiIndex']);
    Route::post('/users', [UserController::class, 'apiStore']);

    // Dashboard API
    Route::get('/dashboard/stats', [DashboardController::class, 'stats']);

    // Analytics API
    Route::get('/analytics/sales', [AnalyticsController::class, 'salesData']);

    // Dues API
    Route::post('/dues', [DueController::class, 'store']);
    Route::get('/dues/lookup/phone', [DueController::class, 'lookupByPhone']);
    Route::get('/dues/statistics', [DueController::class, 'statistics']);

    // Customers API
    Route::get('/customers/search-by-phone', [CustomerController::class, 'searchByPhone']);

    // Daily Closing API (Phase 3B)
    Route::get('/daily-closing/data', [DailyClosingController::class, 'getData']);

    // Storage Locations API (Phase 4A)
    Route::get('/locations/search', [StorageLocationController::class, 'search']);
    Route::get('/locations/suggest', [StorageLocationController::class, 'suggestForProduct']);
    Route::post('/locations/assign-batch', [StorageLocationController::class, 'assignBatch']);
    Route::post('/locations/move-batch', [StorageLocationController::class, 'moveBatch']);

});
