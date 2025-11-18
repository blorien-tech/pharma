@extends('layouts.app')

@section('title', 'Receipt #' . $transaction->id)

@section('content')
<div class="max-w-3xl mx-auto space-y-6">
    <!-- Back Button -->
    <a href="{{ route('transactions.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800">
        <svg class="w-5 h-5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
        Back to Transactions
    </a>

    <!-- Receipt -->
    <div id="receipt" class="bg-white rounded-lg shadow-lg p-8">
        <!-- Header -->
        <div class="text-center border-b-2 border-gray-300 pb-4 mb-4">
            <h1 class="text-3xl font-bold text-gray-900">{{ config('app.pharmacy_name', 'BLORIEN Pharmacy') }}</h1>
            <p class="text-sm text-gray-600 mt-1">{{ env('PHARMACY_ADDRESS', 'Your Pharmacy Address') }}</p>
            <p class="text-sm text-gray-600">{{ env('PHARMACY_PHONE', '+1234567890') }}</p>
            <p class="text-sm text-gray-600">{{ env('PHARMACY_EMAIL', 'contact@blorienpharma.com') }}</p>
        </div>

        <!-- Transaction Info -->
        <div class="mb-6">
            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <p class="text-gray-600">Receipt No:</p>
                    <p class="font-semibold">#{{ $transaction->id }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Date:</p>
                    <p class="font-semibold">{{ $transaction->created_at->format('M d, Y H:i') }}</p>
                </div>
                <div>
                    <p class="text-gray-600">Type:</p>
                    <p class="font-semibold">
                        <span class="px-2 py-1 text-xs rounded-full
                            {{ $transaction->type === 'SALE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $transaction->type }}
                        </span>
                    </p>
                </div>
                <div>
                    <p class="text-gray-600">Cashier:</p>
                    <p class="font-semibold">{{ $transaction->user->name }}</p>
                </div>
            </div>
        </div>

        <!-- Items Table -->
        <div class="mb-6">
            <table class="w-full text-sm">
                <thead class="border-b-2 border-gray-300">
                    <tr class="text-left">
                        <th class="pb-2">Item</th>
                        <th class="pb-2 text-center">Qty</th>
                        <th class="pb-2 text-right">Price</th>
                        <th class="pb-2 text-right">Total</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @foreach($transaction->items as $item)
                    <tr>
                        <td class="py-2">
                            <div>
                                <p class="font-medium">{{ $item->product->name }}</p>
                                <p class="text-xs text-gray-500">SKU: {{ $item->product->sku }}</p>
                                @if($item->batch)
                                <p class="text-xs text-gray-500">Batch: {{ $item->batch->batch_number }}</p>
                                @endif
                            </div>
                        </td>
                        <td class="py-2 text-center">{{ abs($item->quantity) }}</td>
                        <td class="py-2 text-right">৳{{ number_format(abs($item->unit_price), 2) }}</td>
                        <td class="py-2 text-right font-medium">৳{{ number_format(abs($item->total), 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Totals -->
        <div class="border-t-2 border-gray-300 pt-4 mb-6">
            <div class="space-y-2">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Subtotal:</span>
                    <span class="font-medium">৳{{ number_format(abs($transaction->subtotal), 2) }}</span>
                </div>
                @if($transaction->discount > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Discount:</span>
                    <span class="font-medium text-red-600">-৳{{ number_format($transaction->discount, 2) }}</span>
                </div>
                @endif
                @if($transaction->tax > 0)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Tax:</span>
                    <span class="font-medium">৳{{ number_format($transaction->tax, 2) }}</span>
                </div>
                @endif
                <div class="flex justify-between text-xl font-bold border-t pt-2">
                    <span>Total:</span>
                    <span class="{{ $transaction->type === 'SALE' ? 'text-green-600' : 'text-red-600' }}">
                        ৳{{ number_format(abs($transaction->total), 2) }}
                    </span>
                </div>
            </div>
        </div>

        <!-- Payment Details -->
        @if($transaction->type === 'SALE')
        <div class="border-t border-gray-300 pt-4 mb-6">
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Payment Method:</span>
                    <span class="font-medium">{{ $transaction->payment_method }}</span>
                </div>
                @if($transaction->payment_method === 'CASH')
                <div class="flex justify-between">
                    <span class="text-gray-600">Amount Paid:</span>
                    <span class="font-medium">৳{{ number_format($transaction->amount_paid, 2) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Change:</span>
                    <span class="font-medium">৳{{ number_format($transaction->change_given, 2) }}</span>
                </div>
                @endif
            </div>
        </div>
        @endif

        @if($transaction->type === 'RETURN' && $transaction->relatedTransaction)
        <div class="bg-yellow-50 border border-yellow-200 rounded p-3 mb-6">
            <p class="text-sm text-yellow-800">
                <strong>Return for Transaction:</strong> #{{ $transaction->related_transaction_id }}
            </p>
        </div>
        @endif

        <!-- Footer -->
        <div class="border-t-2 border-gray-300 pt-4 text-center text-sm text-gray-600">
            <p class="font-medium">{{ env('RECEIPT_FOOTER', 'Thank you for your business!') }}</p>
            <p class="mt-2 text-xs">This is a computer-generated receipt</p>
        </div>
    </div>

    <!-- Action Buttons -->
    <div class="flex gap-3 justify-center no-print">
        <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
            Print Receipt
        </button>
        @if($transaction->type === 'SALE')
        <button onclick="processReturn({{ $transaction->id }})" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-medium">
            Process Return
        </button>
        @endif
    </div>
</div>

@push('scripts')
<script>
async function processReturn(transactionId) {
    if (!confirm('Are you sure you want to process a return for this transaction? This will refund the full amount and restore inventory.')) {
        return;
    }

    try {
        const response = await fetch(`/api/transactions/${transactionId}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            alert('Return processed successfully!');
            window.location.href = `/transactions/${data.transaction.id}`;
        } else {
            alert(data.message || 'Error processing return');
        }
    } catch (error) {
        console.error('Return error:', error);
        alert('Error processing return');
    }
}

// Print styles
const style = document.createElement('style');
style.textContent = `
    @media print {
        body * {
            visibility: hidden;
        }
        #receipt, #receipt * {
            visibility: visible;
        }
        #receipt {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }
        .no-print {
            display: none !important;
        }
    }
`;
document.head.appendChild(style);
</script>
@endpush
@endsection
