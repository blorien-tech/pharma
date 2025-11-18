<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ProductBatch;
use Illuminate\Support\Facades\DB;

class PosService
{
    protected $inventoryService;

    public function __construct(InventoryService $inventoryService)
    {
        $this->inventoryService = $inventoryService;
    }

    /**
     * Process a sale transaction
     */
    public function processSale(array $items, array $data)
    {
        return DB::transaction(function () use ($items, $data) {
            // Calculate totals
            $subtotal = 0;
            $processedItems = [];

            foreach ($items as $item) {
                $product = Product::findOrFail($item['product_id']);

                // Check stock
                if (!$this->inventoryService->hasStock($product, $item['quantity'])) {
                    throw new \Exception("Insufficient stock for product: {$product->name}");
                }

                // Get best batch (FIFO)
                $batch = $this->inventoryService->getBestBatchForProduct($product, $item['quantity']);

                $itemSubtotal = $item['quantity'] * $item['unit_price'];
                $subtotal += $itemSubtotal;

                $processedItems[] = [
                    'product' => $product,
                    'batch' => $batch,
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['unit_price'],
                    'subtotal' => $itemSubtotal,
                ];
            }

            // Calculate final total
            $discount = $data['discount'] ?? 0;
            $total = $subtotal - $discount;

            // Create transaction
            $transaction = Transaction::create([
                'type' => 'SALE',
                'user_id' => auth()->id(),
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'amount_paid' => $data['amount_paid'] ?? $total,
                'change_given' => ($data['amount_paid'] ?? $total) - $total,
            ]);

            // Create transaction items and update inventory
            foreach ($processedItems as $processedItem) {
                TransactionItem::create([
                    'transaction_id' => $transaction->id,
                    'product_id' => $processedItem['product']->id,
                    'batch_id' => $processedItem['batch']->id ?? null,
                    'quantity' => $processedItem['quantity'],
                    'unit_price' => $processedItem['unit_price'],
                    'subtotal' => $processedItem['subtotal'],
                    'discount' => 0,
                    'total' => $processedItem['subtotal'],
                ]);

                // Deduct stock
                $this->inventoryService->deductStock(
                    $processedItem['product'],
                    $processedItem['batch'],
                    $processedItem['quantity']
                );
            }

            return $transaction->load('items.product', 'user');
        });
    }

    /**
     * Process a return transaction
     */
    public function processReturn(Transaction $originalTransaction)
    {
        if ($originalTransaction->type !== 'SALE') {
            throw new \Exception('Can only return SALE transactions');
        }

        return DB::transaction(function () use ($originalTransaction) {
            // Create return transaction
            $returnTransaction = Transaction::create([
                'type' => 'RETURN',
                'user_id' => auth()->id(),
                'related_transaction_id' => $originalTransaction->id,
                'subtotal' => -$originalTransaction->subtotal,
                'discount' => -$originalTransaction->discount,
                'tax' => -$originalTransaction->tax,
                'total' => -$originalTransaction->total,
                'payment_method' => $originalTransaction->payment_method,
                'amount_paid' => -$originalTransaction->total,
                'change_given' => 0,
            ]);

            // Create return items and restore inventory
            foreach ($originalTransaction->items as $item) {
                TransactionItem::create([
                    'transaction_id' => $returnTransaction->id,
                    'product_id' => $item->product_id,
                    'batch_id' => $item->batch_id,
                    'quantity' => -$item->quantity,
                    'unit_price' => $item->unit_price,
                    'subtotal' => -$item->subtotal,
                    'discount' => -$item->discount,
                    'total' => -$item->total,
                ]);

                // Restore stock
                $this->inventoryService->addStock(
                    $item->product,
                    $item->batch,
                    $item->quantity
                );
            }

            return $returnTransaction->load('items.product', 'user', 'relatedTransaction');
        });
    }
}
