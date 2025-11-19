<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\AnalyticsController;

Route::middleware('auth')->group(function () {

    // Products API
    Route::get('/products', [ProductController::class, 'apiIndex']);
    Route::post('/products', [ProductController::class, 'apiStore']);
    Route::get('/products/search', [ProductController::class, 'search']);
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

});
