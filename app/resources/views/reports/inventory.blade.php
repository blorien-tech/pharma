@extends('layouts.app')
@section('title', 'Inventory Report - BLORIEN Pharma')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Inventory Report</h1>
            <p class="mt-1 text-sm text-gray-600">Current stock levels and valuation</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">← Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Inventory Value (Cost)</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($totalInventoryValue, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Retail Value</div>
            <div class="text-2xl font-bold text-green-600 mt-2">৳{{ number_format($totalRetailValue, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Potential Profit</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">৳{{ number_format($potentialProfit, 2) }}</div>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <strong>Warning:</strong> {{ $lowStockProducts->count() }} products are low on stock
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Products by Value</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Purchase Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Selling Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Value</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productsByValue as $product)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $product['name'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $product['sku'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $product['stock'] }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">৳{{ number_format($product['purchase_price'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">৳{{ number_format($product['selling_price'], 2) }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">৳{{ number_format($product['total_value'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
