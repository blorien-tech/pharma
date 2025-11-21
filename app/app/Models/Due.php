<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Due extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'customer_name',
        'customer_phone',
        'transaction_id',
        'user_id',
        'amount',
        'amount_paid',
        'amount_remaining',
        'status',
        'notes',
        'due_date',
        'paid_at',
    ];

    protected function casts(): array
    {
        return [
            'amount' => 'decimal:2',
            'amount_paid' => 'decimal:2',
            'amount_remaining' => 'decimal:2',
            'due_date' => 'datetime',
            'paid_at' => 'datetime',
        ];
    }

    /**
     * Get the customer associated with this due
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Get the transaction associated with this due
     */
    public function transaction()
    {
        return $this->belongsTo(Transaction::class);
    }

    /**
     * Get the user who recorded this due
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get payments for this due
     */
    public function payments()
    {
        return $this->hasMany(DuePayment::class);
    }

    /**
     * Check if due is pending (no payment)
     */
    public function isPending()
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if due is partially paid
     */
    public function isPartial()
    {
        return $this->status === 'PARTIAL';
    }

    /**
     * Check if due is fully paid
     */
    public function isPaid()
    {
        return $this->status === 'PAID';
    }

    /**
     * Check if due is overdue
     */
    public function isOverdue()
    {
        if ($this->isPaid()) {
            return false;
        }

        return $this->due_date && $this->due_date->isPast();
    }

    /**
     * Record a payment for this due
     */
    public function recordPayment($amount, $paymentMethod = 'CASH', $notes = null, $userId)
    {
        if ($amount > $this->amount_remaining) {
            throw new \Exception('Payment amount cannot exceed remaining balance');
        }

        // Create payment record
        $payment = $this->payments()->create([
            'user_id' => $userId,
            'amount' => $amount,
            'payment_method' => $paymentMethod,
            'notes' => $notes,
        ]);

        // Update due amounts
        $this->amount_paid += $amount;
        $this->amount_remaining -= $amount;

        // Update status
        if ($this->amount_remaining <= 0) {
            $this->status = 'PAID';
            $this->paid_at = now();
        } else {
            $this->status = 'PARTIAL';
        }

        $this->save();

        return $payment;
    }

    /**
     * Scope for pending dues
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope for partial dues
     */
    public function scopePartial($query)
    {
        return $query->where('status', 'PARTIAL');
    }

    /**
     * Scope for paid dues
     */
    public function scopePaid($query)
    {
        return $query->where('status', 'PAID');
    }

    /**
     * Scope for overdue dues
     */
    public function scopeOverdue($query)
    {
        return $query->where('status', '!=', 'PAID')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', now());
    }

    /**
     * Scope for searching by customer
     */
    public function scopeSearchCustomer($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('customer_name', 'like', "%{$search}%")
              ->orWhere('customer_phone', 'like', "%{$search}%");
        });
    }
}
