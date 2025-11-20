@extends('layouts.app')

@section('title', 'Customers - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Customers</h1>
            <p class="mt-1 text-sm text-gray-600">Manage customer information and credit accounts</p>
        </div>
        <a href="{{ route('customers.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            + Add Customer
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('customers.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Search</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Name, phone, email..."
                    class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Credit Status</label>
                <select name="credit_enabled" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Customers</option>
                    <option value="1" {{ request('credit_enabled') === '1' ? 'selected' : '' }}>Credit Enabled</option>
                    <option value="0" {{ request('credit_enabled') === '0' ? 'selected' : '' }}>Cash Only</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="is_active" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All</option>
                    <option value="1" {{ request('is_active') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('is_active') === '0' ? 'selected' : '' }}>Inactive</option>
                </select>
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    Filter
                </button>
                @if(request()->hasAny(['search', 'credit_enabled', 'is_active']))
                <a href="{{ route('customers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Customers Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contact</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Credit Limit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Current Balance</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Available Credit</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($customers as $customer)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm font-medium text-blue-600">
                            <a href="{{ route('customers.show', $customer) }}">{{ $customer->name }}</a>
                        </div>
                        @if($customer->id_number)
                        <div class="text-xs text-gray-500">ID: {{ $customer->id_number }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <div>{{ $customer->phone }}</div>
                        @if($customer->email)
                        <div class="text-xs text-gray-500">{{ $customer->email }}</div>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($customer->credit_enabled)
                            ৳{{ number_format($customer->credit_limit, 2) }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($customer->credit_enabled)
                            <span class="{{ $customer->current_balance > 0 ? 'text-red-600 font-semibold' : 'text-gray-900' }}">
                                ৳{{ number_format($customer->current_balance, 2) }}
                            </span>
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm">
                        @if($customer->credit_enabled)
                            <span class="{{ $customer->availableCredit() > 0 ? 'text-green-600 font-semibold' : 'text-red-600' }}">
                                ৳{{ number_format($customer->availableCredit(), 2) }}
                            </span>
                        @else
                            <span class="text-gray-400">Cash Only</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $customer->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $customer->is_active ? 'Active' : 'Inactive' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('customers.show', $customer) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        <a href="{{ route('customers.edit', $customer) }}" class="text-yellow-600 hover:text-yellow-900">Edit</a>
                        @if($customer->credit_enabled && $customer->current_balance > 0)
                        <a href="{{ route('customers.payment', $customer) }}" class="text-green-600 hover:text-green-900">Payment</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">
                        No customers found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($customers->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $customers->links() }}
    </div>
    @endif
</div>
@endsection
