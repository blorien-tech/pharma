@extends('layouts.app')

@section('title', 'Due Details - BLORIEN Pharma')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Due Details</h1>
            <p class="mt-1 text-sm text-gray-600">Due #{{ $due->id }}</p>
        </div>
        <a href="{{ route('dues.index') }}" class="text-blue-600 hover:text-blue-800">
            ← Back to Dues
        </a>
    </div>

    <!-- Status Badge -->
    <div>
        @if($due->isPaid())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                ✓ Fully Paid
            </span>
        @elseif($due->isOverdue())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                ! Overdue
            </span>
        @elseif($due->isPartial())
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                Partially Paid
            </span>
        @else
            <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">
                Pending
            </span>
        @endif
    </div>

    <!-- Customer Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Customer Information</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Name</label>
                <p class="mt-1 text-lg font-medium">{{ $due->customer_name }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Phone</label>
                <p class="mt-1 text-lg">{{ $due->customer_phone ?? 'N/A' }}</p>
            </div>
        </div>
    </div>

    <!-- Due Amount Summary -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Amount Summary</h2>
        <div class="space-y-3">
            <div class="flex justify-between">
                <span class="text-gray-600">Total Amount:</span>
                <span class="text-xl font-bold">৳{{ number_format($due->amount, 2) }}</span>
            </div>
            <div class="flex justify-between text-green-600">
                <span>Amount Paid:</span>
                <span class="font-semibold">৳{{ number_format($due->amount_paid, 2) }}</span>
            </div>
            <div class="pt-3 border-t flex justify-between">
                <span class="font-medium {{ $due->amount_remaining > 0 ? 'text-yellow-700' : 'text-green-600' }}">
                    Amount Remaining:
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
                Collect Payment
            </a>
        </div>
        @endif
    </div>

    <!-- Dates -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Important Dates</h2>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-600">Recorded On</label>
                <p class="mt-1">{{ $due->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-600">Due Date</label>
                <p class="mt-1">{{ $due->due_date ? $due->due_date->format('d M Y') : 'Not specified' }}</p>
            </div>
            @if($due->paid_at)
            <div>
                <label class="block text-sm font-medium text-gray-600">Fully Paid On</label>
                <p class="mt-1 text-green-600 font-medium">{{ $due->paid_at->format('d M Y, h:i A') }}</p>
            </div>
            @endif
            <div>
                <label class="block text-sm font-medium text-gray-600">Recorded By</label>
                <p class="mt-1">{{ $due->user->name }}</p>
            </div>
        </div>
    </div>

    <!-- Payment History -->
    @if($due->payments->count() > 0)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Payment History ({{ $due->payments->count() }})</h2>
        <div class="space-y-3">
            @foreach($due->payments as $payment)
            <div class="flex justify-between items-center p-3 bg-gray-50 rounded">
                <div>
                    <p class="font-medium">৳{{ number_format($payment->amount, 2) }}</p>
                    <p class="text-sm text-gray-600">
                        {{ $payment->payment_method }} - {{ $payment->created_at->format('d M Y, h:i A') }}
                    </p>
                    <p class="text-xs text-gray-500">Collected by: {{ $payment->user->name }}</p>
                    @if($payment->notes)
                    <p class="text-xs text-gray-600 mt-1">Note: {{ $payment->notes }}</p>
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
        <h2 class="text-lg font-semibold mb-4">Notes</h2>
        <p class="text-gray-700">{{ $due->notes }}</p>
    </div>
    @endif

    <!-- Related Transaction -->
    @if($due->transaction)
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold mb-4">Related Transaction</h2>
        <div class="flex justify-between items-center">
            <div>
                <p class="text-sm text-gray-600">Transaction ID</p>
                <p class="font-medium">#{{ $due->transaction->id }}</p>
                <p class="text-sm text-gray-500">{{ $due->transaction->created_at->format('d M Y, h:i A') }}</p>
            </div>
            <a href="{{ route('transactions.show', $due->transaction) }}"
                class="text-blue-600 hover:text-blue-800 font-medium">
                View Transaction →
            </a>
        </div>
    </div>
    @endif
</div>
@endsection
