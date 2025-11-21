@extends('layouts.app')

@section('title', __('dues.due_details') . ' - BLORIEN Pharma')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('dues.due_details') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('dues.due_details') }} #{{ $due->id }}</p>
        </div>
        <a href="{{ route('dues.index') }}" class="text-blue-600 hover:text-blue-800">
            ← {{ __('common.back') }}
        </a>
    </div>

    <!-- Status Badge -->
    <div>
        @if($due->isPaid())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                ✓ {{ __('dues.paid') }}
            </span>
        @elseif($due->isOverdue())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                ! {{ __('dues.overdue') }}
            </span>
        @elseif($due->isPartial())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                {{ __('dues.partially_paid') }}
            </span>
        @else
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                {{ __('dues.pending') }}
            </span>
        @endif
    </div>

    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('dues.due_information') }}</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('common.name') }}</label>
                <p class="mt-1 text-lg font-medium">{{ $due->customer_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('common.phone') }}</label>
                <p class="mt-1 text-lg">{{ $due->customer_phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Due Amount Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.amount') }}</h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">{{ __('dues.total_amount') }}:</span>
                <span class="text-xl font-bold">৳{{ number_format($due->amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-green-600">
                <span>{{ __('dues.paid_amount') }}:</span>
                <span class="font-semibold">৳{{ number_format($due->amount_paid, 2) }}</span>
            </div>
            <div class="pt-3 border-t flex justify-between">
                <span class="font-medium {{ $due->amount_remaining > 0 ? 'text-yellow-700' : 'text-green-600' }}">
                    {{ __('dues.remaining_amount') }}:
                </span>
                <span class="text-2xl font-bold {{ $due->amount_remaining > 0 ? 'text-yellow-700' : 'text-green-600' }}">
                    ৳{{ number_format($due->amount_remaining, 2) }}
                </span>
            </div>
        </div>

        @if(!$due->isPaid())
        <div class="mt-6 pt-6 border-t">
            <a href="{{ route('dues.payment', $due) }}"
                class="block w-full text-center bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-semibold">
                {{ __('dues.record_payment') }}
            </a>
        </div>
        @endif
    </div>

    <!-- Dates -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.date') }}</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('dues.created_date') }}</label>
                <p class="mt-1">{{ $due->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('dues.due_date') }}</label>
                <p class="mt-1">{{ $due->due_date ? $due->due_date->format('d M Y') : 'N/A' }}</p>
            </div>
            @if($due->paid_at)
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('dues.payment_date') }}</label>
                <p class="mt-1 text-green-600 font-medium">{{ $due->paid_at->format('d M Y, h:i A') }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-600">{{ __('common.name') }}</label>
                <p class="mt-1">{{ $due->user->name }}</p>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($due->payments->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('dues.payment_history') }} ({{ $due->payments->count() }})</h2>
        <div class="space-y-3">
            @foreach($due->payments as $payment)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium">৳{{ number_format($payment->amount, 2) }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $payment->payment_method }} - {{ $payment->created_at->format('d M Y, h:i A') }}
                    </p>
                    <p class="text-xs text-gray-500">{{ $payment->user->name }}</p>
                    @if($payment->notes)
                    <p class="text-xs text-gray-600 mt-1">{{ __('common.notes') }}: {{ $payment->notes }}</p>
                    @endif
                </div>
                <div class="text-green-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Notes -->
    @if($due->notes)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('common.notes') }}</h2>
        <p class="text-gray-700">{{ $due->notes }}</p>
    </div>
    @endif

    <!-- Related Transaction -->
    @if($due->transaction)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">{{ __('transactions.transaction_details') }}</h2>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600">{{ __('transactions.transaction_id') }}</p>
                <p class="font-medium">#{{ $due->transaction->id }}</p>
                <p class="text-sm text-gray-500">{{ $due->transaction->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <a href="{{ route('transactions.show', $due->transaction) }}"
                class="text-blue-600 hover:text-blue-800 font-medium">
                {{ __('common.view') }} →
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
