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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['SALE', 'RETURN'])->default('SALE');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('related_transaction_id')->nullable()->constrained('transactions')->onDelete('set null');
            $table->decimal('subtotal', 10, 2)->default(0);
            $table->decimal('tax', 10, 2)->default(0);
            $table->decimal('discount', 10, 2)->default(0);
            $table->decimal('total', 10, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'MOBILE', 'OTHER'])->default('CASH');
            $table->decimal('amount_paid', 10, 2)->nullable();
            $table->decimal('change_given', 10, 2)->nullable();
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('type');
            $table->index('user_id');
            $table->index('created_at');
            $table->index('payment_method');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
