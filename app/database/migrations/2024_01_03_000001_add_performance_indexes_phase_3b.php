<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations - Phase 3B Performance Optimizations
     */
    public function up(): void
    {
        // Products table indexes for common queries
        Schema::table('products', function (Blueprint $table) {
            $table->index('is_active', 'idx_products_is_active');
            $table->index(['current_stock', 'min_stock'], 'idx_products_stock_check');
            $table->index(['generic_name', 'brand_name'], 'idx_products_search');
        });

        // Product batches indexes for expiry queries
        Schema::table('product_batches', function (Blueprint $table) {
            $table->index('expiry_date', 'idx_batches_expiry');
            $table->index('is_active', 'idx_batches_is_active');
        });

        // Transactions indexes for date-based queries
        Schema::table('transactions', function (Blueprint $table) {
            $table->index('created_at', 'idx_transactions_created_at');
            $table->index(['type', 'created_at'], 'idx_transactions_type_date');
            $table->index('payment_method', 'idx_transactions_payment_method');
        });

        // Dues indexes for status and date queries
        Schema::table('dues', function (Blueprint $table) {
            $table->index('status', 'idx_dues_status');
            $table->index('customer_phone', 'idx_dues_customer_phone');
            $table->index(['status', 'due_date'], 'idx_dues_status_date');
        });

        // Due payments indexes
        Schema::table('due_payments', function (Blueprint $table) {
            $table->index('due_id', 'idx_due_payments_due_id');
            $table->index('created_at', 'idx_due_payments_created_at');
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex('idx_products_is_active');
            $table->dropIndex('idx_products_stock_check');
            $table->dropIndex('idx_products_search');
        });

        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropIndex('idx_batches_expiry');
            $table->dropIndex('idx_batches_is_active');
        });

        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('idx_transactions_created_at');
            $table->dropIndex('idx_transactions_type_date');
            $table->dropIndex('idx_transactions_payment_method');
        });

        Schema::table('dues', function (Blueprint $table) {
            $table->dropIndex('idx_dues_status');
            $table->dropIndex('idx_dues_customer_phone');
            $table->dropIndex('idx_dues_status_date');
        });

        Schema::table('due_payments', function (Blueprint $table) {
            $table->dropIndex('idx_due_payments_due_id');
            $table->dropIndex('idx_due_payments_created_at');
        });
    }
};
