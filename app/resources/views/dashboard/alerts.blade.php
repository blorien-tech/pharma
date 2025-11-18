@extends('layouts.app')

@section('title', 'Alerts - BLORIEN Pharma')

@section('content')
<div x-data="alertsApp()" x-init="loadAlerts()" class="space-y-6">
    <!-- Page Header -->
    <div>
        <h1 class="text-3xl font-bold text-gray-900">System Alerts</h1>
        <p class="mt-1 text-sm text-gray-600">Monitor low stock and expiring products</p>
    </div>

    <!-- Alert Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div class="bg-orange-50 border border-orange-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-orange-600">Low Stock</p>
                    <p class="text-3xl font-bold text-orange-900" x-text="alerts.low_stock?.count || 0"></p>
                </div>
                <svg class="w-12 h-12 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-yellow-600">Expiring Soon</p>
                    <p class="text-3xl font-bold text-yellow-900" x-text="alerts.expiring_soon?.count || 0"></p>
                </div>
                <svg class="w-12 h-12 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
            </div>
        </div>

        <div class="bg-red-50 border border-red-200 rounded-lg p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-red-600">Expired</p>
                    <p class="text-3xl font-bold text-red-900" x-text="alerts.expired?.count || 0"></p>
                </div>
                <svg class="w-12 h-12 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
        </div>
    </div>

    <!-- Low Stock Products -->
    <div x-show="alerts.low_stock?.items?.length > 0" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Low Stock Products</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">SKU</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Current Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Min Stock</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Action</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in alerts.low_stock?.items" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="item.name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.sku"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-orange-600" x-text="item.current_stock"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.min_stock"></td>
                            <td class="px-6 py-4 text-sm">
                                <a :href="`/products/${item.id}/edit`" class="text-blue-600 hover:text-blue-900">Restock</a>
                            </td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expiring Soon Batches -->
    <div x-show="alerts.expiring_soon?.items?.length > 0" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Batches Expiring Soon (30 days)</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Days Left</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in alerts.expiring_soon?.items" :key="item.id">
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="item.product?.name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.batch_number"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-yellow-600" x-text="formatDate(item.expiry_date)"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.quantity_remaining"></td>
                            <td class="px-6 py-4 text-sm text-yellow-600" x-text="getDaysUntil(item.expiry_date)"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Expired Batches -->
    <div x-show="alerts.expired?.items?.length > 0" class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Expired Batches</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Product</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Batch Number</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Expiry Date</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Remaining</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <template x-for="item in alerts.expired?.items" :key="item.id">
                        <tr class="hover:bg-gray-50 bg-red-50">
                            <td class="px-6 py-4 text-sm font-medium text-gray-900" x-text="item.product?.name"></td>
                            <td class="px-6 py-4 text-sm text-gray-500" x-text="item.batch_number"></td>
                            <td class="px-6 py-4 text-sm font-semibold text-red-600" x-text="formatDate(item.expiry_date)"></td>
                            <td class="px-6 py-4 text-sm text-red-600" x-text="item.quantity_remaining"></td>
                        </tr>
                    </template>
                </tbody>
            </table>
        </div>
    </div>

    <!-- No Alerts -->
    <div x-show="alerts.total_alerts === 0" class="bg-green-50 border border-green-200 rounded-lg p-12 text-center">
        <svg class="w-16 h-16 mx-auto mb-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
        </svg>
        <h3 class="text-lg font-semibold text-green-900">All Clear!</h3>
        <p class="text-green-700 mt-2">No alerts at this time. Everything is in good condition.</p>
    </div>
</div>

@push('scripts')
<script>
function alertsApp() {
    return {
        alerts: {
            low_stock: { count: 0, items: [] },
            expiring_soon: { count: 0, items: [] },
            expired: { count: 0, items: [] },
            total_alerts: 0
        },

        async loadAlerts() {
            try {
                // Load low stock
                const lowStockRes = await fetch('/api/products?search=');
                const products = await lowStockRes.json();
                const lowStock = products.filter(p => p.current_stock <= p.min_stock);

                // Load expiring batches
                const expiringRes = await fetch('/api/batches/expiring');
                const expiring = await expiringRes.json();

                // Load expired batches
                const expiredRes = await fetch('/api/batches/expired');
                const expired = await expiredRes.json();

                this.alerts = {
                    low_stock: { count: lowStock.length, items: lowStock },
                    expiring_soon: { count: expiring.length, items: expiring },
                    expired: { count: expired.length, items: expired },
                    total_alerts: lowStock.length + expiring.length + expired.length
                };
            } catch (error) {
                console.error('Error loading alerts:', error);
            }
        },

        formatDate(date) {
            return new Date(date).toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
        },

        getDaysUntil(date) {
            const days = Math.ceil((new Date(date) - new Date()) / (1000 * 60 * 60 * 24));
            return `${days} days`;
        }
    }
}
</script>
@endpush
@endsection
