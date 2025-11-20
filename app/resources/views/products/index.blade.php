@extends('layouts.app')

@section('title', 'Products - BLORIEN Pharma')

@section('content')
<div class="space-y-6" x-data="productsPage()">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Products</h1>
            <p class="mt-1 text-sm text-gray-600">Manage your pharmacy inventory</p>
        </div>
        <div class="flex gap-3">
            <button @click="showQuickStockModal = true" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg font-medium">
                ⚡ Quick Add Stock
            </button>
            <a href="{{ route('products.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                + Add Product
            </a>
        </div>
    </div>

    <!-- Quick Stock Add Modal -->
    <div x-show="showQuickStockModal"
         x-cloak
         class="fixed inset-0 z-50 overflow-y-auto"
         style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <!-- Background overlay -->
            <div x-show="showQuickStockModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 @click="showQuickStockModal = false"
                 class="fixed inset-0 transition-opacity bg-gray-500 bg-opacity-75"></div>

            <!-- Modal panel -->
            <div x-show="showQuickStockModal"
                 x-transition:enter="ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">

                <form @submit.prevent="submitQuickStock()" class="bg-white">
                    <!-- Modal Header -->
                    <div class="bg-green-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <h3 class="text-lg font-medium text-white">⚡ Quick Add Stock</h3>
                            <button @click="showQuickStockModal = false" type="button" class="text-white hover:text-gray-200">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <p class="text-sm text-green-100 mt-1">Quickly add stock to existing products</p>
                    </div>

                    <!-- Modal Body -->
                    <div class="px-6 py-4 space-y-4">
                        <!-- Product Selection -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Product *</label>
                            <select x-model="quickStock.product_id" required
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <option value="">Select a product...</option>
                                @foreach(\App\Models\Product::where('is_active', true)->orderBy('name')->get() as $prod)
                                <option value="{{ $prod->id }}">{{ $prod->name }} (Current: {{ $prod->current_stock }})</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Quantity -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Quantity to Add *</label>
                            <input type="number" x-model="quickStock.quantity" required min="1"
                                   placeholder="Enter quantity"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>

                        <!-- Batch Number -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Batch Number *</label>
                            <input type="text" x-model="quickStock.batch_number" required
                                   placeholder="e.g., BATCH-2024-001"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>

                        <!-- Expiry Date -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Expiry Date *</label>
                            <input type="date" x-model="quickStock.expiry_date" required
                                   :min="new Date().toISOString().split('T')[0]"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                        </div>

                        <!-- Purchase Price (optional) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">Purchase Price (optional)</label>
                            <input type="number" x-model="quickStock.purchase_price" step="0.01" min="0"
                                   placeholder="Cost per unit"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            <p class="text-xs text-gray-500 mt-1">Leave blank to use product's default price</p>
                        </div>
                    </div>

                    <!-- Modal Footer -->
                    <div class="bg-gray-50 px-6 py-4 flex gap-3 justify-end">
                        <button @click="showQuickStockModal = false" type="button"
                                class="px-4 py-2 bg-gray-200 hover:bg-gray-300 text-gray-700 rounded-lg font-medium">
                            Cancel
                        </button>
                        <button type="submit" :disabled="processing"
                                :class="processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700'"
                                class="px-4 py-2 text-white rounded-lg font-medium">
                            <span x-show="!processing">Add Stock</span>
                            <span x-show="processing">Adding...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Success Toast -->
    <div x-show="showSuccessToast"
         x-transition:enter="ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-2"
         x-transition:enter-end="opacity-100 translate-y-0"
         x-transition:leave="ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         style="display: none;"
         class="fixed top-4 right-4 z-50 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
            </svg>
            <span x-text="successMessage"></span>
        </div>
    </div>

    <!-- Search Bar -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('products.index') }}" class="flex gap-3">
            <input type="text" name="search" placeholder="Search products by name, SKU, or description..."
                value="{{ request('search') }}"
                class="flex-1 rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                Search
            </button>
            @if(request('search'))
            <a href="{{ route('products.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                Clear
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
                        {{ $product->is_active ? 'Active' : 'Inactive' }}
                    </span>
                </div>

                @if($product->description)
                <p class="text-sm text-gray-600 mb-4 line-clamp-2">{{ $product->description }}</p>
                @endif

                <div class="space-y-2 mb-4">
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Purchase Price:</span>
                        <span class="font-medium">৳{{ number_format($product->purchase_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Selling Price:</span>
                        <span class="font-medium text-green-600">৳{{ number_format($product->selling_price, 2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Stock:</span>
                        <span class="font-medium {{ $product->isLowStock() ? 'text-red-600' : 'text-gray-900' }}">
                            {{ $product->current_stock }} units
                            @if($product->isLowStock())
                            <span class="text-xs">(Low!)</span>
                            @endif
                        </span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-gray-600">Min Stock:</span>
                        <span class="font-medium">{{ $product->min_stock }} units</span>
                    </div>
                </div>

                <div class="flex gap-2 pt-4 border-t">
                    <a href="{{ route('products.edit', $product) }}" class="flex-1 bg-blue-50 hover:bg-blue-100 text-blue-600 px-3 py-2 rounded text-center text-sm font-medium">
                        Edit
                    </a>
                    <a href="{{ route('batches.index', $product) }}" class="flex-1 bg-green-50 hover:bg-green-100 text-green-600 px-3 py-2 rounded text-center text-sm font-medium">
                        Batches
                    </a>
                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1" onsubmit="return confirm('Are you sure you want to delete this product?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-50 hover:bg-red-100 text-red-600 px-3 py-2 rounded text-sm font-medium">
                            Delete
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
            <h3 class="mt-2 text-sm font-medium text-gray-900">No products found</h3>
            <p class="mt-1 text-sm text-gray-500">Get started by creating a new product.</p>
            <div class="mt-6">
                <a href="{{ route('products.create') }}" class="inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                    + Add Product
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
document.addEventListener('alpine:init', () => {
    Alpine.data('productsPage', () => ({
        showQuickStockModal: false,
        processing: false,
        showSuccessToast: false,
        successMessage: '',
        quickStock: {
            product_id: '',
            quantity: '',
            batch_number: '',
            expiry_date: '',
            purchase_price: ''
        },

        async submitQuickStock() {
            if (this.processing) return;

            this.processing = true;

            try {
                const response = await fetch('/api/products/quick-stock', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(this.quickStock)
                });

                const data = await response.json();

                if (response.ok) {
                    // Success - show toast and reload
                    this.successMessage = data.message || 'Stock added successfully!';
                    this.showSuccessToast = true;
                    this.showQuickStockModal = false;

                    // Reset form
                    this.quickStock = {
                        product_id: '',
                        quantity: '',
                        batch_number: '',
                        expiry_date: '',
                        purchase_price: ''
                    };

                    // Hide toast after 3 seconds and reload
                    setTimeout(() => {
                        this.showSuccessToast = false;
                        window.location.reload();
                    }, 3000);
                } else {
                    alert(data.message || 'Error adding stock. Please try again.');
                }
            } catch (error) {
                console.error('Error:', error);
                alert('Error adding stock. Please try again.');
            } finally {
                this.processing = false;
            }
        }
    }));
});
</script>
@endpush

@endsection
