<?php

namespace App\Services;

use App\Models\PurchaseOrder;
use App\Models\PurchaseOrderItem;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;

class PurchaseOrderService
{
    /**
     * Create a new purchase order
     */
    public function createPurchaseOrder(array $data)
    {
        return DB::transaction(function () use ($data) {
            // Calculate totals
            $subtotal = 0;
            foreach ($data['items'] as $item) {
                $subtotal += $item['quantity'] * $item['unit_price'];
            }

            $tax = $data['tax'] ?? 0;
            $shipping = $data['shipping'] ?? 0;
            $total = $subtotal + $tax + $shipping;

            // Create purchase order
            $purchaseOrder = PurchaseOrder::create([
                'po_number' => PurchaseOrder::generatePoNumber(),
                'supplier_id' => $data['supplier_id'],
                'user_id' => auth()->id(),
                'status' => 'PENDING',
                'order_date' => $data['order_date'],
                'expected_delivery_date' => $data['expected_delivery_date'] ?? null,
                'subtotal' => $subtotal,
                'tax' => $tax,
                'shipping' => $shipping,
                'total' => $total,
                'notes' => $data['notes'] ?? null,
            ]);

            // Create purchase order items
            foreach ($data['items'] as $item) {
                PurchaseOrderItem::create([
                    'purchase_order_id' => $purchaseOrder->id,
                    'product_id' => $item['product_id'],
                    'quantity_ordered' => $item['quantity'],
                    'quantity_received' => 0,
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $item['quantity'] * $item['unit_price'],
                ]);
            }

            return $purchaseOrder->load(['supplier', 'items.product']);
        });
    }

    /**
     * Receive stock from a purchase order
     */
    public function receiveStock(PurchaseOrder $purchaseOrder, array $data)
    {
        return DB::transaction(function () use ($purchaseOrder, $data) {
            // Update purchase order status
            $purchaseOrder->update([
                'status' => 'RECEIVED',
                'received_date' => $data['received_date'],
            ]);

            // Process each item
            foreach ($data['items'] as $itemData) {
                $item = PurchaseOrderItem::findOrFail($itemData['id']);

                // Update received quantity
                $item->update([
                    'quantity_received' => $itemData['quantity_received'],
                    'batch_number' => $itemData['batch_number'],
                    'expiry_date' => $itemData['expiry_date'],
                ]);

                // Create product batch
                $batch = ProductBatch::create([
                    'product_id' => $item->product_id,
                    'batch_number' => $itemData['batch_number'],
                    'expiry_date' => $itemData['expiry_date'],
                    'quantity_received' => $itemData['quantity_received'],
                    'quantity_remaining' => $itemData['quantity_received'],
                    'purchase_price' => $item->unit_price,
                    'is_active' => true,
                ]);

                // Update product stock
                $product = Product::findOrFail($item->product_id);
                $product->increment('current_stock', $itemData['quantity_received']);

                // Update product purchase price if different
                if ($product->purchase_price != $item->unit_price) {
                    $product->update(['purchase_price' => $item->unit_price]);
                }
            }

            return $purchaseOrder->fresh(['items.product']);
        });
    }

    /**
     * Update purchase order to ORDERED status
     */
    public function markAsOrdered(PurchaseOrder $purchaseOrder)
    {
        if ($purchaseOrder->status !== 'PENDING') {
            throw new \Exception('Only pending purchase orders can be marked as ordered.');
        }

        $purchaseOrder->update(['status' => 'ORDERED']);

        return $purchaseOrder;
    }
}
