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
        // Storage locations (hierarchical: Rack > Shelf > Bin)
        Schema::create('storage_locations', function (Blueprint $table) {
            $table->id();
            $table->string('code', 20)->unique(); // "R1-S3-B2" (Rack 1, Shelf 3, Bin 2)
            $table->string('name', 100); // "Main Rack 1, Shelf 3, Bin 2"
            $table->enum('type', ['RACK', 'SHELF', 'BIN', 'FLOOR', 'REFRIGERATOR', 'COUNTER', 'WAREHOUSE'])->default('BIN');
            $table->foreignId('parent_id')->nullable()->constrained('storage_locations')->onDelete('cascade');
            $table->integer('capacity')->nullable()->comment('Max items/batches this location can hold');
            $table->boolean('temperature_controlled')->default(false);
            $table->decimal('temperature_min', 5, 2)->nullable()->comment('Minimum temperature in Celsius');
            $table->decimal('temperature_max', 5, 2)->nullable()->comment('Maximum temperature in Celsius');
            $table->integer('display_order')->default(0)->comment('Order for display in lists');
            $table->text('notes')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            // Indexes for performance
            $table->index('code');
            $table->index('type');
            $table->index('parent_id');
            $table->index('is_active');
            $table->index('display_order');
        });

        // Add location tracking to product batches
        Schema::table('product_batches', function (Blueprint $table) {
            $table->foreignId('storage_location_id')->nullable()->after('is_active')->constrained('storage_locations')->onDelete('set null');
            $table->index('storage_location_id');
        });

        // Stock movement tracking (audit trail)
        Schema::create('stock_movements', function (Blueprint $table) {
            $table->id();
            $table->foreignId('batch_id')->constrained('product_batches')->onDelete('cascade');
            $table->foreignId('from_location_id')->nullable()->constrained('storage_locations')->onDelete('set null');
            $table->foreignId('to_location_id')->constrained('storage_locations')->onDelete('set null');
            $table->integer('quantity')->comment('Quantity moved');
            $table->enum('reason', [
                'RECEIPT',       // Stock received from PO
                'TRANSFER',      // Moved between locations
                'ADJUSTMENT',    // Manual adjustment
                'SALE',          // Sold from this location
                'RETURN',        // Customer return to location
                'EXPIRED',       // Moved to expired section
                'DAMAGED',       // Moved to damaged goods
                'QUARANTINE',    // Moved to quarantine
                'OTHER'
            ])->default('TRANSFER');
            $table->foreignId('moved_by')->constrained('users')->onDelete('cascade');
            $table->text('notes')->nullable();
            $table->timestamp('created_at');

            // Indexes
            $table->index('batch_id');
            $table->index('from_location_id');
            $table->index('to_location_id');
            $table->index('created_at');
            $table->index(['batch_id', 'created_at']); // Compound index for batch movement history
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_movements');

        Schema::table('product_batches', function (Blueprint $table) {
            $table->dropForeign(['storage_location_id']);
            $table->dropColumn('storage_location_id');
        });

        Schema::dropIfExists('storage_locations');
    }
};
