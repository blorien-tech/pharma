@extends('layouts.app')

@section('title', $location->getFullPath() . ' - ' . __('navigation.app_name'))

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-start">
        <div class="flex items-center gap-4">
            <a href="{{ route('locations.index') }}" class="text-gray-600 hover:text-gray-900">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
            </a>
            <div>
                <h1 class="text-3xl font-bold text-gray-900">{{ $location->name }}</h1>
                <p class="mt-1 text-sm text-gray-600">{{ $location->getFullPath() }}</p>
            </div>
        </div>
        @canany(['owner', 'manager'], App\Models\User::class)
        <a href="{{ route('locations.edit', $location) }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium flex items-center gap-2">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
            </svg>
            {{ __('common.edit') }}
        </a>
        @endcanany
    </div>

    <!-- Location Info Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
        <!-- Type -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('locations.type') }}</p>
            <p class="mt-2 text-2xl font-bold text-gray-900">{{ ucfirst(strtolower($location->type)) }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ __('locations.code') }}: {{ $location->code }}</p>
        </div>

        <!-- Capacity -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('locations.capacity') }}</p>
            @if($stats['capacity'])
                <p class="mt-2 text-2xl font-bold {{ $stats['is_full'] ? 'text-red-600' : 'text-green-600' }}">
                    {{ $stats['occupancy'] }}/{{ $stats['capacity'] }}
                </p>
                <div class="mt-2 h-2 bg-gray-200 rounded-full overflow-hidden">
                    <div class="h-full transition-all
                        {{ $stats['occupancy_percentage'] >= 100 ? 'bg-red-500' : '' }}
                        {{ $stats['occupancy_percentage'] >= 75 && $stats['occupancy_percentage'] < 100 ? 'bg-yellow-500' : '' }}
                        {{ $stats['occupancy_percentage'] < 75 ? 'bg-green-500' : '' }}"
                         style="width: {{ min($stats['occupancy_percentage'], 100) }}%">
                    </div>
                </div>
            @else
                <p class="mt-2 text-2xl font-bold text-gray-400">{{ __('locations.unlimited') }}</p>
                <p class="mt-1 text-xs text-gray-500">{{ __('locations.no_capacity_limit') }}</p>
            @endif
        </div>

        <!-- Total Batches -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('locations.total_batches') }}</p>
            <p class="mt-2 text-2xl font-bold text-indigo-600">{{ $stats['total_batches'] }}</p>
            <p class="mt-1 text-xs text-gray-500">{{ $stats['total_products'] }} {{ __('locations.unique_products') }}</p>
        </div>

        <!-- Status -->
        <div class="bg-white rounded-lg shadow p-6">
            <p class="text-sm font-medium text-gray-600">{{ __('locations.status') }}</p>
            <p class="mt-2 text-2xl font-bold {{ $location->is_active ? 'text-green-600' : 'text-red-600' }}">
                {{ $location->is_active ? __('locations.active') : __('locations.inactive') }}
            </p>
            @if($location->temperature_controlled)
                <p class="mt-1 text-xs text-cyan-600 font-medium">
                    {{ $location->temperature_min }}°C - {{ $location->temperature_max }}°C
                </p>
            @endif
        </div>
    </div>

    <!-- Alerts -->
    @if(!empty($alerts))
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('locations.alerts') }}</h2>
        <div class="space-y-2">
            @foreach($alerts as $alert)
                <div class="flex items-start p-3 rounded-lg
                    {{ $alert['severity'] === 'danger' ? 'bg-red-50 border border-red-200' : '' }}
                    {{ $alert['severity'] === 'warning' ? 'bg-yellow-50 border border-yellow-200' : '' }}
                    {{ $alert['severity'] === 'info' ? 'bg-blue-50 border border-blue-200' : '' }}">
                    <svg class="w-5 h-5 mr-2 flex-shrink-0
                        {{ $alert['severity'] === 'danger' ? 'text-red-600' : '' }}
                        {{ $alert['severity'] === 'warning' ? 'text-yellow-600' : '' }}
                        {{ $alert['severity'] === 'info' ? 'text-blue-600' : '' }}"
                         fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        @if($alert['severity'] === 'danger')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @elseif($alert['severity'] === 'warning')
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        @else
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        @endif
                    </svg>
                    <p class="text-sm
                        {{ $alert['severity'] === 'danger' ? 'text-red-700' : '' }}
                        {{ $alert['severity'] === 'warning' ? 'text-yellow-700' : '' }}
                        {{ $alert['severity'] === 'info' ? 'text-blue-700' : '' }}">
                        {{ $alert['message'] }}
                    </p>
                </div>
            @endforeach
        </div>
    </div>
    @endif

    <!-- Products in this Location -->
    @if($productsInLocation->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('locations.products_stored') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('products.product') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.batch_count') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.total_quantity') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.oldest_expiry') }}
                        </th>
                        <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('common.actions') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($productsInLocation as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $item['product']->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $item['product']->sku }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item['batch_count'] }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $item['total_quantity'] }} {{ __('products.units') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                @if($item['oldest_expiry'])
                                    @php
                                        $expiryDate = \Carbon\Carbon::parse($item['oldest_expiry']);
                                        $daysUntilExpiry = \Carbon\Carbon::today()->diffInDays($expiryDate, false);
                                    @endphp
                                    <span class="
                                        {{ $daysUntilExpiry < 0 ? 'text-red-600 font-semibold' : '' }}
                                        {{ $daysUntilExpiry >= 0 && $daysUntilExpiry <= 30 ? 'text-yellow-600 font-medium' : '' }}
                                        {{ $daysUntilExpiry > 30 ? 'text-gray-700' : '' }}">
                                        {{ $expiryDate->format('Y-m-d') }}
                                    </span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm">
                                <a href="{{ route('batches.index', $item['product']) }}" class="text-blue-600 hover:text-blue-800">
                                    {{ __('products.view_batches') }}
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="bg-white rounded-lg shadow p-12 text-center">
        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
        </svg>
        <h3 class="mt-2 text-sm font-medium text-gray-900">{{ __('locations.no_products_stored') }}</h3>
        <p class="mt-1 text-sm text-gray-500">{{ __('locations.no_products_desc') }}</p>
    </div>
    @endif

    <!-- Child Locations (if any) -->
    @if($location->children->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('locations.sub_locations') }}</h2>
        </div>
        <div class="p-6">
            @foreach($location->children as $child)
                @include('locations.partials.tree-item', ['location' => $child, 'level' => 0])
            @endforeach
        </div>
    </div>
    @endif

    <!-- Recent Stock Movements -->
    @if($recentMovements->isNotEmpty())
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">{{ __('locations.recent_movements') }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.date') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('products.product') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.from') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.to') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.reason') }}
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            {{ __('locations.quantity') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($recentMovements as $movement)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->created_at->format('Y-m-d H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div>
                                    <p class="font-medium text-gray-900">{{ $movement->batch->product->name ?? 'N/A' }}</p>
                                    <p class="text-sm text-gray-500">{{ $movement->batch->batch_number ?? 'N/A' }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $movement->fromLocation?->getFullPath() ?? __('locations.external') }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-700">
                                {{ $movement->toLocation?->getFullPath() ?? __('locations.external') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 py-1 text-xs font-medium rounded-full
                                    {{ $movement->reason === 'RECEIPT' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $movement->reason === 'TRANSFER' ? 'bg-blue-100 text-blue-700' : '' }}
                                    {{ in_array($movement->reason, ['EXPIRED', 'DAMAGED']) ? 'bg-red-100 text-red-700' : '' }}
                                    {{ !in_array($movement->reason, ['RECEIPT', 'TRANSFER', 'EXPIRED', 'DAMAGED']) ? 'bg-gray-100 text-gray-700' : '' }}">
                                    {{ $movement->getFormattedReason() }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $movement->quantity }}
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <!-- Location Details -->
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">{{ __('locations.details') }}</h2>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <p class="text-sm font-medium text-gray-600">{{ __('locations.full_path') }}</p>
                <p class="mt-1 text-gray-900">{{ $location->getFullPath() }}</p>
            </div>

            @if($location->parent)
            <div>
                <p class="text-sm font-medium text-gray-600">{{ __('locations.parent_location') }}</p>
                <a href="{{ route('locations.show', $location->parent) }}" class="mt-1 text-blue-600 hover:text-blue-800">
                    {{ $location->parent->getFullPath() }}
                </a>
            </div>
            @endif

            <div>
                <p class="text-sm font-medium text-gray-600">{{ __('locations.created_at') }}</p>
                <p class="mt-1 text-gray-900">{{ $location->created_at->format('Y-m-d H:i') }}</p>
            </div>

            <div>
                <p class="text-sm font-medium text-gray-600">{{ __('locations.last_updated') }}</p>
                <p class="mt-1 text-gray-900">{{ $location->updated_at->format('Y-m-d H:i') }}</p>
            </div>

            @if($location->notes)
            <div class="md:col-span-2">
                <p class="text-sm font-medium text-gray-600">{{ __('locations.notes') }}</p>
                <p class="mt-1 text-gray-900">{{ $location->notes }}</p>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
