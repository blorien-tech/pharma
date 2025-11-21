@extends('layouts.app')

@section('title', 'Profit Analysis - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('reports.profit_title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('reports.profit_desc') }}</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
            ← {{ __('common.back') }}
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('reports.profit') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('reports.from_date') }}</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('reports.to_date') }}</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ __('reports.apply_filters') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.total_revenue') }}</div>
            <div class="text-2xl font-bold text-green-600 mt-2">৳{{ number_format($totalRevenue, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.total_cost') }}</div>
            <div class="text-2xl font-bold text-red-600 mt-2">৳{{ number_format($totalCost, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.net_profit') }}</div>
            <div class="text-2xl font-bold {{ $totalProfit >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                ৳{{ number_format($totalProfit, 2) }}
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.profit_margin') }}</div>
            <div class="text-2xl font-bold {{ $profitMargin >= 0 ? 'text-green-600' : 'text-red-600' }} mt-2">
                {{ number_format($profitMargin, 2) }}%
            </div>
        </div>
    </div>

    <!-- Profit Breakdown Chart -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.summary') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-green-600">{{ number_format(($totalRevenue / max($totalRevenue, 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">{{ __('analytics.revenue') }}</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalRevenue, 2) }}</div>
            </div>
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-red-600">{{ number_format(($totalCost / max($totalRevenue, 1)) * 100, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">{{ __('reports.total_cost') }}</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalCost, 2) }}</div>
            </div>
            <div class="border rounded-lg p-6 text-center">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($profitMargin, 1) }}%</div>
                <div class="text-sm text-gray-600 mt-2">{{ __('reports.gross_profit') }}</div>
                <div class="text-lg font-semibold text-gray-900 mt-1">৳{{ number_format($totalProfit, 2) }}</div>
            </div>
        </div>
    </div>

    <!-- Profit by Product -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.profit_by_product') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.quantity') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('analytics.revenue') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.total_cost') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.gross_profit') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.profit_margin') }}</th>
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
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('common.no_data') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
