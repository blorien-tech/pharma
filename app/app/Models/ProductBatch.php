<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ProductBatch extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'batch_number',
        'expiry_date',
        'quantity_received',
        'quantity_remaining',
        'purchase_price',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'expiry_date' => 'date',
            'quantity_received' => 'integer',
            'quantity_remaining' => 'integer',
            'purchase_price' => 'decimal:2',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the product that owns this batch
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Get transaction items using this batch
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class, 'batch_id');
    }

    /**
     * Check if batch is expired
     */
    public function isExpired(): bool
    {
        return $this->expiry_date < Carbon::today();
    }

    /**
     * Check if batch is expiring soon (within configured days)
     */
    public function isExpiringSoon(): bool
    {
        $warningDays = config('app.expiry_warning_days', 30);
        $warningDate = Carbon::today()->addDays($warningDays);
        return $this->expiry_date <= $warningDate && !$this->isExpired();
    }

    /**
     * Scope for expired batches
     */
    public function scopeExpired($query)
    {
        return $query->where('expiry_date', '<', Carbon::today());
    }

    /**
     * Scope for expiring soon batches
     */
    public function scopeExpiringSoon($query, $days = null)
    {
        $days = $days ?? config('app.expiry_warning_days', 30);
        $warningDate = Carbon::today()->addDays($days);
        return $query->where('expiry_date', '<=', $warningDate)
                     ->where('expiry_date', '>=', Carbon::today());
    }
}
