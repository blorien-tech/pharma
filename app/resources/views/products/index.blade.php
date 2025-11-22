@extends('layouts.app')

@section('title', __('products.title') . ' - ' . __('navigation.app_name'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ __('products.title') }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ __('products.subtitle') }}</p>
        </div>
        <div class="flex gap-3">
            <button @click="window.openQuickStockModal()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                {{ __('products.quick_stock_add') }}
            </button>
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                + {{ __('products.add_product') }}
            </a>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex gap-3">
            <input type="text" name="search" placeholder="{{ __('products.search_products') }}"
                value="{{ request('search') }}"
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                {{ __('products.search') }}
            </button>
            @if(request('search'))
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                {{ __('products.clear') }}
            </a>
            @endif
        </form>
    </div>

    <!-- Products Grid/Table -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($products as $product)
        <div class="bg-white rounded-lg shadow hover:shadow-md transition">
            <div class="p-6">
                <div class="flex justify-between items-start mb-4">
                    <div class="flex-1">
                        <h3 class="text-lg font-semibold text-gray-900">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-500 mt-1">SKU: {{ $product->sku }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs font-semibold rounded-full
                        {{ $product->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $product->is_active ? __('products.active') : __('products.inactive') }}
                    </span>
                </div>

                @if($product->description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $product->description }}</p>
                @endif

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('products.purchase_price') }}:</span>
                        <span class="font-medium">{{ __('common.currency_symbol') }}{{ number_format($product->purchase_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('products.selling_price') }}:</span>
                        <span class="font-medium text-green-600">{{ __('common.currency_symbol') }}{{ number_format($product->selling_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('products.current_stock') }}:</span>
                        <span class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->current_stock }} {{ __('products.units') }}
                            @if($product->isLowStock())
                            <span class="text-xs">{{ __('products.low') }}</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">{{ __('products.min_stock') }}:</span>
                        <span class="font-medium">{{ $product->min_stock }} {{ __('products.units') }}</span>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <a href="{{ route('products.edit', $product) }}" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-2 rounded text-center text-sm font-medium">
                        {{ __('products.edit') }}
                    </a>
                    <a href="{{ route('batches.index', $product) }}" class="flex-1 bg-green-50 hover:bg-green-100 text-green-600 px-3 py-2 rounded text-center text-sm font-medium">
                        {{ __('products.view_batches') }}
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1" onsubmit="return handleDeleteProduct(event, '{{ $product->name }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-sm font-medium">
                            {{ __('common.delete') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-span-full bg-white rounded-lg shadow p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
            </svg>
            <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('products.no_products') }}</h3>
            <p class="mt-1 text-sm text-gray-500">{{ __('products.get_started_creating') }}</p>
            <div class="mt-6">
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    + {{ __('products.add_product') }}
                </a>
            </div>
        </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($products->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $products->links() }}
    </div>
    @endif
</div>

@push('scripts')
<script>
// Handle product delete with custom modal
async function handleDeleteProduct(event, productName) {
    event.preventDefault();

    const confirmed = await showConfirm(
        `Are you sure you want to delete "${productName}"? This action cannot be undone and all associated data will be permanently removed.`,
        'Delete Product?'
    );

    if (confirmed) {
        event.target.submit();
    }

    return false;
}
</script>
@endpush

@endsection
