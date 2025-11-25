<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'type',
        'user_id',
        'customer_id',
        'is_credit',
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
            'is_credit' => 'boolean',
        ];
    }

    /**
     * Boot method to auto-generate invoice numbers
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($transaction) {
            if (empty($transaction->invoice_number)) {
                $transaction->invoice_number = static::generateInvoiceNumber();
            }
        });
    }

    /**
     * Generate unique invoice number in format: YYYYMMDDnnnnnn
     * Example: 20251121000001
     */
    public static function generateInvoiceNumber(): string
    {
        $datePrefix = now()->format('Ymd'); // 20251121

        // Get the last invoice number with today's date prefix
        $lastInvoice = static::where('invoice_number', 'like', $datePrefix . '%')
            ->orderBy('invoice_number', 'desc')
            ->first();

        if ($lastInvoice) {
            // Extract the sequential number and increment it
            $lastSequence = (int) substr($lastInvoice->invoice_number, 8); // Get last 6 digits
            $newSequence = $lastSequence + 1;
        } else {
            // First transaction of the day
            $newSequence = 1;
        }

        // Pad with zeros to make it 6 digits
        $sequenceNumber = str_pad($newSequence, 6, '0', STR_PAD_LEFT);

        return $datePrefix . $sequenceNumber;
    }

    /**
     * Get the user who created this transaction
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the customer for this transaction
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
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
