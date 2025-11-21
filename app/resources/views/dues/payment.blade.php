@extends('layouts.app')

@section('title', __('dues.make_payment') . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('dues.make_payment') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('dues.due_details') }} #{{ $due->id }}</p>
    </div>

    <!-- Customer & Due Info -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
        <div class="grid grid-cols-2 gap-4 mb-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('dues.customer_name') }}</label>
                <p class="text-lg font-semibold">{{ $due->customer_name }}</p>
                @if($due->customer_phone)
                <p class="text-sm text-gray-600">{{ $due->customer_phone }}</p>
                @endif
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('dues.remaining_amount') }}</label>
                <p class="text-2xl font-bold text-yellow-700">৳{{ number_format($due->amount_remaining, 2) }}</p>
            </div>
        </div>
        <div class="text-sm text-gray-600">
            <p>{{ __('common.total') }}: ৳{{ number_format($due->amount, 2) }} | {{ __('dues.paid_amount') }}: ৳{{ number_format($due->amount_paid, 2) }}</p>
        </div>
    </div>

    <!-- Payment Form -->
    <div class="bg-white rounded-lg shadow p-6">
        <form action="{{ route('dues.payment.store', $due) }}" method="POST" class="space-y-6">
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

            <!-- Payment Amount -->
            <div>
                <label for="amount" class="block text-sm font-medium text-gray-700">{{ __('dues.payment_amount') }} (৳) *</label>
                <input type="number" name="amount" id="amount" step="0.01" min="0.01"
                    max="{{ $due->amount_remaining }}"
                    value="{{ old('amount', $due->amount_remaining) }}"
                    required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-3 py-2 border text-lg font-semibold">
                <p class="mt-1 text-sm text-gray-500">Maximum: ৳{{ number_format($due->amount_remaining, 2) }}</p>

                <!-- Quick Amount Buttons -->
                <div class="mt-2 flex gap-2">
                    <button type="button"
                        onclick="document.getElementById('amount').value = ({{ $due->amount_remaining }} / 2).toFixed(2)"
                        class="px-3 py-1 bg-gray-200 hover:bg-gray-300 rounded text-sm">
                        Half (৳{{ number_format($due->amount_remaining / 2, 2) }})
                    </button>
                    <button type="button"
                        onclick="document.getElementById('amount').value = {{ $due->amount_remaining }}"
                        class="px-3 py-1 bg-green-200 hover:bg-green-300 rounded text-sm">
                        Full (৳{{ number_format($due->amount_remaining, 2) }})
                    </button>
                </div>
            </div>

            <!-- Payment Method -->
            <div>
                <label for="payment_method" class="block text-sm font-medium text-gray-700">{{ __('transactions.payment_method') }} *</label>
                <select name="payment_method" id="payment_method" required
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-3 py-2 border">
                    <option value="CASH" {{ old('payment_method') == 'CASH' ? 'selected' : '' }}>{{ __('transactions.cash') }}</option>
                    <option value="CARD" {{ old('payment_method') == 'CARD' ? 'selected' : '' }}>{{ __('transactions.card') }}</option>
                    <option value="MOBILE" {{ old('payment_method') == 'MOBILE' ? 'selected' : '' }}>{{ __('transactions.mobile_payment') }}</option>
                    <option value="OTHER" {{ old('payment_method') == 'OTHER' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Notes -->
            <div>
                <label for="notes" class="block text-sm font-medium text-gray-700">{{ __('common.notes') }} ({{ __('common.optional') }})</label>
                <textarea name="notes" id="notes" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-green-500 focus:ring-green-500 px-3 py-2 border">{{ old('notes') }}</textarea>
                <p class="mt-1 text-sm text-gray-500">{{ __('dues.notes') }}</p>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('dues.show', $due) }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                    class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ __('dues.record_payment') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Payment History Preview -->
    @if($due->payments->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-sm font-medium text-gray-700 mb-3">{{ __('dues.payment_history') }} ({{ $due->payments->count() }})</h3>
        <div class="space-y-2">
            @foreach($due->payments->take(3) as $payment)
            <div class="flex justify-between text-sm p-2 bg-gray-50 rounded">
                <span>৳{{ number_format($payment->amount, 2) }} - {{ $payment->payment_method }}</span>
                <span class="text-gray-500">{{ $payment->created_at->format('d M Y') }}</span>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>
@endsection
