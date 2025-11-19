@extends('layouts.app')

@section('title', 'Reports - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="mt-1 text-sm text-gray-600">Access comprehensive business reports and insights</p>
    </div>

    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 flex items-center justify-between">
        <div>
            <h3 class="font-semibold text-blue-900">Looking for visual insights?</h3>
            <p class="text-sm text-blue-700">Check out our interactive Analytics Dashboard with charts and graphs</p>
        </div>
        <a href="{{ route('analytics.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
            View Analytics →
        </a>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        <!-- Sales Report -->
        <a href="{{ route('reports.sales') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Sales Report</h3>
                    <p class="mt-1 text-sm text-gray-600">View sales by date, payment method, and transaction details</p>
                </div>
            </div>
        </a>

        <!-- Profit Analysis -->
        <a href="{{ route('reports.profit') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Profit Analysis</h3>
                    <p class="mt-1 text-sm text-gray-600">Analyze profit margins and revenue vs. cost breakdown</p>
                </div>
            </div>
        </a>

        <!-- Inventory Report -->
        <a href="{{ route('reports.inventory') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Inventory Report</h3>
                    <p class="mt-1 text-sm text-gray-600">View inventory valuation and stock levels</p>
                </div>
            </div>
        </a>

        <!-- Top Products -->
        <a href="{{ route('reports.top-products') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Top Products</h3>
                    <p class="mt-1 text-sm text-gray-600">Best selling products by quantity and revenue</p>
                </div>
            </div>
        </a>

        <!-- Supplier Performance -->
        <a href="{{ route('reports.suppliers') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Supplier Performance</h3>
                    <p class="mt-1 text-sm text-gray-600">Analyze spending and order history by supplier</p>
                </div>
            </div>
        </a>

        <!-- Customer Credit -->
        <a href="{{ route('reports.customers') }}" class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
            <div class="flex items-start">
                <div class="flex-shrink-0">
                    <svg class="w-12 h-12 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <h3 class="text-lg font-semibold text-gray-900">Customer Credit</h3>
                    <p class="mt-1 text-sm text-gray-600">Monitor customer credit balances and utilization</p>
                </div>
            </div>
        </a>
    </div>

    <!-- Quick Stats -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mt-8">
        <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">Today's Sales</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format(\App\Models\Transaction::where('type', 'SALE')->whereDate('created_at', today())->sum('total'), 2) }}</div>
        </div>

        <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">This Month</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format(\App\Models\Transaction::where('type', 'SALE')->whereMonth('created_at', now()->month)->sum('total'), 2) }}</div>
        </div>

        <div class="bg-gradient-to-br from-purple-500 to-purple-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">Inventory Value</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format(\App\Models\Product::where('is_active', true)->get()->sum(fn($p) => $p->current_stock * $p->purchase_price), 2) }}</div>
        </div>

        <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg shadow-md p-6 text-white">
            <div class="text-sm font-medium opacity-90">Credit Outstanding</div>
            <div class="text-3xl font-bold mt-2">৳{{ number_format(\App\Models\Customer::sum('current_balance'), 2) }}</div>
        </div>
    </div>
</div>
@endsection
