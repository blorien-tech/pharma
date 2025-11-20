@extends('layouts.app')
@section('title', 'Customer Credit Report - BLORIEN Pharma')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Customer Credit Report</h1>
            <p class="mt-1 text-sm text-gray-600">Credit balances and utilization</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">← Back</a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Total Credit Limit</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($totalCreditLimit, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Outstanding Balance</div>
            <div class="text-2xl font-bold text-red-600 mt-2">৳{{ number_format($totalOutstanding, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Available Credit</div>
            <div class="text-2xl font-bold text-green-600 mt-2">৳{{ number_format($totalAvailable, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Overdue Customers</div>
            <div class="text-2xl font-bold text-red-600 mt-2">{{ $overdueCustomers->count() }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Customer Credit Status</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Customer</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Credit Limit</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Balance</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Available</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Utilization</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($customers as $item)
                    <tr class="{{ $item['is_overdue'] ? 'bg-red-50' : '' }}">
                        <td class="px-6 py-4 text-sm font-medium text-blue-600">
                            <a href="{{ route('customers.show', $item['customer']) }}">{{ $item['customer']->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">৳{{ number_format($item['credit_limit'], 2) }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-red-600">৳{{ number_format($item['current_balance'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-green-600">৳{{ number_format($item['available_credit'], 2) }}</td>
                        <td class="px-6 py-4 text-sm">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ min($item['utilization'], 100) }}%"></div>
                                </div>
                                <span>{{ number_format($item['utilization'], 1) }}%</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 text-sm">
                            @if($item['is_overdue'])
                            <span class="px-2 py-1 text-xs bg-red-100 text-red-800 rounded-full">Overdue</span>
                            @else
                            <span class="px-2 py-1 text-xs bg-green-100 text-green-800 rounded-full">Good</span>
                            @endif
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
