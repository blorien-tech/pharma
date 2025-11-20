@extends('layouts.app')

@section('title', __('products.create_new_product') . ' - ' . __('navigation.app_name'))

@section('content')
<div class="max-w-2xl mx-auto space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">{{ __('products.create_new_product') }}</h1>
        <p class="mt-1 text-sm text-gray-600">{{ __('products.add_product_desc') }}</p>
    </div>

    <!-- Form -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <form action="{{ route('products.store') }}" method="POST" class="space-y-6">
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

            <!-- Product Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700">{{ __('products.product_name_label') }}</label>
                <input type="text" name="name" id="name" required
                    value="{{ old('name') }}"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                <p class="mt-1 text-sm text-gray-500">{{ __('products.full_product_name') }}</p>
            </div>

            <!-- Generic Name and Brand Name -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="generic_name" class="block text-sm font-medium text-gray-700">{{ __('products.generic_name') }}</label>
                    <input type="text" name="generic_name" id="generic_name"
                        value="{{ old('generic_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">{{ __('products.generic_example') }}</p>
                </div>
                <div>
                    <label for="brand_name" class="block text-sm font-medium text-gray-700">{{ __('products.brand_name') }}</label>
                    <input type="text" name="brand_name" id="brand_name"
                        value="{{ old('brand_name') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">{{ __('products.brand_example') }}</p>
                </div>
            </div>

            <!-- SKU and Barcode -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="sku" class="block text-sm font-medium text-gray-700">{{ __('products.sku_label') }}</label>
                    <input type="text" name="sku" id="sku" required
                        value="{{ old('sku') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">{{ __('products.unique_identifier') }}</p>
                </div>
                <div>
                    <label for="barcode" class="block text-sm font-medium text-gray-700">{{ __('products.barcode') }}</label>
                    <input type="text" name="barcode" id="barcode"
                        value="{{ old('barcode') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">{{ __('products.barcode_optional') }}</p>
                </div>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-sm font-medium text-gray-700">{{ __('products.description') }}</label>
                <textarea name="description" id="description" rows="3"
                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">{{ old('description') }}</textarea>
            </div>

            <!-- Pricing -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="purchase_price" class="block text-sm font-medium text-gray-700">{{ __('products.purchase_price_label') }}</label>
                    <input type="number" name="purchase_price" id="purchase_price" step="0.01" min="0" required
                        value="{{ old('purchase_price', '0.00') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
                <div>
                    <label for="selling_price" class="block text-sm font-medium text-gray-700">{{ __('products.selling_price_label') }}</label>
                    <input type="number" name="selling_price" id="selling_price" step="0.01" min="0" required
                        value="{{ old('selling_price', '0.00') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
            </div>

            <!-- Stock -->
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label for="current_stock" class="block text-sm font-medium text-gray-700">{{ __('products.current_stock_label') }}</label>
                    <input type="number" name="current_stock" id="current_stock" min="0" required
                        value="{{ old('current_stock', '0') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                </div>
                <div>
                    <label for="min_stock" class="block text-sm font-medium text-gray-700">{{ __('products.min_stock_label') }}</label>
                    <input type="number" name="min_stock" id="min_stock" min="0" required
                        value="{{ old('min_stock', '10') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 sm:text-sm px-3 py-2 border">
                    <p class="mt-1 text-sm text-gray-500">{{ __('products.low_stock_alert') }}</p>
                </div>
            </div>

            <!-- Status -->
            <div class="flex items-center">
                <input type="checkbox" name="is_active" id="is_active" checked
                    class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded">
                <label for="is_active" class="ml-2 block text-sm text-gray-900">
                    {{ __('products.active_for_sale') }}
                </label>
            </div>

            <!-- Actions -->
            <div class="flex justify-end space-x-3 pt-4 border-t">
                <a href="{{ route('products.index') }}"
                    class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                    {{ __('common.cancel') }}
                </a>
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                    {{ __('products.create_product') }}
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
