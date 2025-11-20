<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\TransactionItem;
use App\Models\Product;
use App\Models\ProductBatch;
use App\Models\Customer;
use App\Models\CustomerCreditTransaction;
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

            // Check if credit sale
            $isCredit = isset($data['customer_id']) && isset($data['is_credit']) && $data['is_credit'];
            $customer = null;

            if ($isCredit) {
                $customer = Customer::findOrFail($data['customer_id']);

                // Validate credit availability
                if (!$customer->credit_enabled) {
                    throw new \Exception("Credit is not enabled for this customer");
                }

                if (!$customer->hasCreditAvailable($total)) {
                    throw new \Exception("Insufficient credit available. Available: ৳" . number_format($customer->availableCredit(), 2));
                }
            }

            // Create transaction
            $transaction = Transaction::create([
                'type' => 'SALE',
                'user_id' => auth()->id(),
                'customer_id' => $isCredit ? $customer->id : null,
                'is_credit' => $isCredit,
                'subtotal' => $subtotal,
                'discount' => $discount,
                'tax' => 0,
                'total' => $total,
                'payment_method' => $data['payment_method'],
                'amount_paid' => $isCredit ? 0 : ($data['amount_paid'] ?? $total),
                'change_given' => $isCredit ? 0 : (($data['amount_paid'] ?? $total) - $total),
            ]);

            // Record credit transaction
            if ($isCredit) {
                $balanceBefore = $customer->current_balance;
                $balanceAfter = $balanceBefore + $total;

                CustomerCreditTransaction::create([
                    'customer_id' => $customer->id,
                    'transaction_id' => $transaction->id,
                    'type' => 'SALE',
                    'amount' => $total,
                    'balance_before' => $balanceBefore,
                    'balance_after' => $balanceAfter,
                    'notes' => "Credit sale - Invoice {$transaction->invoice_number}",
                    'created_by' => auth()->id(),
                ]);

                // Update customer balance
                $customer->increment('current_balance', $total);
            }

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
