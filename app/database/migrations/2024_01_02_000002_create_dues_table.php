<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Simple dues tracking - like digital notebook
     * Quick entry without requiring full customer profile
     */
    public function up(): void
    {
        Schema::create('dues', function (Blueprint $table) {
            $table->id();
            $table->string('customer_name');
            $table->string('customer_phone')->nullable();
            $table->foreignId('transaction_id')->nullable()->constrained()->onDelete('set null');
            $table->foreignId('user_id')->constrained()->onDelete('cascade'); // Who recorded it
            $table->decimal('amount', 12, 2);
            $table->decimal('amount_paid', 12, 2)->default(0);
            $table->decimal('amount_remaining', 12, 2);
            $table->enum('status', ['PENDING', 'PARTIAL', 'PAID'])->default('PENDING');
            $table->text('notes')->nullable();
            $table->timestamp('due_date')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamps();

            $table->index('customer_phone');
            $table->index('status');
            $table->index('created_at');
        });

        // Due payments - track partial payments
        Schema::create('due_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('due_id')->constrained()->onDelete('cascade');
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->decimal('amount', 12, 2);
            $table->enum('payment_method', ['CASH', 'CARD', 'MOBILE', 'OTHER'])->default('CASH');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index('due_id');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('due_payments');
        Schema::dropIfExists('dues');
    }
};
