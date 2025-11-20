@extends('layouts.app')

@section('title', __('dashboard.title') . ' - ' . __('navigation.app_name'))
@section('breadcrumb', __('dashboard.title'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('dashboard.title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('dashboard.welcome_back', ['name' => auth()->user()->name]) }}</p>
        </div>
        <div class="hidden lg:flex space-x-3">
            <a href="{{ route('pos.index') }}" class="btn-ripple inline-flex items-center px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg transition shadow-sm">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                {{ __('dashboard.new_sale') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 lg:gap-6">
        <!-- Today's Sales -->
        <a href="{{ route('transactions.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover transition-all hover:shadow-md hover:border-green-300 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-green-500 to-green-600 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">{{ __('dashboard.todays_sales') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ __('common.currency_symbol') }}{{ number_format($todaySales, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ __('dashboard.transactions_count', ['count' => $todayTransactions]) }}</p>
        </a>

        <!-- Pending Dues -->
        <a href="{{ route('dues.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover transition-all hover:shadow-md hover:border-yellow-300 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">{{ __('dashboard.pending_dues_bangla') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ __('common.currency_symbol') }}{{ number_format($totalPendingDues, 2) }}</p>
            <p class="text-xs text-gray-500 mt-2">
                {{ __('dashboard.dues_count', ['count' => $pendingDuesCount]) }}
                @if($overdueDuesCount > 0)
                <span class="text-red-600 font-medium">{{ __('dashboard.overdue_count', ['count' => $overdueDuesCount]) }}</span>
                @endif
            </p>
        </a>

        <!-- Total Products -->
        <a href="{{ route('products.index') }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover transition-all hover:shadow-md hover:border-blue-300 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-blue-500 to-blue-600 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">{{ __('dashboard.total_products') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $totalProducts }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ __('dashboard.active_items') }}</p>
        </a>

        <!-- Low Stock Alert -->
        <a href="{{ route('alerts') }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover transition-all hover:shadow-md hover:border-orange-300 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-orange-500 to-orange-600 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">{{ __('dashboard.low_stock_alert') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $lowStockProducts }}</p>
            <p class="text-xs text-orange-600 hover:text-orange-700 font-medium mt-2">
                {{ __('dashboard.products_need_restock') }} →
            </p>
        </a>

        <!-- Expiring Batches -->
        <a href="{{ route('alerts') }}" class="block bg-white rounded-xl shadow-sm border border-gray-200 p-6 card-hover transition-all hover:shadow-md hover:border-red-300 cursor-pointer">
            <div class="flex items-center justify-between mb-4">
                <div class="bg-gradient-to-br from-red-500 to-red-600 rounded-lg p-3">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                </div>
            </div>
            <p class="text-sm font-medium text-gray-600 mb-1">{{ __('dashboard.expiring_soon') }}</p>
            <p class="text-2xl font-bold text-gray-900">{{ $expiringSoonBatches }}</p>
            <p class="text-xs text-gray-500 mt-2">{{ __('dashboard.already_expired', ['count' => $expiredBatches]) }}</p>
        </a>
    </div>

    <!-- Recent Activity Section -->
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
        <!-- Recent Transactions -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-blue-50 to-white">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Transactions</h2>
                    @if($recentTransactions->count() > 0)
                    <a href="{{ route('transactions.index') }}" class="text-sm text-blue-600 hover:text-blue-700 font-medium">
                        View all →
                    </a>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentTransactions as $transaction)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">#{{ $transaction->id }}</td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $transaction->type === 'SALE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                    {{ $transaction->type }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">৳{{ number_format($transaction->total, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->payment_method }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $transaction->created_at->format('M d, H:i') }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No transactions yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Dues -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200 bg-gradient-to-r from-yellow-50 to-white">
                <div class="flex justify-between items-center">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Dues </h2>
                    @if($recentDues->count() > 0)
                    <a href="{{ route('dues.index') }}" class="text-sm text-yellow-600 hover:text-yellow-700 font-medium">
                        View all →
                    </a>
                    @endif
                </div>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @forelse($recentDues as $due)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $due->customer_name }}</div>
                                <div class="text-xs text-gray-500">{{ $due->customer_phone ?? 'N/A' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($due->total_amount, 2) }}</td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold {{ $due->remaining_amount > 0 ? 'text-yellow-600' : 'text-green-600' }}">
                                ৳{{ number_format($due->remaining_amount, 2) }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $due->status === 'PAID' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                                    {{ $due->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($due->status === 'PENDING')
                                <a href="{{ route('dues.payment', $due) }}" class="text-blue-600 hover:text-blue-700 font-medium">Collect →</a>
                                @else
                                <a href="{{ route('dues.show', $due) }}" class="text-gray-500 hover:text-gray-700">View</a>
                                @endif
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center">
                                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                                </svg>
                                <p class="mt-2 text-sm text-gray-500">No dues yet</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-6 border border-blue-100">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Quick Actions</h2>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            <a href="{{ route('pos.index') }}" class="bg-white hover:bg-blue-50 border-2 border-transparent hover:border-blue-600 rounded-lg shadow-sm p-6 text-center transition group">
                <div class="bg-blue-100 group-hover:bg-blue-600 rounded-lg p-3 w-16 h-16 mx-auto mb-3 transition flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.new_sale') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.new_sale_desc') }}</p>
            </a>

            <a href="{{ route('dues.index') }}" class="bg-white hover:bg-yellow-50 border-2 border-transparent hover:border-yellow-600 rounded-lg shadow-sm p-6 text-center transition group">
                <div class="bg-yellow-100 group-hover:bg-yellow-600 rounded-lg p-3 w-16 h-16 mx-auto mb-3 transition flex items-center justify-center">
                    <svg class="w-8 h-8 text-yellow-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.manage_dues') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.manage_dues_desc') }}</p>
            </a>

            <a href="{{ route('products.create') }}" class="bg-white hover:bg-green-50 border-2 border-transparent hover:border-green-600 rounded-lg shadow-sm p-6 text-center transition group">
                <div class="bg-green-100 group-hover:bg-green-600 rounded-lg p-3 w-16 h-16 mx-auto mb-3 transition flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.add_product') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.add_product_desc') }}</p>
            </a>

            <a href="{{ route('daily-closing.index') }}" class="bg-white hover:bg-purple-50 border-2 border-transparent hover:border-purple-600 rounded-lg shadow-sm p-6 text-center transition group">
                <div class="bg-purple-100 group-hover:bg-purple-600 rounded-lg p-3 w-16 h-16 mx-auto mb-3 transition flex items-center justify-center">
                    <svg class="w-8 h-8 text-purple-600 group-hover:text-white transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-900">{{ __('dashboard.daily_closing') }}</h3>
                <p class="text-xs text-gray-500 mt-1">{{ __('dashboard.daily_closing_desc') }}</p>
            </a>
        </div>
    </div>
</div>
@endsection
