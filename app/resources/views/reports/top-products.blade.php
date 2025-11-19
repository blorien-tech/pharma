@extends('layouts.app')
@section('title', 'Top Selling Products - BLORIEN Pharma')
@section('content')
<div class="space-y-6">
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Top Selling Products</h1>
            <p class="mt-1 text-sm text-gray-600">Best performers by quantity and revenue</p>
        </div>
        <a href="{{ route('reports.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">← Back</a>
    </div>

    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" class="flex gap-4">
            <select name="period" class="px-3 py-2 border rounded-lg">
                <option value="week" {{ $period === 'week' ? 'selected' : '' }}>This Week</option>
                <option value="month" {{ $period === 'month' ? 'selected' : '' }}>This Month</option>
                <option value="year" {{ $period === 'year' ? 'selected' : '' }}>This Year</option>
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg">Apply</button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Top 20 Products (Since {{ $startDate->format('M d, Y') }})</h2>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">#</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Quantity Sold</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Revenue</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Avg Price</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($topProducts as $index => $item)
                    <tr>
                        <td class="px-6 py-4 text-sm font-bold text-gray-900">{{ $index + 1 }}</td>
                        <td class="px-6 py-4 text-sm font-medium text-gray-900">{{ $item['product']->name }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">{{ $item['quantity_sold'] }}</td>
                        <td class="px-6 py-4 text-sm font-semibold text-green-600">৳{{ number_format($item['revenue'], 2) }}</td>
                        <td class="px-6 py-4 text-sm text-gray-900">৳{{ number_format($item['average_price'], 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
