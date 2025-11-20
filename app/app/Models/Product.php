<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Product extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name',
        'generic_name',
        'brand_name',
        'sku',
        'barcode',
        'supplier_id',
        'description',
        'purchase_price',
        'selling_price',
        'current_stock',
        'min_stock',
        'is_active',
    ];

    protected function casts(): array
    {
        return [
            'purchase_price' => 'decimal:2',
            'selling_price' => 'decimal:2',
            'current_stock' => 'integer',
            'min_stock' => 'integer',
            'is_active' => 'boolean',
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
     * Get batches for this product
     */
    public function batches()
    {
        return $this->hasMany(ProductBatch::class);
    }

    /**
     * Get active batches only
     */
    public function activeBatches()
    {
        return $this->batches()->where('is_active', true)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date', 'asc');
    }

    /**
     * Get transaction items
     */
    public function transactionItems()
    {
        return $this->hasMany(TransactionItem::class);
    }

    /**
     * Check if product is low on stock
     */
    public function isLowStock(): bool
    {
        return $this->current_stock <= $this->min_stock;
    }

    /**
     * Scope for searching products
     * Searches by name, generic name, brand name, SKU, barcode, and description
     * Case-insensitive search
     */
    public function scopeSearch($query, $search)
    {
        $searchTerm = strtolower($search);

        return $query->where(function($q) use ($searchTerm, $search) {
            $q->whereRaw('LOWER(name) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(generic_name) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(brand_name) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(sku) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(barcode) LIKE ?', ["%{$searchTerm}%"])
              ->orWhereRaw('LOWER(description) LIKE ?', ["%{$searchTerm}%"]);
        });
    }
}
