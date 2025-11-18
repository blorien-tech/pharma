<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'user_id',
        'related_transaction_id',
        'subtotal',
        'tax',
        'discount',
        'total',
        'payment_method',
        'amount_paid',
        'change_given',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'total' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'change_given' => 'decimal:2',
        ];
    }

    /**
     * Get the user who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get items in this transaction
     */
    public function items()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Get related transaction (for returns)
     */
    public function relatedTransaction()
    {
        return $this->belongsTo(Transaction::class, 'related_transaction_id');
    }

    /**
     * Get returns for this transaction
     */
    public function returns()
    {
        return $this->hasMany(Transaction::class, 'related_transaction_id');
    }

    /**
     * Check if this is a sale
     */
    public function isSale(): bool
    {
        return $this->type === 'SALE';
    }

    /**
     * Check if this is a return
     */
    public function isReturn(): bool
    {
        return $this->type === 'RETURN';
    }

    /**
     * Scope for sales only
     */
    public function scopeSales($query)
    {
        return $query->where('type', 'SALE');
    }

    /**
     * Scope for returns only
     */
    public function scopeReturns($query)
    {
        return $query->where('type', 'RETURN');
    }

    /**
     * Scope for today's transactions
     */
    public function scopeToday($query)
    {
        return $query->whereDate('created_at', today());
    }
}
