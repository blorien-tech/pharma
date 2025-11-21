@extends('layouts.app')

@section('title', __('dues.title') . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="bg-yellow-600 text-white rounded-lg shadow p-6">
        <h1 class="text-3xl font-bold">{{ __('dues.title') }}</h1>
        <p class="mt-1 opacity-90">{{ __('dues.subtitle') }}</p>
    </div>

    <!-- Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-medium text-gray-600">{{ __('dues.total_pending') }}</h3>
            <p class="text-2xl font-bold text-yellow-600">৳{{ number_format($summary['total_pending'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $summary['count_pending'] }} {{ __('dues.pending_dues') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-medium text-gray-600">{{ __('dues.overdue') }}</h3>
            <p class="text-2xl font-bold text-red-600">৳{{ number_format($summary['total_overdue'], 2) }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ $summary['count_overdue'] }} {{ __('dues.overdue') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-medium text-gray-600">{{ __('dues.partially_paid') }}</h3>
            <p class="text-2xl font-bold text-blue-600">{{ $summary['count_partial'] }}</p>
            <p class="text-xs text-gray-500 mt-1">{{ __('dues.partially_paid') }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-sm font-medium text-gray-600">Quick Stats</h3>
            <p class="text-sm text-gray-700 mt-1">
                <span class="font-semibold">{{ $dues->total() }}</span> total entries
            </p>
        </div>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('dues.index') }}" class="flex flex-wrap gap-3">
            <div class="flex-1 min-w-[200px]">
                <input type="text" name="search" value="{{ request('search') }}"
                    placeholder="{{ __('dues.customer_name') }} / {{ __('dues.customer_phone') }}..."
                    class="w-full px-3 py-2 border rounded-lg text-sm">
            </div>
            <div>
                <select name="status" class="px-3 py-2 border rounded-lg text-sm">
                    <option value="">{{ __('dues.all_status') }}</option>
                    <option value="PENDING" {{ request('status') == 'PENDING' ? 'selected' : '' }}>{{ __('dues.pending') }}</option>
                    <option value="PARTIAL" {{ request('status') == 'PARTIAL' ? 'selected' : '' }}>{{ __('dues.partially_paid') }}</option>
                    <option value="PAID" {{ request('status') == 'PAID' ? 'selected' : '' }}>{{ __('dues.paid') }}</option>
                    <option value="OVERDUE" {{ request('status') == 'OVERDUE' ? 'selected' : '' }}>{{ __('dues.overdue') }}</option>
                </select>
            </div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm">
                {{ __('common.filter') }}
            </button>
            <a href="{{ route('dues.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg text-sm">
                {{ __('common.clear') }}
            </a>
        </form>
    </div>

    <!-- Dues List -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('dues.customer_name') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.amount') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.status') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('dues.due_date') }}</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($dues as $due)
                <tr class="{{ $due->isOverdue() ? 'bg-red-50' : '' }}">
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div>
                            <div class="text-sm font-medium text-gray-900">{{ $due->customer_name }}</div>
                            @if($due->customer_phone)
                            <div class="text-sm text-gray-500">{{ $due->customer_phone }}</div>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="text-sm text-gray-900">৳{{ number_format($due->amount, 2) }}</div>
                        @if($due->amount_paid > 0)
                        <div class="text-xs text-green-600">{{ __('dues.paid_amount') }}: ৳{{ number_format($due->amount_paid, 2) }}</div>
                        @endif
                        <div class="text-xs font-medium text-yellow-700">{{ __('dues.remaining_amount') }}: ৳{{ number_format($due->amount_remaining, 2) }}</div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($due->isPaid())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">{{ __('dues.paid') }}</span>
                        @elseif($due->isOverdue())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">{{ __('dues.overdue') }}</span>
                        @elseif($due->isPartial())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">{{ __('dues.partially_paid') }}</span>
                        @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-yellow-100 text-yellow-800">{{ __('dues.pending') }}</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $due->created_at->format('d M Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($due->due_date)
                            {{ $due->due_date->format('d M Y') }}
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium space-x-2">
                        <a href="{{ route('dues.show', $due) }}" class="text-blue-600 hover:text-blue-900">{{ __('common.view') }}</a>
                        @if(!$due->isPaid())
                        <a href="{{ route('dues.payment', $due) }}" class="text-green-600 hover:text-green-900">{{ __('dues.record_payment') }}</a>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-12 text-center">
                        <div class="text-gray-400">
                            <svg class="mx-auto h-12 w-12 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                            <p class="text-lg font-medium">{{ __('dues.no_dues') }}</p>
                            <p class="text-sm mt-1">{{ __('dues.no_dues_desc') }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($dues->hasPages())
        <div class="bg-gray-50 px-6 py-4">
            {{ $dues->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
