@extends('layouts.app')
@section('title', 'Supplier Performance - BLORIEN Pharma')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Supplier Performance</h1>
            <p class="mt-1 text-sm text-gray-600">Purchase order and spending analysis</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">← Back</a>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date" name="start_date" value="{{ $startDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date" name="end_date" value="{{ $endDate }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Apply</button>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Total Spent</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">৳{{ number_format($totalSpent, 2) }}</div>
        </div>
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500">Total Orders</div>
            <div class="text-2xl font-bold text-gray-900 mt-2">{{ $totalOrders }}</div>
        </div>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Supplier Performance</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Supplier</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Orders</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Received</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Pending</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Total Spent</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($suppliers as $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-medium text-blue-600">
                            <a href="{{ route('suppliers.show', $item['supplier']) }}">{{ $item['supplier']->name }}</a>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item['total_orders'] }}</td>
                        <td class="px-6 py-4 text-sm text-green-600">{{ $item['received_orders'] }}</td>
                        <td class="px-6 py-4 text-sm text-yellow-600">{{ $item['pending_orders'] }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">৳{{ number_format($item['total_spent'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
