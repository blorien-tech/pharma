<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add customer_id to link dues with customer records
     * This allows automatic customer creation and better tracking
     */
    public function up(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->foreignId('customer_id')->nullable()->after('id')->constrained()->onDelete('set null');
            $table->index('customer_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('dues', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
            $table->dropIndex(['customer_id']);
            $table->dropColumn('customer_id');
        });
    }
};
