@extends('layouts.app')

@section('title', 'Receive Stock - ' . $purchaseOrder->po_number)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Receive Stock - {{ $purchaseOrder->po_number }}</h1>
        <p class="mt-1 text-sm text-gray-600">Record received stock and update inventory</p>
    </div>

    <form action="{{ route('purchase-orders.receive.store', $purchaseOrder) }}" method="POST">
        @csrf

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 space-y-4">
            <h2 class="text-lg font-semibold text-gray-900">Supplier: {{ $purchaseOrder->supplier->name }}</h2>

            <div>
                <label for="received_date" class="block text-sm font-medium text-gray-700">Received Date *</label>
                <input type="date" name="received_date" id="received_date" required value="{{ date('Y-m-d') }}"
                    class="mt-1 block w-full md:w-64 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <h2 class="text-lg font-semibold text-gray-900 mb-4">Items to Receive</h2>

            <div class="space-y-6">
                @foreach($purchaseOrder->items as $index => $item)
                <div class="border rounded-lg p-4">
                    <input type="hidden" name="items[{{ $index }}][id]" value="{{ $item->id }}">

                    <h3 class="font-semibold text-gray-900 mb-3">{{ $item->product->name }}</h3>

                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Ordered Quantity</label>
                            <input type="text" value="{{ $item->quantity_ordered }}" readonly
                                class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 border text-sm">
                        </div>

                        <div>
                            <label for="quantity_{{ $index }}" class="block text-sm font-medium text-gray-700">Received Quantity *</label>
                            <input type="number" name="items[{{ $index }}][quantity_received]" id="quantity_{{ $index }}"
                                min="0" max="{{ $item->quantity_ordered }}" value="{{ $item->quantity_ordered }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                        </div>

                        <div>
                            <label for="batch_{{ $index }}" class="block text-sm font-medium text-gray-700">Batch Number *</label>
                            <input type="text" name="items[{{ $index }}][batch_number]" id="batch_{{ $index }}" required
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                        </div>

                        <div>
                            <label for="expiry_{{ $index }}" class="block text-sm font-medium text-gray-700">Expiry Date *</label>
                            <input type="date" name="items[{{ $index }}][expiry_date]" id="expiry_{{ $index }}" required
                                value="{{ date('Y-m-d', strtotime('+1 year')) }}"
                                min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
            <p class="text-sm text-yellow-800">
                <strong>Note:</strong> Receiving this stock will automatically update product inventory and create batches for expiry tracking.
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('purchase-orders.show', $purchaseOrder) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                Confirm Receipt & Update Inventory
            </button>
        </div>
    </form>
</div>
@endsection
