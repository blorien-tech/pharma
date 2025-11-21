@extends('layouts.app')

@section('title', __('transactions.title') . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('transactions.title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('transactions.subtitle') }}</p>
        </div>
        <a href="{{ route('pos.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            + {{ __('transactions.sale') }}
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('transactions.index') }}" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.type') }}</label>
                <select name="type" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">{{ __('transactions.all_types') }}</option>
                    <option value="SALE" {{ request('type') === 'SALE' ? 'selected' : '' }}>{{ __('transactions.sale') }}</option>
                    <option value="RETURN" {{ request('type') === 'RETURN' ? 'selected' : '' }}>{{ __('transactions.return') }}</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('common.date') }}</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    {{ __('common.filter') }}
                </button>
                @if(request()->hasAny(['type', 'date']))
                <a href="{{ route('transactions.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    {{ __('common.clear') }}
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Transactions Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.transaction_id') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.type') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.total') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.payment_method') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('transactions.cashier') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.date') }}</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ __('common.actions') }}</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($transactions as $transaction)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                        #{{ $transaction->id }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $transaction->type === 'SALE' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                            {{ $transaction->type === 'SALE' ? __('transactions.sale') : __('transactions.return') }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold
                        {{ $transaction->type === 'SALE' ? 'text-green-600' : 'text-red-600' }}">
                        ৳{{ number_format(abs($transaction->total), 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->payment_method }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->user->name }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $transaction->created_at->format('M d, Y H:i') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('transactions.show', $transaction) }}" class="text-blue-600 hover:text-blue-900">
                            {{ __('common.view') }}
                        </a>
                        @if($transaction->type === 'SALE')
                        <button onclick="processReturn({{ $transaction->id }})" class="text-red-600 hover:text-red-900">
                            {{ __('transactions.return') }}
                        </button>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="px-6 py-4 text-center text-sm text-gray-500">{{ __('transactions.no_transactions') }}</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($transactions->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $transactions->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
async function processReturn(transactionId) {
    const confirmed = await showConfirm('This will process a return for all items in this transaction. This action cannot be undone.', 'Process Return?');
    if (!confirmed) {
        return;
    }

    try {
        const response = await fetch(`/api/transactions/${transactionId}/return`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });

        const data = await response.json();

        if (response.ok) {
            showSuccess('Return has been processed successfully! The page will reload.', 'Return Processed');
            setTimeout(() => window.location.reload(), 1500);
        } else {
            showError(data.message || 'Failed to process return. Please try again.', 'Return Failed');
        }
    } catch (error) {
        console.error('Return error:', error);
        showError('An error occurred while processing the return. Please try again.', 'Error');
    }
}
</script>
@endpush
@endsection
