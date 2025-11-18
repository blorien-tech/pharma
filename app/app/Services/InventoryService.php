<?php

namespace App\Services;

use App\Models\Product;
use App\Models\ProductBatch;
use Carbon\Carbon;

class InventoryService
{
    /**
     * Get low stock products
     */
    public function getLowStockProducts()
    {
        return Product::where('is_active', true)
            ->whereColumn('current_stock', '<=', 'min_stock')
            ->orderBy('current_stock')
            ->get();
    }

    /**
     * Get expiring batches within specified days
     */
    public function getExpiringBatches($days = null)
    {
        $days = $days ?? config('app.expiry_warning_days', 30);

        return ProductBatch::with('product')
            ->expiringSoon($days)
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Get expired batches
     */
    public function getExpiredBatches()
    {
        return ProductBatch::with('product')
            ->expired()
            ->where('quantity_remaining', '>', 0)
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Deduct stock from a product and batch
     */
    public function deductStock(Product $product, ProductBatch $batch = null, int $quantity)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        // If batch is specified, deduct from batch
        if ($batch) {
            if ($batch->quantity_remaining < $quantity) {
                throw new \Exception("Insufficient stock in batch {$batch->batch_number}. Available: {$batch->quantity_remaining}");
            }

            $batch->decrement('quantity_remaining', $quantity);
        }

        // Always deduct from product total
        if ($product->current_stock < $quantity) {
            throw new \Exception("Insufficient product stock. Available: {$product->current_stock}");
        }

        $product->decrement('current_stock', $quantity);

        return true;
    }

    /**
     * Add stock to a product and batch
     */
    public function addStock(Product $product, ProductBatch $batch = null, int $quantity)
    {
        if ($quantity <= 0) {
            throw new \InvalidArgumentException('Quantity must be greater than 0');
        }

        // If batch is specified, add to batch
        if ($batch) {
            $batch->increment('quantity_remaining', $quantity);
        }

        // Always add to product total
        $product->increment('current_stock', $quantity);

        return true;
    }

    /**
     * Get best batch to use for a product (FIFO - First In First Out by expiry)
     */
    public function getBestBatchForProduct(Product $product, int $quantity)
    {
        $batch = $product->activeBatches()
            ->where('quantity_remaining', '>=', $quantity)
            ->orderBy('expiry_date', 'asc')
            ->first();

        return $batch;
    }

    /**
     * Get all alerts (low stock + expiring batches)
     */
    public function getAllAlerts()
    {
        $lowStock = $this->getLowStockProducts();
        $expiring = $this->getExpiringBatches();
        $expired = $this->getExpiredBatches();

        return [
            'low_stock' => [
                'count' => $lowStock->count(),
                'items' => $lowStock,
            ],
            'expiring_soon' => [
                'count' => $expiring->count(),
                'items' => $expiring,
            ],
            'expired' => [
                'count' => $expired->count(),
                'items' => $expired,
            ],
            'total_alerts' => $lowStock->count() + $expiring->count() + $expired->count(),
        ];
    }

    /**
     * Check if product has sufficient stock
     */
    public function hasStock(Product $product, int $quantity): bool
    {
        return $product->current_stock >= $quantity;
    }

    /**
     * Get stock summary for a product
     */
    public function getProductStockSummary(Product $product)
    {
        $activeBatches = $product->activeBatches()->get();
        $totalBatchStock = $activeBatches->sum('quantity_remaining');

        return [
            'product_id' => $product->id,
            'product_name' => $product->name,
            'sku' => $product->sku,
            'current_stock' => $product->current_stock,
            'min_stock' => $product->min_stock,
            'is_low_stock' => $product->isLowStock(),
            'active_batches_count' => $activeBatches->count(),
            'total_batch_stock' => $totalBatchStock,
            'stock_mismatch' => $product->current_stock != $totalBatchStock,
        ];
    }
}
