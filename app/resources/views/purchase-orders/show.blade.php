@extends('layouts.app')

@section('title', 'PO ' . $purchaseOrder->po_number)

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $purchaseOrder->po_number }}</h1>
            <p class="mt-1 text-sm text-gray-600">Purchase Order Details</p>
        </div>
        <div class="flex gap-2">
            @if($purchaseOrder->status !== 'RECEIVED' && $purchaseOrder->status !== 'CANCELLED')
            <a href="{{ route('purchase-orders.receive', $purchaseOrder) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                Receive Stock
            </a>
            @endif
            <a href="{{ route('purchase-orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                Back to List
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Status</h3>
            <span class="px-3 py-1 text-sm font-semibold rounded-full
                {{ $purchaseOrder->status === 'RECEIVED' ? 'bg-green-100 text-green-800' :
                   ($purchaseOrder->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                   ($purchaseOrder->status === 'ORDERED' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                {{ $purchaseOrder->status }}
            </span>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Order Date</h3>
            <p class="text-lg font-semibold">{{ $purchaseOrder->order_date->format('M d, Y') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-sm font-medium text-gray-500 mb-2">Total Amount</h3>
            <p class="text-2xl font-bold text-green-600">৳{{ number_format($purchaseOrder->total, 2) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Order Information</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div><dt class="text-sm font-medium text-gray-500">Supplier</dt>
                <dd class="mt-1 text-sm text-gray-900"><a href="{{ route('suppliers.show', $purchaseOrder->supplier) }}" class="text-blue-600 hover:underline">{{ $purchaseOrder->supplier->name }}</a></dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Created By</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->user->name }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Expected Delivery</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->expected_delivery_date ? $purchaseOrder->expected_delivery_date->format('M d, Y') : '-' }}</dd></div>
            <div><dt class="text-sm font-medium text-gray-500">Received Date</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->received_date ? $purchaseOrder->received_date->format('M d, Y') : '-' }}</dd></div>
            @if($purchaseOrder->notes)
            <div class="md:col-span-2"><dt class="text-sm font-medium text-gray-500">Notes</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $purchaseOrder->notes }}</dd></div>
            @endif
        </dl>
    </div>

    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b"><h2 class="text-lg font-semibold text-gray-900">Order Items</h2></div>
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Ordered</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Unit Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Subtotal</th>
                    @if($purchaseOrder->status === 'RECEIVED')
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch/Expiry</th>
                    @endif
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @foreach($purchaseOrder->items as $item)
                <tr>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->product->name }}</td>
                    <td class="px-6 py-4 text-sm text-gray-900">{{ $item->quantity_ordered }}</td>
                    <td class="px-6 py-4 text-sm font-semibold {{ $item->quantity_received >= $item->quantity_ordered ? 'text-green-600' : 'text-orange-600' }}">
                        {{ $item->quantity_received }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-900">৳{{ number_format($item->unit_price, 2) }}</td>
                    <td class="px-6 py-4 text-sm font-semibold text-gray-900">৳{{ number_format($item->subtotal, 2) }}</td>
                    @if($purchaseOrder->status === 'RECEIVED')
                    <td class="px-6 py-4 text-sm text-gray-500">
                        {{ $item->batch_number }}<br>
                        <span class="text-xs">Exp: {{ $item->expiry_date ? $item->expiry_date->format('M d, Y') : '-' }}</span>
                    </td>
                    @endif
                </tr>
                @endforeach
            </tbody>
            <tfoot class="bg-gray-50">
                <tr><td colspan="{{ $purchaseOrder->status === 'RECEIVED' ? 4 : 3 }}" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Subtotal:</td>
                    <td colspan="2" class="px-6 py-3 text-sm font-semibold text-gray-900">৳{{ number_format($purchaseOrder->subtotal, 2) }}</td></tr>
                @if($purchaseOrder->shipping > 0)
                <tr><td colspan="{{ $purchaseOrder->status === 'RECEIVED' ? 4 : 3 }}" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Shipping:</td>
                    <td colspan="2" class="px-6 py-3 text-sm text-gray-900">৳{{ number_format($purchaseOrder->shipping, 2) }}</td></tr>
                @endif
                @if($purchaseOrder->tax > 0)
                <tr><td colspan="{{ $purchaseOrder->status === 'RECEIVED' ? 4 : 3 }}" class="px-6 py-3 text-right text-sm font-medium text-gray-700">Tax:</td>
                    <td colspan="2" class="px-6 py-3 text-sm text-gray-900">৳{{ number_format($purchaseOrder->tax, 2) }}</td></tr>
                @endif
                <tr><td colspan="{{ $purchaseOrder->status === 'RECEIVED' ? 4 : 3 }}" class="px-6 py-3 text-right text-lg font-bold text-gray-900">Total:</td>
                    <td colspan="2" class="px-6 py-3 text-lg font-bold text-green-600">৳{{ number_format($purchaseOrder->total, 2) }}</td></tr>
            </tfoot>
        </table>
    </div>
</div>
@endsection
