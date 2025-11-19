<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PurchaseOrder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'po_number',
        'supplier_id',
        'user_id',
        'status',
        'order_date',
        'expected_delivery_date',
        'received_date',
        'subtotal',
        'tax',
        'shipping',
        'total',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'order_date' => 'date',
            'expected_delivery_date' => 'date',
            'received_date' => 'date',
            'subtotal' => 'decimal:2',
            'tax' => 'decimal:2',
            'shipping' => 'decimal:2',
            'total' => 'decimal:2',
        ];
    }

    /**
     * Get the supplier
     */
    public function supplier()
    {
        return $this->belongsTo(Supplier::class);
    }

    /**
     * Get the user who created the order
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get purchase order items
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    /**
     * Check if order is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'PENDING';
    }

    /**
     * Check if order is received
     */
    public function isReceived(): bool
    {
        return $this->status === 'RECEIVED';
    }

    /**
     * Scope for pending orders
     */
    public function scopePending($query)
    {
        return $query->where('status', 'PENDING');
    }

    /**
     * Scope for received orders
     */
    public function scopeReceived($query)
    {
        return $query->where('status', 'RECEIVED');
    }

    /**
     * Scope for orders by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('order_date', [$startDate, $endDate]);
    }

    /**
     * Generate next PO number
     */
    public static function generatePoNumber()
    {
        $latest = self::withTrashed()->latest('id')->first();
        $number = $latest ? intval(substr($latest->po_number, 3)) + 1 : 1;
        return 'PO-' . str_pad($number, 6, '0', STR_PAD_LEFT);
    }
}
