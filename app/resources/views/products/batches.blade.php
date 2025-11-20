@extends('layouts.app')

@section('title', __('products.batches') . ' - ' . $product->name)

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $product->name }} - {{ __('products.batches') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('products.batches_subtitle') }}</p>
        </div>
        <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
            {{ __('products.back_to_products') }}
        </a>
    </div>

    <!-- Product Info Card -->
    <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <p class="text-sm text-blue-600 font-medium">{{ __('products.sku') }}</p>
                <p class="text-lg font-semibold text-blue-900">{{ $product->sku }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-600 font-medium">{{ __('products.current_stock') }}</p>
                <p class="text-lg font-semibold text-blue-900">{{ $product->current_stock }} {{ __('products.units') }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-600 font-medium">{{ __('products.selling_price') }}</p>
                <p class="text-lg font-semibold text-blue-900">{{ __('common.currency_symbol') }}{{ number_format($product->selling_price, 2) }}</p>
            </div>
            <div>
                <p class="text-sm text-blue-600 font-medium">{{ __('products.total_batches') }}</p>
                <p class="text-lg font-semibold text-blue-900">{{ $batches->total() }}</p>
            </div>
        </div>
    </div>

    <!-- Add Batch Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('products.add_new_batch') }}</h2>

        <form action="{{ route('batches.store', $product) }}" method="POST">
            @csrf

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <div>
                    <label for="batch_number" class="block text-sm font-medium text-gray-700">{{ __('products.batch_number_label') }}</label>
                    <input type="text" name="batch_number" id="batch_number" required
                        value="{{ old('batch_number') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>

                <div>
                    <label for="expiry_date" class="block text-sm font-medium text-gray-700">{{ __('products.expiry_date_label') }}</label>
                    <input type="date" name="expiry_date" id="expiry_date" required
                        value="{{ old('expiry_date') }}"
                        min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>

                <div>
                    <label for="quantity_received" class="block text-sm font-medium text-gray-700">{{ __('products.quantity') }} *</label>
                    <input type="number" name="quantity_received" id="quantity_received" min="1" required
                        value="{{ old('quantity_received') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>

                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">{{ __('products.purchase_price_label') }}</label>
                    <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0"
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
            </div>

            <div class="mt-4">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    {{ __('products.add_batch') }}
                </button>
            </div>
        </form>
    </div>

    <!-- Batches Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Existing Batches</h2>
        </div>

        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Received</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Purchase Price</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($batches as $batch)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ $batch->batch_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm
                            @if($batch->isExpired()) text-red-600 font-semibold
                            @elseif($batch->isExpiringSoon()) text-orange-600 font-semibold
                            @else text-gray-900 @endif">
                            {{ $batch->expiry_date->format('M d, Y') }}
                            @if($batch->isExpired())
                            <span class="text-xs">(Expired)</span>
                            @elseif($batch->isExpiringSoon())
                            <span class="text-xs">(Expiring Soon)</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $batch->quantity_received }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $batch->quantity_remaining }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($batch->purchase_price ?? 0, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($batch->isExpired())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                Expired
                            </span>
                            @elseif($batch->isExpiringSoon())
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-orange-100 text-orange-800">
                                Expiring Soon
                            </span>
                            @elseif($batch->quantity_remaining == 0)
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                Depleted
                            </span>
                            @else
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                Active
                            </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">No batches found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($batches->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $batches->links() }}
    </div>
    @endif
</div>
@endsection
