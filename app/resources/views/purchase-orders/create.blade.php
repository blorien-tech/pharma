@extends('layouts.app')

@section('title', 'Create Purchase Order - BLORIEN Pharma')

@section('content')
<div x-data="purchaseOrderApp()" x-init="init()" class="space-y-6">
    <div>
        <h1 class="text-3xl font-bold text-gray-900">Create Purchase Order</h1>
        <p class="mt-1 text-sm text-gray-600">Order new stock from suppliers</p>
    </div>

    <form action="{{ route('purchase-orders.store') }}" method="POST" class="space-y-6">
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

        <div class="bg-white rounded-lg shadow-md p-6 space-y-6">
            <h2 class="text-lg font-semibold text-gray-900">Order Information</h2>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label for="supplier_id" class="block text-sm font-medium text-gray-700">Supplier *</label>
                    <select name="supplier_id" id="supplier_id" required class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                        <option value="">Select Supplier</option>
                        @foreach($suppliers as $supplier)
                        <option value="{{ $supplier->id }}" {{ old('supplier_id') == $supplier->id ? 'selected' : '' }}>
                            {{ $supplier->name }} - {{ $supplier->company_name }}
                        </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="order_date" class="block text-sm font-medium text-gray-700">Order Date *</label>
                    <input type="date" name="order_date" id="order_date" required value="{{ old('order_date', date('Y-m-d')) }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>

                <div>
                    <label for="expected_delivery_date" class="block text-sm font-medium text-gray-700">Expected Delivery</label>
                    <input type="date" name="expected_delivery_date" id="expected_delivery_date" value="{{ old('expected_delivery_date') }}"
                        class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-lg font-semibold text-gray-900">Order Items</h2>
                <button type="button" @click="addItem()" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                    + Add Item
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(item, index) in items" :key="index">
                    <div class="border rounded-lg p-4">
                        <div class="grid grid-cols-1 md:grid-cols-5 gap-4">
                            <div class="md:col-span-2">
                                <label class="block text-sm font-medium text-gray-700">Product *</label>
                                <select x-model="item.product_id" :name="`items[${index}][product_id]`" required
                                    @change="updateProductPrice(index)"
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                                    <option value="">Select Product</option>
                                    @foreach($products as $product)
                                    <option value="{{ $product->id }}" data-price="{{ $product->purchase_price }}">
                                        {{ $product->name }} ({{ $product->sku }})
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Quantity *</label>
                                <input type="number" x-model.number="item.quantity" :name="`items[${index}][quantity]`"
                                    @input="calculateTotals()" min="1" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">Unit Price (৳) *</label>
                                <input type="number" x-model.number="item.unit_price" :name="`items[${index}][unit_price]`"
                                    @input="calculateTotals()" step="0.01" min="0" required
                                    class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border text-sm">
                            </div>

                            <div class="flex items-end gap-2">
                                <div class="flex-1">
                                    <label class="block text-sm font-medium text-gray-700">Subtotal</label>
                                    <input type="text" :value="(item.quantity * item.unit_price).toFixed(2)" readonly
                                        class="mt-1 block w-full rounded-md border-gray-300 bg-gray-50 px-3 py-2 border text-sm">
                                </div>
                                <button type="button" @click="removeItem(index)" class="bg-red-600 hover:bg-red-700 text-white px-3 py-2 rounded text-sm">
                                    Remove
                                </button>
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="items.length === 0" class="text-center py-8 text-gray-500">
                    No items added. Click "Add Item" to start.
                </div>
            </div>

            <!-- Totals -->
            <div class="mt-6 border-t pt-6 space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="shipping" class="block text-sm font-medium text-gray-700">Shipping Cost (৳)</label>
                        <input type="number" name="shipping" id="shipping" x-model.number="shipping"
                            @input="calculateTotals()" step="0.01" min="0" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>

                    <div>
                        <label for="tax" class="block text-sm font-medium text-gray-700">Tax (৳)</label>
                        <input type="number" name="tax" id="tax" x-model.number="tax"
                            @input="calculateTotals()" step="0.01" min="0" value="0"
                            class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">
                    </div>
                </div>

                <div class="bg-gray-50 rounded-lg p-4">
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">৳<span x-text="subtotal.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-600">Shipping:</span>
                        <span class="font-medium">৳<span x-text="shipping.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-sm mb-2">
                        <span class="text-gray-600">Tax:</span>
                        <span class="font-medium">৳<span x-text="tax.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center text-lg font-bold border-t pt-2 mt-2">
                        <span>Total:</span>
                        <span class="text-green-600">৳<span x-text="total.toFixed(2)"></span></span>
                    </div>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow-md p-6">
            <label for="notes" class="block text-sm font-medium text-gray-700">Notes</label>
            <textarea name="notes" id="notes" rows="3"
                class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 px-3 py-2 border">{{ old('notes') }}</textarea>
        </div>

        <div class="flex justify-end space-x-3">
            <a href="{{ route('purchase-orders.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-700 px-6 py-2 rounded-lg font-medium">
                Cancel
            </a>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg font-medium">
                Create Purchase Order
            </button>
        </div>
    </form>
</div>

@push('scripts')
<script>
function purchaseOrderApp() {
    return {
        items: [],
        subtotal: 0,
        shipping: 0,
        tax: 0,
        total: 0,

        init() {
            this.addItem();
        },

        addItem() {
            this.items.push({
                product_id: '',
                quantity: 1,
                unit_price: 0
            });
        },

        removeItem(index) {
            this.items.splice(index, 1);
            this.calculateTotals();
        },

        updateProductPrice(index) {
            const select = event.target;
            const selectedOption = select.options[select.selectedIndex];
            const price = selectedOption.getAttribute('data-price');
            if (price) {
                this.items[index].unit_price = parseFloat(price);
                this.calculateTotals();
            }
        },

        calculateTotals() {
            this.subtotal = this.items.reduce((sum, item) => {
                return sum + (item.quantity * item.unit_price);
            }, 0);

            this.total = this.subtotal + this.shipping + this.tax;
        }
    }
}
</script>
@endpush
@endsection
