@extends('layouts.app')

@section('title', 'Sales Report - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('reports.sales_title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('reports.sales_desc') }}</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
            ← {{ __('common.back') }}
        </a>
    </div>

    <!-- Date Filter -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('reports.sales') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
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
            <div class="text-sm font-medium text-gray-500">{{ __('reports.total_sales') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($totalSales, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.total_transactions') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalTransactions }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('reports.average_sale') }}</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($averageTransactionValue, 2) }}</div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">{{ __('common.discount') }}</div>
            <div class="text-2xl font-bold text-red-600 mt-2">৳{{ number_format($totalDiscount, 2) }}</div>
        </div>
    </div>

    <!-- Sales by Date -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.sales_by_day') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.total_transactions') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('reports.total_sales') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($salesByDate as $daySales)
                    <tr>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $daySales['date'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $daySales['transactions'] }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">৳{{ number_format($daySales['total'], 2) }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('reports.no_data_available') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Payment Methods -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.payment_methods') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($paymentMethods as $method => $data)
            <div class="border rounded-lg p-4">
                <div class="text-sm font-medium text-gray-500">{{ $method }}</div>
                <div class="text-xl font-bold text-gray-900 mt-1">৳{{ number_format($data['total'], 2) }}</div>
                <div class="text-xs text-gray-500 mt-1">{{ $data['count'] }} {{ __('reports.total_transactions') }}</div>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Transaction List -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('reports.total_transactions') }}</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.items') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($transactions->take(50) as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            <a href="{{ route('transactions.show', $transaction) }}">{{ $transaction->invoice_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            @if($transaction->customer)
                                <a href="{{ route('customers.show', $transaction->customer) }}" class="hover:text-blue-600">
                                    {{ $transaction->customer->name }}
                                </a>
                            @else
                                Walk-in
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->items->count() }} {{ __('common.items') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                            ৳{{ number_format($transaction->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->payment_method }}
                            @if($transaction->is_credit)
                            <span class="ml-1 text-xs text-red-600">(Credit)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">{{ __('common.view') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('common.no_results') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
