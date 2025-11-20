@extends('layouts.app')

@section('title', 'Daily Closing Summary - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center no-print">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Daily Closing Summary</h1>
            <p class="mt-1 text-sm text-gray-600">End of day report</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('dashboard') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                ← Back to Dashboard
            </a>
            <button onclick="window.print()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                🖨️ Print Summary
            </button>
        </div>
    </div>

    <!-- Date Selector -->
    <div class="bg-white rounded-lg shadow p-4 no-print">
        <form method="GET" action="{{ route('daily-closing.index') }}" class="flex gap-3 items-center">
            <label class="text-sm font-medium text-gray-700">Select Date:</label>
            <input type="date" name="date" value="{{ $summary['date']->toDateString() }}"
                   max="{{ today()->toDateString() }}"
                   class="px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                View
            </button>
        </form>
    </div>

    <!-- Print Header (only visible when printing) -->
    <div class="print-only text-center mb-8">
        <h1 class="text-2xl font-bold">BLORIEN Pharma</h1>
        <h2 class="text-xl font-semibold mt-2">Daily Closing Summary</h2>
        <p class="text-sm mt-1">{{ $summary['date']->format('l, F j, Y') }}</p>
        <p class="text-xs text-gray-600 mt-1">Printed on: {{ now()->format('d/m/Y h:i A') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <!-- Total Revenue -->
        <div class="bg-gradient-to-br from-blue-500 to-blue-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-sm font-medium opacity-90">Total Revenue</h3>
            <p class="text-3xl font-bold mt-2">৳{{ number_format($summary['total_revenue'], 2) }}</p>
            <p class="text-xs opacity-75 mt-2">Sales + Due Payments</p>
        </div>

        <!-- Total Sales -->
        <div class="bg-gradient-to-br from-green-500 to-green-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-sm font-medium opacity-90">Total Sales</h3>
            <p class="text-3xl font-bold mt-2">৳{{ number_format($summary['total_sales'], 2) }}</p>
            <p class="text-xs opacity-75 mt-2">{{ $summary['total_transactions'] }} transactions</p>
        </div>

        <!-- Cash in Hand -->
        <div class="bg-gradient-to-br from-yellow-500 to-yellow-600 text-white rounded-lg shadow-lg p-6">
            <h3 class="text-sm font-medium opacity-90">Cash in Hand</h3>
            <p class="text-3xl font-bold mt-2">৳{{ number_format($summary['total_cash_in_hand'], 2) }}</p>
            <p class="text-xs opacity-75 mt-2">Sales + Due Payments</p>
        </div>
    </div>

    <!-- Sales Breakdown -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="bg-gray-50 px-6 py-4 border-b">
            <h2 class="text-lg font-semibold text-gray-900">Sales Breakdown by Payment Method</h2>
        </div>
        <div class="p-6">
            <table class="min-w-full">
                <thead>
                    <tr class="border-b">
                        <th class="text-left py-2 text-sm font-medium text-gray-700">Payment Method</th>
                        <th class="text-right py-2 text-sm font-medium text-gray-700">Amount (৳)</th>
                        <th class="text-right py-2 text-sm font-medium text-gray-700">Percentage</th>
                    </tr>
                </thead>
                <tbody class="divide-y">
                    <tr>
                        <td class="py-3 text-sm">Cash</td>
                        <td class="py-3 text-sm text-right font-medium">{{ number_format($summary['cash_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right text-gray-600">
                            {{ $summary['total_sales'] > 0 ? number_format(($summary['cash_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-sm">Card</td>
                        <td class="py-3 text-sm text-right font-medium">{{ number_format($summary['card_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right text-gray-600">
                            {{ $summary['total_sales'] > 0 ? number_format(($summary['card_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-sm">Mobile Money (bKash/Nagad)</td>
                        <td class="py-3 text-sm text-right font-medium">{{ number_format($summary['mobile_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right text-gray-600">
                            {{ $summary['total_sales'] > 0 ? number_format(($summary['mobile_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-sm">Credit Sales</td>
                        <td class="py-3 text-sm text-right font-medium">{{ number_format($summary['credit_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right text-gray-600">
                            {{ $summary['total_sales'] > 0 ? number_format(($summary['credit_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr>
                        <td class="py-3 text-sm">Other</td>
                        <td class="py-3 text-sm text-right font-medium">{{ number_format($summary['other_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right text-gray-600">
                            {{ $summary['total_sales'] > 0 ? number_format(($summary['other_sales'] / $summary['total_sales']) * 100, 1) : 0 }}%
                        </td>
                    </tr>
                    <tr class="bg-gray-50 font-semibold">
                        <td class="py-3 text-sm">TOTAL</td>
                        <td class="py-3 text-sm text-right">৳{{ number_format($summary['total_sales'], 2) }}</td>
                        <td class="py-3 text-sm text-right">100%</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Dues Summary -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Dues Created -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-yellow-50 px-6 py-4 border-b border-yellow-200">
                <h2 class="text-lg font-semibold text-gray-900">Dues Created</h2>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-600">Number of Dues:</span>
                    <span class="text-2xl font-bold text-gray-900">{{ $summary['dues_created_count'] }}</span>
                </div>
                <div class="flex justify-between items-center">
                    <span class="text-sm text-gray-600">Total Amount:</span>
                    <span class="text-2xl font-bold text-yellow-600">৳{{ number_format($summary['total_dues_created'], 2) }}</span>
                </div>
            </div>
        </div>

        <!-- Due Payments Collected -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="bg-green-50 px-6 py-4 border-b border-green-200">
                <h2 class="text-lg font-semibold text-gray-900">Due Payments Collected</h2>
            </div>
            <div class="p-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-600">Number of Payments:</span>
                    <span class="text-2xl font-bold text-gray-900">{{ $summary['due_payments_count'] }}</span>
                </div>
                <div class="flex justify-between items-center mb-4">
                    <span class="text-sm text-gray-600">Total Collected:</span>
                    <span class="text-2xl font-bold text-green-600">৳{{ number_format($summary['total_due_payments_collected'], 2) }}</span>
                </div>
                <div class="pt-4 border-t space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Cash:</span>
                        <span class="font-medium">৳{{ number_format($summary['due_payments_by_cash'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Card:</span>
                        <span class="font-medium">৳{{ number_format($summary['due_payments_by_card'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Mobile:</span>
                        <span class="font-medium">৳{{ number_format($summary['due_payments_by_mobile'], 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Other:</span>
                        <span class="font-medium">৳{{ number_format($summary['due_payments_by_other'], 2) }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Print Footer -->
    <div class="print-only text-center text-xs text-gray-600 mt-8 pt-4 border-t">
        <p>This is a computer-generated summary. No signature required.</p>
        <p class="mt-1">BLORIEN Pharma Management System v2.5</p>
    </div>
</div>

<style>
@media print {
    body * {
        visibility: hidden;
    }
    .print-content, .print-content * {
        visibility: visible;
    }
    .no-print {
        display: none !important;
    }
    .print-only {
        display: block !important;
    }
    @page {
        margin: 1cm;
    }
}

.print-only {
    display: none;
}

@media print {
    .bg-gradient-to-br {
        background: #eee !important;
        color: #000 !important;
        -webkit-print-color-adjust: exact;
        print-color-adjust: exact;
    }
}
</style>
@endsection
