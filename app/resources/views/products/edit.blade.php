@extends('layouts.app')

@section('title', 'Edit Product - BLORIEN Pharma')

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Edit Product</h1>
        <p class="mt-1 text-sm text-gray-600">Update product information</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('products.update', $product) }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @if($errors->any())
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                <ul class="list-disc list-inside">
                    @foreach($errors->all() as $error)
                    <li class="text-sm">{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">Product Name *</label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name', $product->name) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
            </div>

            <!-- SKU -->
            <div>
                <label for="sku" class="block text-sm font-medium text-gray-700">SKU (Stock Keeping Unit) *</label>
                <input type="text" name="sku" id="sku" required
                    value="{{ old('sku', $product->sku) }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                <p class="mt-1 text-sm text-gray-500">Unique product identifier</p>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">Description</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ old('description', $product->description) }}</textarea>
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">Purchase Price (৳) *</label>
                    <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0" required
                        value="{{ old('purchase_price', $product->purchase_price) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700">Selling Price (৳) *</label>
                    <input type="number" name="selling_price" id="selling_price" step="0.01" min="0" required
                        value="{{ old('selling_price', $product->selling_price) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
            </div>

            <!-- Stock -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="current_stock" class="block text-sm font-medium text-gray-700">Current Stock *</label>
                    <input type="number" name="current_stock" id="current_stock" min="0" required
                        value="{{ old('current_stock', $product->current_stock) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700">Minimum Stock Alert *</label>
                    <input type="number" name="min_stock" id="min_stock" min="0" required
                        value="{{ old('min_stock', $product->min_stock) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">Alert when stock falls below this</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active"
                    {{ old('is_active', $product->is_active) ? 'checked' : '' }}
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    Active (available for sale)
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('products.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                    Cancel
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    Update Product
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
