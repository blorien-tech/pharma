@extends('layouts.app')
@section('title', 'Inventory Report - BLORIEN Pharma')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('reports.inventory_title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('reports.inventory_desc') }}</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">← {{ __('common.back') }}</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.total_stock_value') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($totalInventoryValue, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('analytics.inventory_value') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-2">৳{{ number_format($totalRetailValue, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.gross_profit') }}</div>
            <div class="text-2xl font-bold text-blue-600 mt-2">৳{{ number_format($potentialProfit, 2) }}</div>
        </div>
    </div>

    @if($lowStockProducts->count() > 0)
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
        <strong>{{ __('common.warning') }}:</strong> {{ $lowStockProducts->count() }} {{ __('reports.low_stock_items') }}
    </div>
    @endif

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.total_products') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.quantity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.total') }}</th>
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
