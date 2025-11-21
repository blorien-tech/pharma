@extends('layouts.app')

@section('title', 'Record Payment - ' . $customer->name)

@section('content')
<div class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('customers.record_payment') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('customers.record_payment') }} {{ __('common.from') }} {{ $customer->name }}</p>
    </div>

    <!-- Customer Balance Card -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('common.customer') }}</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">{{ $customer->name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.current_balance') }}</label>
                <p class="mt-1 text-lg font-semibold text-red-600">৳{{ number_format($customer->current_balance, 2) }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-500">{{ __('customers.credit_limit') }}</label>
                <p class="mt-1 text-lg font-semibold text-gray-900">৳{{ number_format($customer->credit_limit, 2) }}</p>
            </div>
        </div>
    </div>

    <form action="{{ route('customers.payment.store', $customer) }}" method="POST" class="space-y-6">
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
            <h2 class="text-lg font-semibold text-gray-900">{{ __('customers.payment_title') }}</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('customers.payment_amount') }} (৳) *</label>
                    <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                        max="{{ $customer->current_balance }}" required value="{{ old('amount') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    <p class="mt-1 text-xs text-gray-500">{{ __('common.maximum') }}: ৳{{ number_format($customer->current_balance, 2) }}</p>
                </div>

                <div>
                    <label for="payment_method" class="block text-sm font-medium text-gray-700">{{ __('customers.payment_method') }} *</label>
                    <select name="payment_method" id="payment_method" required
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                        <option value="">{{ __('common.select_method') }}</option>
                        <option value="CASH" {{ old('payment_method') === 'CASH' ? 'selected' : '' }}>{{ __('common.cash') }}</option>
                        <option value="CARD" {{ old('payment_method') === 'CARD' ? 'selected' : '' }}>{{ __('common.card') }}</option>
                        <option value="MOBILE" {{ old('payment_method') === 'MOBILE' ? 'selected' : '' }}>{{ __('common.mobile_payment') }}</option>
                        <option value="OTHER" {{ old('payment_method') === 'OTHER' ? 'selected' : '' }}>{{ __('common.other') }}</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('common.notes') }}</label>
                    <textarea name="notes" id="notes" rows="3"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{{ old('notes') }}</textarea>
                </div>
            </div>
        </div>

        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
            <p class="text-sm text-blue-800">
                <strong>{{ __('common.note') }}:</strong> {{ __('alerts.payment_reduce_balance') }} {{ __('alerts.recorded_credit_history') }}
            </p>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('customers.show', $customer) }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                {{ __('common.cancel') }}
            </a>
            <button type="submit" class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                {{ __('customers.record_payment') }}
            </button>
        </div>
    </form>
</div>
@endsection
