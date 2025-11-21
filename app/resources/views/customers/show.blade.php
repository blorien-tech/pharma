@extends('layouts.app')

@section('title', $customer->name . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $customer->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('customers.customer_details') }}</p>
        </div>
        <div class="flex space-x-2">
            @if($customer->credit_enabled && $customer->current_balance > 0)
            <a href="{{ route('customers.payment', $customer) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                {{ __('customers.record_payment') }}
            </a>
            @endif
            <a href="{{ route('customers.adjustment', $customer) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                {{ __('customers.adjust_balance') }}
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                {{ __('customers.edit_customer') }}
            </a>
        </div>
    </div>

    <!-- Customer Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Credit Limit -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">{{ __('customers.credit_limit') }}</div>
            <div class="text-2xl font-bold text-gray-900">
                @if($customer->credit_enabled)
                    ৳{{ number_format($customer->credit_limit, 2) }}
                @else
                    <span class="text-gray-400">{{ __('common.not_enabled') }}</span>
                @endif
            </div>
        </div>

        <!-- Current Balance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">{{ __('customers.current_balance') }}</div>
            <div class="text-2xl font-bold {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                ৳{{ number_format($customer->current_balance, 2) }}
            </div>
        </div>

        <!-- Available Credit -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">{{ __('customers.available_credit') }}</div>
            <div class="text-2xl font-bold {{ $customer->availableCredit() > 0 ? 'text-green-600' : 'text-red-600' }}">
                @if($customer->credit_enabled)
                    ৳{{ number_format($customer->availableCredit(), 2) }}
                @else
                    <span class="text-gray-400">-</span>
                @endif
            </div>
        </div>

        <!-- Total Transactions -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">{{ __('transactions.total_transactions') }}</div>
            <div class="text-2xl font-bold text-gray-900">
                {{ $customer->transactions->count() }}
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if($customer->isOverdue())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <strong>{{ __('common.alert') }}:</strong> {{ __('alerts.customer_exceeds_credit_limit', ['balance' => '৳' . number_format($customer->current_balance, 2), 'limit' => '৳' . number_format($customer->credit_limit, 2)]) }}
    </div>
    @endif

    <!-- Customer Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('customers.contact_information') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.phone') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->phone }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.email') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?? __('common.na') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('common.id_number') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->id_number ?? __('common.na') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.city') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->city ?? __('common.na') }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.address') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->address ?? __('common.na') }}</p>
            </div>
            @if($customer->notes)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500">{{ __('common.notes') }}</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Credit Transaction History -->
    @if($customer->credit_enabled)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('transactions.credit_transaction_history') }}</h2>

        @if($creditHistory->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.amount') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.balance_before') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.balance_after') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.notes') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.created_by') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($creditHistory as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->created_at->format('M d, Y H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $transaction->type === 'SALE' ? 'bg-red-100 text-red-800' :
                                   ($transaction->type === 'PAYMENT' ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $transaction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                            {{ $transaction->type === 'PAYMENT' ? 'text-green-600' : 'text-red-600' }}">
                            {{ $transaction->type === 'PAYMENT' ? '-' : '+' }}৳{{ number_format(abs($transaction->amount), 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($transaction->balance_before, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($transaction->balance_after, 2) }}
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">
                            {{ $transaction->notes ?? '-' }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->creator->name }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($creditHistory->hasPages())
        <div class="mt-4">
            {{ $creditHistory->links() }}
        </div>
        @endif
        @else
        <p class="text-sm text-gray-500">{{ __('customers.no_transactions') }}</p>
        @endif
    </div>
    @endif

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('transactions.recent_transactions') }}</h2>

        @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.invoice_number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.items') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.payment_method') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.type') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentTransactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $transaction->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            <a href="{{ route('transactions.show', $transaction) }}">{{ $transaction->invoice_number }}</a>
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
                            <span class="ml-1 text-xs text-red-600">({{ __('transactions.credit') }})</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $transaction->type === 'SALE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $transaction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">{{ __('common.view') }}</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500">{{ __('customers.no_transactions') }}</p>
        @endif
    </div>
</div>
@endsection
