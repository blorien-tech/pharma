<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            // Add generic and brand name for better search
            $table->string('generic_name')->nullable()->after('name');
            $table->string('brand_name')->nullable()->after('generic_name');

            // Add barcode support
            $table->string('barcode')->nullable()->after('sku');

            // Index for faster search
            $table->index('generic_name');
            $table->index('brand_name');
            $table->index('barcode');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropIndex(['generic_name']);
            $table->dropIndex(['brand_name']);
            $table->dropIndex(['barcode']);
            $table->dropColumn(['generic_name', 'brand_name', 'barcode']);
        });
    }
};
