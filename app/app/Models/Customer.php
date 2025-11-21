<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'id_number',
        'credit_limit',
        'current_balance',
        'credit_enabled',
        'is_active',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'credit_limit' => 'decimal:2',
            'current_balance' => 'decimal:2',
            'credit_enabled' => 'boolean',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get transactions for this customer
     */
    public function transactions()
    {
        return $this->hasMany(Transaction::class);
    }

    /**
     * Get credit transactions
     */
    public function creditTransactions()
    {
        return $this->hasMany(CustomerCreditTransaction::class);
    }

    /**
     * Get dues for this customer
     */
    public function dues()
    {
        return $this->hasMany(Due::class);
    }

    /**
     * Get available credit
     */
    public function availableCredit()
    {
        if (!$this->credit_enabled) {
            return 0;
        }
        return $this->credit_limit - $this->current_balance;
    }

    /**
     * Check if customer has credit available
     */
    public function hasCreditAvailable($amount)
    {
        return $this->credit_enabled && $this->availableCredit() >= $amount;
    }

    /**
     * Check if customer is overdue
     */
    public function isOverdue()
    {
        return $this->current_balance > $this->credit_limit;
    }

    /**
     * Scope for searching customers
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for active customers
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for credit-enabled customers
     */
    public function scopeCreditEnabled($query)
    {
        return $query->where('credit_enabled', true);
    }
}
