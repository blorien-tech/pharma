@extends('layouts.app')

@section('title', $supplier->name . ' - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Header with Actions -->
    <div class="flex justify-between items-start">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">{{ $supplier->name }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $supplier->company_name ?? __('suppliers.supplier_details') }}</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('suppliers.edit', $supplier) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
                {{ __('suppliers.edit_supplier') }}
            </a>
            <a href="{{ route('suppliers.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-4 py-2 rounded-lg font-medium">
                {{ __('common.back_to_list') }}
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('common.total_products') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_products'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('suppliers.total_spent') }}</p>
            <p class="text-3xl font-bold text-green-600">৳{{ number_format($stats['total_spent'], 2) }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('suppliers.pending_orders') }}</p>
            <p class="text-3xl font-bold text-orange-600">{{ $stats['pending_orders'] }}</p>
        </div>
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('suppliers.total_orders') }}</p>
            <p class="text-3xl font-bold text-gray-900">{{ $stats['total_orders'] }}</p>
        </div>
    </div>

    <!-- Supplier Information -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('suppliers.supplier_information') }}</h2>
        <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('suppliers.contact_person') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->name }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('suppliers.company_name') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->company_name ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('suppliers.email') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->email ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('suppliers.phone') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->phone }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('suppliers.address') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->address ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('common.city_country') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">
                    {{ $supplier->city ? $supplier->city . ', ' : '' }}{{ $supplier->country ?? '-' }}
                </dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('common.tax_id') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->tax_id ?? '-' }}</dd>
            </div>
            <div>
                <dt class="text-sm font-medium text-gray-500">{{ __('common.status') }}</dt>
                <dd class="mt-1">
                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                        {{ $supplier->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $supplier->is_active ? __('suppliers.active') : __('suppliers.inactive') }}
                    </span>
                </dd>
            </div>
            @if($supplier->notes)
            <div class="md:col-span-2">
                <dt class="text-sm font-medium text-gray-500">{{ __('common.notes') }}</dt>
                <dd class="mt-1 text-sm text-gray-900">{{ $supplier->notes }}</dd>
            </div>
            @endif
        </dl>
    </div>

    <!-- Recent Purchase Orders -->
    @if($supplier->purchaseOrders->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200 flex justify-between items-center">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('suppliers.purchase_orders') }}</h2>
            <a href="{{ route('purchase-orders.index', ['supplier' => $supplier->id]) }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                {{ __('common.view_all') }} →
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.po_number') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.date') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.total') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.status') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplier->purchaseOrders as $order)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                            <a href="{{ route('purchase-orders.show', $order) }}">{{ $order->po_number }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ $order->order_date->format('M d, Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            ৳{{ number_format($order->total, 2) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                                {{ $order->status === 'RECEIVED' ? 'bg-green-100 text-green-800' :
                                   ($order->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                                   ($order->status === 'ORDERED' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                                {{ $order->status }}
                            </span>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Products from this Supplier -->
    @if($supplier->products->count() > 0)
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('suppliers.supplied_products') }} ({{ $supplier->products->count() }})</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.name') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.sku') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.stock') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">{{ __('common.price') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($supplier->products as $product)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 text-sm font-medium text-blue-600">
                            <a href="{{ route('products.edit', $product) }}">{{ $product->name }}</a>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $product->sku }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $product->current_stock }}</td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">৳{{ number_format($product->selling_price, 2) }}</td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif
</div>
@endsection
