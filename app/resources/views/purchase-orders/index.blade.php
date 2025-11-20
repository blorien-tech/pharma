@extends('layouts.app')

@section('title', 'Purchase Orders - BLORIEN Pharma')

@section('content')
<div class="space-y-6">
    <!-- Page Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Purchase Orders</h1>
            <p class="mt-1 text-sm text-gray-600">Manage inventory purchase orders</p>
        </div>
        <a href="{{ route('purchase-orders.create') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium">
            + Create PO
        </a>
    </div>

    <!-- Filters -->
    <div class="bg-white rounded-lg shadow p-4">
        <form method="GET" action="{{ route('purchase-orders.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Status</option>
                    <option value="PENDING" {{ request('status') === 'PENDING' ? 'selected' : '' }}>Pending</option>
                    <option value="ORDERED" {{ request('status') === 'ORDERED' ? 'selected' : '' }}>Ordered</option>
                    <option value="RECEIVED" {{ request('status') === 'RECEIVED' ? 'selected' : '' }}>Received</option>
                    <option value="CANCELLED" {{ request('status') === 'CANCELLED' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Supplier</label>
                <select name="supplier" class="w-full px-3 py-2 border rounded-lg">
                    <option value="">All Suppliers</option>
                    @foreach($suppliers as $supplier)
                    <option value="{{ $supplier->id }}" {{ request('supplier') == $supplier->id ? 'selected' : '' }}>
                        {{ $supplier->name }}
                    </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Date</label>
                <input type="date" name="date" value="{{ request('date') }}" class="w-full px-3 py-2 border rounded-lg">
            </div>
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                    Filter
                </button>
                @if(request()->hasAny(['status', 'supplier', 'date']))
                <a href="{{ route('purchase-orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                    Clear
                </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Purchase Orders Table -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">PO Number</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Supplier</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Order Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($purchaseOrders as $po)
                <tr class="hover:bg-gray-50">
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-blue-600">
                        <a href="{{ route('purchase-orders.show', $po) }}">{{ $po->po_number }}</a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        <a href="{{ route('suppliers.show', $po->supplier) }}" class="hover:text-blue-600">
                            {{ $po->supplier->name }}
                        </a>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        {{ $po->order_date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        ৳{{ number_format($po->total, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full
                            {{ $po->status === 'RECEIVED' ? 'bg-green-100 text-green-800' :
                               ($po->status === 'PENDING' ? 'bg-yellow-100 text-yellow-800' :
                               ($po->status === 'ORDERED' ? 'bg-blue-100 text-blue-800' : 'bg-red-100 text-red-800')) }}">
                            {{ $po->status }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium space-x-2">
                        <a href="{{ route('purchase-orders.show', $po) }}" class="text-blue-600 hover:text-blue-900">View</a>
                        @if($po->status !== 'RECEIVED' && $po->status !== 'CANCELLED')
                        <a href="{{ route('purchase-orders.receive', $po) }}" class="text-green-600 hover:text-green-900">Receive</a>
                        <form action="{{ route('purchase-orders.cancel', $po) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to cancel this PO?')">
                            @csrf
                            @method('PUT')
                            <button type="submit" class="text-red-600 hover:text-red-900">Cancel</button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                        No purchase orders found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($purchaseOrders->hasPages())
    <div class="bg-white px-4 py-3 rounded-lg shadow">
        {{ $purchaseOrders->links() }}
    </div>
    @endif
</div>
@endsection
