@extends('layouts.app')

@section('title', $customer->name . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $customer->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">Customer Details</p>
        </div>
        <div class="flex space-x-2">
            @if($customer->credit_enabled && $customer->current_balance > 0)
            <a href="{{ route('customers.payment', $customer) }}" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                Record Payment
            </a>
            @endif
            <a href="{{ route('customers.adjustment', $customer) }}" class="bg-yellow-600 hover:bg-yellow-700 text-white px-4 py-2 rounded-lg font-medium">
                Adjust Balance
            </a>
            <a href="{{ route('customers.edit', $customer) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                Edit Customer
            </a>
        </div>
    </div>

    <!-- Customer Information Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <!-- Credit Limit -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Credit Limit</div>
            <div class="text-2xl font-bold text-gray-900">
                @if($customer->credit_enabled)
                    ৳{{ number_format($customer->credit_limit, 2) }}
                @else
                    <span class="text-gray-400">Not Enabled</span>
                @endif
            </div>
        </div>

        <!-- Current Balance -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Current Balance</div>
            <div class="text-2xl font-bold {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                ৳{{ number_format($customer->current_balance, 2) }}
            </div>
        </div>

        <!-- Available Credit -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="text-sm font-medium text-gray-500 mb-1">Available Credit</div>
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
            <div class="text-sm font-medium text-gray-500 mb-1">Total Transactions</div>
            <div class="text-2xl font-bold text-gray-900">
                {{ $customer->transactions->count() }}
            </div>
        </div>
    </div>

    <!-- Status Alerts -->
    @if($customer->isOverdue())
    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
        <strong>Alert:</strong> Customer balance exceeds credit limit. Current balance: ৳{{ number_format($customer->current_balance, 2) }}, Limit: ৳{{ number_format($customer->credit_limit, 2) }}
    </div>
    @endif

    <!-- Customer Details -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Contact Information</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Phone</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->phone }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Email</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->email ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">ID Number</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->id_number ?? 'N/A' }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">City</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->city ?? 'N/A' }}</p>
            </div>
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500">Address</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->address ?? 'N/A' }}</p>
            </div>
            @if($customer->notes)
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-gray-500">Notes</label>
                <p class="mt-1 text-sm text-gray-900">{{ $customer->notes }}</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Credit Transaction History -->
    @if($customer->credit_enabled)
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Credit Transaction History</h2>

        @if($creditHistory->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance Before</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Balance After</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Notes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created By</th>
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
        <p class="text-sm text-gray-500">No credit transactions yet.</p>
        @endif
    </div>
    @endif

    <!-- Recent Transactions -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">Recent Transactions</h2>

        @if($recentTransactions->count() > 0)
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Invoice #</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Items</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Payment</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Type</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
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
                            {{ $transaction->items->count() }} items
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
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $transaction->type === 'SALE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $transaction->type }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @else
        <p class="text-sm text-gray-500">No transactions yet.</p>
        @endif
    </div>
</div>
@endsection
