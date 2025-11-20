@extends('layouts.app')

@section('title', 'Profit Analysis - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Profit Analysis</h1>
            <p class="mt-1 text-sm text-gray-600">Revenue, cost, and profit breakdown</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
            ← Back to Reports
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('reports.profit') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    Apply Filter
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Total Revenue</div>
            <div class="text-2xl font-bold text-green-600 mt-2">৳{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Total Cost</div>
            <div class="text-2xl font-bold text-red-600 mt-2">৳{{ number_format($totalCost, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Net Profit</div>
            <div class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                ৳{{ number_format($totalProfit, 2) }}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Profit Margin</div>
            <div class="text-2xl font-bold {{ $profitMargin >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ number_format($profitMargin, 2) }}%
            </div>
        </div>
    </div>

    <!-- Profit Breakdown Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Profit Breakdown</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-600">{{ number_format(($totalRevenue / max($totalRevenue, 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">Revenue</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-red-600">{{ number_format(($totalCost / max($totalRevenue, 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">Cost</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalCost, 2) }}</div>
            </div>
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($profitMargin, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">Profit</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalProfit, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Profit by Product -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top Products by Profit</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Quantity Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Cost</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Profit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Margin</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($profitByProduct as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $product['name'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $product['quantity_sold'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($product['revenue'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($product['cost'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $product['profit'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            ৳{{ number_format($product['profit'], 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $product['margin'] >= 0 ? 'text-green-600' : 'text-red-600' }}">
                            {{ number_format($product['margin'], 2) }}%
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
