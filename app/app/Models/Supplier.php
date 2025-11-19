<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'company_name',
        'email',
        'phone',
        'address',
        'city',
        'country',
        'tax_id',
        'notes',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get products from this supplier
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Get purchase orders from this supplier
     */
    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    /**
     * Get pending purchase orders
     */
    public function pendingOrders()
    {
        return $this->purchaseOrders()->where('status', 'PENDING');
    }

    /**
     * Get total amount spent with this supplier
     */
    public function totalSpent()
    {
        return $this->purchaseOrders()
            ->where('status', 'RECEIVED')
            ->sum('total');
    }

    /**
     * Scope for searching suppliers
     */
    public function scopeSearch($query, $search)
    {
        return $query->where(function($q) use ($search) {
            $q->where('name', 'like', "%{$search}%")
              ->orWhere('company_name', 'like', "%{$search}%")
              ->orWhere('email', 'like', "%{$search}%")
              ->orWhere('phone', 'like', "%{$search}%");
        });
    }

    /**
     * Scope for active suppliers only
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }
}
