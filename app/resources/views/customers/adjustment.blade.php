@extends('layouts.app')

@section('title', 'Balance Adjustment - ' . $customer->name)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Balance Adjustment</h1>
        <p class="mt-1 text-sm text-gray-600">Adjust balance for {{ $customer->name }}</p>
    </div>

    <!-- Customer Balance Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">Customer</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $customer->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Current Balance</label>
                <p class="mt-1 text-lg font-semibold {{ $customer->current_balance > 0 ? 'text-red-600' : 'text-green-600' }}">
                    ৳{{ number_format($customer->current_balance, 2) }}
                </p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">Credit Limit</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">৳{{ number_format($customer->credit_limit, 2) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('customers.adjustment.store', $customer) }}" method="POST" class="space-y-6">
        @csrf

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                <li class="text-sm">{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Adjustment Details</h2>

            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                <p class="text-sm text-yellow-800">
                    <strong>Important:</strong> Balance adjustments should only be used for corrections or special circumstances.
                    Use positive values to increase balance (customer owes more), negative values to decrease balance (forgive debt).
                </p>
            </div>

            <div class="grid grid-cols-1 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">Adjustment Amount (৳) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" required value="{{ old('amount') }}"
                        placeholder="Use negative value to reduce balance"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    <p class="mt-1 text-xs text-gray-500">
                        Examples: +100 to add ৳100 to balance, -100 to subtract ৳100 from balance
                    </p>
                </div>

                <div>
                    <label for="notes" class="block text-sm font-medium text-gray-700">Reason for Adjustment *</label>
                    <textarea name="notes" id="notes" rows="4" required
                        placeholder="Provide a detailed reason for this adjustment..."
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{{ old('notes') }}</textarea>
                    <p class="mt-1 text-xs text-gray-500">This will be recorded in the audit log</p>
                </div>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-4">
            <p class="text-sm text-red-800">
                <strong>Warning:</strong> This action will permanently adjust the customer's balance. Make sure to document the reason thoroughly.
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('customers.show', $customer) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" class="bg-yellow-600 hover:bg-yellow-700 text-white px-6 py-2 rounded-lg font-medium">
                Apply Adjustment
            </button>
        </div>
    </form>
</div>
@endsection
