<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerCreditTransaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'transaction_id',
        'type',
        'amount',
        'balance_before',
        'balance_after',
        'notes',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'balance_before' => 'decimal:2',
            'balance_after' => 'decimal:2',
        ];
    }

    /**
     * Get the customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the related transaction
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user who created this transaction
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Check if this is a sale transaction
     */
    public function isSale()
    {
        return $this->type === 'SALE';
    }

    /**
     * Check if this is a payment transaction
     */
    public function isPayment()
    {
        return $this->type === 'PAYMENT';
    }

    /**
     * Check if this is an adjustment transaction
     */
    public function isAdjustment()
    {
        return $this->type === 'ADJUSTMENT';
    }
}
