<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add invoice_number column to transactions table
     * Format: YYYYMMDDnnnnnn (date + 6-digit sequential number)
     * Example: 20251121000001
     */
    public function up(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->string('invoice_number', 14)->unique()->after('id');
            $table->index('invoice_number');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex(['invoice_number']);
            $table->dropColumn('invoice_number');
        });
    }
};
