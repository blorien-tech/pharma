<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\BatchController;
use App\Http\Controllers\PosController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\PurchaseOrderController;
use App\Http\Controllers\CustomerController;
use App\Http\Controllers\DueController;
use App\Http\Controllers\DailyClosingController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\AnalyticsController;
use App\Http\Controllers\LanguageController;

// Language switching (accessible to all)
Route::post('/language/switch', [LanguageController::class, 'switch'])->name('language.switch');

// Public routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/setup', [AuthController::class, 'showSetup'])->name('setup');
Route::post('/setup', [AuthController::class, 'setup']);

// Protected routes
Route::middleware('auth')->group(function () {
    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Dashboard
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard', [DashboardController::class, 'index']);
    Route::get('/alerts', function () {
        return view('dashboard.alerts');
    })->name('alerts');

    // Products
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/create', [ProductController::class, 'create'])->name('products.create');
    Route::post('/products', [ProductController::class, 'store'])->name('products.store');
    Route::get('/products/{product}/edit', [ProductController::class, 'edit'])->name('products.edit');
    Route::put('/products/{product}', [ProductController::class, 'update'])->name('products.update');
    Route::delete('/products/{product}', [ProductController::class, 'destroy'])->name('products.destroy');

    // Batches
    Route::get('/products/{product}/batches', [BatchController::class, 'index'])->name('batches.index');
    Route::post('/products/{product}/batches', [BatchController::class, 'store'])->name('batches.store');

    // POS
    Route::get('/pos', [PosController::class, 'index'])->name('pos.index');

    // POS Product Search API (using web middleware for session auth)
    Route::get('/api/products/search', [ProductController::class, 'search'])->name('api.products.search');

    // Transactions
    Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
    Route::get('/transactions/{transaction}', [TransactionController::class, 'show'])->name('transactions.show');

    // Suppliers (Owner/Manager only)
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('/suppliers', [SupplierController::class, 'index'])->name('suppliers.index');
        Route::get('/suppliers/create', [SupplierController::class, 'create'])->name('suppliers.create');
        Route::post('/suppliers', [SupplierController::class, 'store'])->name('suppliers.store');
        Route::get('/suppliers/{supplier}', [SupplierController::class, 'show'])->name('suppliers.show');
        Route::get('/suppliers/{supplier}/edit', [SupplierController::class, 'edit'])->name('suppliers.edit');
        Route::put('/suppliers/{supplier}', [SupplierController::class, 'update'])->name('suppliers.update');
        Route::delete('/suppliers/{supplier}', [SupplierController::class, 'destroy'])->name('suppliers.destroy');
    });

    // Purchase Orders (Owner/Manager only)
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('/purchase-orders', [PurchaseOrderController::class, 'index'])->name('purchase-orders.index');
        Route::get('/purchase-orders/create', [PurchaseOrderController::class, 'create'])->name('purchase-orders.create');
        Route::post('/purchase-orders', [PurchaseOrderController::class, 'store'])->name('purchase-orders.store');
        Route::get('/purchase-orders/{purchaseOrder}', [PurchaseOrderController::class, 'show'])->name('purchase-orders.show');
        Route::get('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'showReceive'])->name('purchase-orders.receive');
        Route::post('/purchase-orders/{purchaseOrder}/receive', [PurchaseOrderController::class, 'receive'])->name('purchase-orders.receive.store');
        Route::put('/purchase-orders/{purchaseOrder}/cancel', [PurchaseOrderController::class, 'cancel'])->name('purchase-orders.cancel');
    });

    // Customers
    Route::get('/customers', [CustomerController::class, 'index'])->name('customers.index');
    Route::get('/customers/create', [CustomerController::class, 'create'])->name('customers.create');
    Route::post('/customers', [CustomerController::class, 'store'])->name('customers.store');
    Route::get('/customers/{customer}', [CustomerController::class, 'show'])->name('customers.show');
    Route::get('/customers/{customer}/edit', [CustomerController::class, 'edit'])->name('customers.edit');
    Route::put('/customers/{customer}', [CustomerController::class, 'update'])->name('customers.update');
    Route::delete('/customers/{customer}', [CustomerController::class, 'destroy'])->name('customers.destroy');
    Route::get('/customers/{customer}/payment', [CustomerController::class, 'showPayment'])->name('customers.payment');
    Route::post('/customers/{customer}/payment', [CustomerController::class, 'recordPayment'])->name('customers.payment.store');
    Route::get('/customers/{customer}/adjustment', [CustomerController::class, 'showAdjustment'])->name('customers.adjustment');
    Route::post('/customers/{customer}/adjustment', [CustomerController::class, 'recordAdjustment'])->name('customers.adjustment.store');

    // Dues (Notebook-style due tracking)
    Route::get('/dues', [DueController::class, 'index'])->name('dues.index');
    Route::get('/dues/create', [DueController::class, 'create'])->name('dues.create');
    Route::post('/dues', [DueController::class, 'store'])->name('dues.store');
    Route::get('/dues/{due}', [DueController::class, 'show'])->name('dues.show');
    Route::get('/dues/{due}/edit', [DueController::class, 'edit'])->name('dues.edit');
    Route::put('/dues/{due}', [DueController::class, 'update'])->name('dues.update');
    Route::delete('/dues/{due}', [DueController::class, 'destroy'])->name('dues.destroy');
    Route::get('/dues/{due}/payment', [DueController::class, 'showPayment'])->name('dues.payment');
    Route::post('/dues/{due}/payment', [DueController::class, 'recordPayment'])->name('dues.payment.store');
    Route::get('/dues/lookup/phone', [DueController::class, 'lookupByPhone'])->name('dues.lookup.phone');

    // Daily Closing (Phase 3B)
    Route::get('/daily-closing', [DailyClosingController::class, 'index'])->name('daily-closing.index');

    // Reports
    Route::get('/reports', [ReportController::class, 'index'])->name('reports.index');
    Route::get('/reports/sales', [ReportController::class, 'sales'])->name('reports.sales');
    Route::get('/reports/profit', [ReportController::class, 'profit'])->name('reports.profit');
    Route::get('/reports/inventory', [ReportController::class, 'inventory'])->name('reports.inventory');
    Route::get('/reports/top-products', [ReportController::class, 'topProducts'])->name('reports.top-products');
    Route::get('/reports/suppliers', [ReportController::class, 'suppliers'])->name('reports.suppliers');
    Route::get('/reports/customers', [ReportController::class, 'customers'])->name('reports.customers');

    // Analytics
    Route::get('/analytics', [AnalyticsController::class, 'index'])->name('analytics.index');

    // Users (Owner/Manager only)
    Route::middleware('role:owner,manager')->group(function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/create', [UserController::class, 'create'])->name('users.create');
        Route::post('/users', [UserController::class, 'store'])->name('users.store');
    });
});
