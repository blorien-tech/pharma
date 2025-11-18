@extends('layouts.app')

@section('title', 'Point of Sale - BLORIEN Pharma')

@section('content')
<div x-data="posApp()" x-init="init()" class="space-y-4">
    <!-- Page Header -->
    <div class="bg-blue-600 text-white rounded-lg shadow p-4">
        <h1 class="text-2xl font-bold">Point of Sale (POS)</h1>
        <p class="text-sm opacity-90">Scan or search products to add to cart</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Left: Product Search & List -->
        <div class="lg:col-span-2 space-y-4">
            <!-- Search Bar -->
            <div class="bg-white rounded-lg shadow p-4">
                <div class="relative">
                    <input
                        type="text"
                        x-model="searchQuery"
                        @input="searchProducts()"
                        @keydown.escape="searchQuery = ''; searchResults = []"
                        placeholder="Search products by name or SKU..."
                        class="w-full px-4 py-3 pl-12 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent text-lg"
                        autofocus>
                    <svg class="absolute left-4 top-4 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>

                <!-- Search Results -->
                <div x-show="searchResults.length > 0" class="mt-4 max-h-96 overflow-y-auto">
                    <template x-for="product in searchResults" :key="product.id">
                        <div @click="addToCart(product)" class="p-3 border-b hover:bg-blue-50 cursor-pointer transition">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <h3 class="font-semibold text-gray-900" x-text="product.name"></h3>
                                    <p class="text-sm text-gray-500">SKU: <span x-text="product.sku"></span></p>
                                    <p class="text-sm text-gray-600">Stock: <span x-text="product.current_stock"></span> units</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-lg font-bold text-green-600">৳<span x-text="parseFloat(product.selling_price).toFixed(2)"></span></p>
                                </div>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            <!-- Cart Items -->
            <div class="bg-white rounded-lg shadow">
                <div class="p-4 border-b">
                    <h2 class="text-lg font-semibold">Cart Items (<span x-text="cart.length"></span>)</h2>
                </div>
                <div class="divide-y max-h-96 overflow-y-auto">
                    <template x-for="(item, index) in cart" :key="index">
                        <div class="p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div class="flex-1">
                                    <h3 class="font-semibold" x-text="item.name"></h3>
                                    <p class="text-sm text-gray-500">৳<span x-text="parseFloat(item.price).toFixed(2)"></span> each</p>
                                </div>
                                <button @click="removeFromCart(index)" class="text-red-600 hover:text-red-800">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                </button>
                            </div>
                            <div class="flex items-center gap-3">
                                <button @click="decrementQuantity(index)" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">-</button>
                                <input
                                    type="number"
                                    x-model.number="item.quantity"
                                    @change="updateItemTotal(index)"
                                    min="1"
                                    :max="item.max_stock"
                                    class="w-20 px-3 py-1 border rounded text-center">
                                <button @click="incrementQuantity(index)" class="bg-gray-200 hover:bg-gray-300 px-3 py-1 rounded">+</button>
                                <span class="flex-1 text-right font-semibold">৳<span x-text="item.total.toFixed(2)"></span></span>
                            </div>
                        </div>
                    </template>

                    <div x-show="cart.length === 0" class="p-8 text-center text-gray-500">
                        <svg class="w-16 h-16 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <p>Cart is empty</p>
                        <p class="text-sm mt-1">Search and add products to start a sale</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Cart Summary & Checkout -->
        <div class="space-y-4">
            <!-- Cart Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold mb-4">Cart Summary</h2>

                <div class="space-y-3 mb-4">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Items:</span>
                        <span class="font-medium" x-text="getTotalItems()"></span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Subtotal:</span>
                        <span class="font-medium">৳<span x-text="subtotal.toFixed(2)"></span></span>
                    </div>
                    <div class="flex justify-between items-center">
                        <span class="text-gray-600">Discount:</span>
                        <div class="flex items-center gap-2">
                            <input
                                type="number"
                                x-model.number="discount"
                                @input="calculateTotals()"
                                min="0"
                                :max="subtotal"
                                step="0.01"
                                class="w-24 px-2 py-1 border rounded text-right">
                        </div>
                    </div>
                    <div class="pt-3 border-t flex justify-between text-xl font-bold">
                        <span>Total:</span>
                        <span class="text-green-600">৳<span x-text="total.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select x-model="paymentMethod" class="w-full px-3 py-2 border rounded-lg">
                        <option value="CASH">Cash</option>
                        <option value="CARD">Card</option>
                        <option value="MOBILE">Mobile Money</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <!-- Amount Paid (for cash) -->
                <div x-show="paymentMethod === 'CASH'" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                    <input
                        type="number"
                        x-model.number="amountPaid"
                        @input="calculateChange()"
                        step="0.01"
                        min="0"
                        class="w-full px-3 py-2 border rounded-lg">
                    <div x-show="change >= 0" class="mt-2 p-2 bg-blue-50 rounded">
                        <span class="text-sm text-blue-900">Change: ৳<span x-text="change.toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Complete Sale Button -->
                <button
                    @click="completeSale()"
                    :disabled="cart.length === 0 || processing"
                    :class="cart.length === 0 || processing ? 'bg-gray-400' : 'bg-green-600 hover:bg-green-700'"
                    class="w-full text-white py-3 rounded-lg font-semibold text-lg transition">
                    <span x-show="!processing">Complete Sale</span>
                    <span x-show="processing">Processing...</span>
                </button>

                <!-- Clear Cart Button -->
                <button
                    @click="clearCart()"
                    :disabled="cart.length === 0"
                    :class="cart.length === 0 ? 'bg-gray-200 text-gray-400' : 'bg-red-100 hover:bg-red-200 text-red-600'"
                    class="w-full mt-2 py-2 rounded-lg font-medium transition">
                    Clear Cart
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
function posApp() {
    return {
        searchQuery: '',
        searchResults: [],
        cart: [],
        subtotal: 0,
        discount: 0,
        total: 0,
        paymentMethod: 'CASH',
        amountPaid: 0,
        change: 0,
        processing: false,

        init() {
            // Initialize
        },

        async searchProducts() {
            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                return;
            }

            try {
                const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`, {
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });
                this.searchResults = await response.json();
            } catch (error) {
                console.error('Search error:', error);
            }
        },

        addToCart(product) {
            const existingItem = this.cart.find(item => item.id === product.id);

            if (existingItem) {
                if (existingItem.quantity < product.current_stock) {
                    existingItem.quantity++;
                    this.updateItemTotal(this.cart.indexOf(existingItem));
                } else {
                    alert('Insufficient stock available');
                }
            } else {
                this.cart.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    price: parseFloat(product.selling_price),
                    quantity: 1,
                    max_stock: product.current_stock,
                    total: parseFloat(product.selling_price)
                });
            }

            this.calculateTotals();
            this.searchQuery = '';
            this.searchResults = [];
        },

        removeFromCart(index) {
            this.cart.splice(index, 1);
            this.calculateTotals();
        },

        incrementQuantity(index) {
            if (this.cart[index].quantity < this.cart[index].max_stock) {
                this.cart[index].quantity++;
                this.updateItemTotal(index);
            }
        },

        decrementQuantity(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
                this.updateItemTotal(index);
            }
        },

        updateItemTotal(index) {
            this.cart[index].total = this.cart[index].quantity * this.cart[index].price;
            this.calculateTotals();
        },

        calculateTotals() {
            this.subtotal = this.cart.reduce((sum, item) => sum + item.total, 0);
            this.total = this.subtotal - this.discount;
            if (this.total < 0) this.total = 0;
            this.calculateChange();
        },

        calculateChange() {
            if (this.paymentMethod === 'CASH') {
                this.change = this.amountPaid - this.total;
            } else {
                this.change = 0;
            }
        },

        getTotalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        clearCart() {
            if (confirm('Are you sure you want to clear the cart?')) {
                this.cart = [];
                this.discount = 0;
                this.amountPaid = 0;
                this.calculateTotals();
            }
        },

        async completeSale() {
            if (this.cart.length === 0) return;

            if (this.paymentMethod === 'CASH' && this.amountPaid < this.total) {
                alert('Amount paid is less than total');
                return;
            }

            this.processing = true;

            try {
                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify({
                        items: this.cart.map(item => ({
                            product_id: item.id,
                            quantity: item.quantity,
                            unit_price: item.price
                        })),
                        payment_method: this.paymentMethod,
                        discount: this.discount,
                        amount_paid: this.paymentMethod === 'CASH' ? this.amountPaid : this.total
                    })
                });

                const data = await response.json();

                if (response.ok) {
                    alert('Sale completed successfully!');
                    // Open receipt in new tab
                    if (data.transaction && data.transaction.id) {
                        window.open(`/transactions/${data.transaction.id}`, '_blank');
                    }
                    this.clearCart();
                } else {
                    alert(data.message || 'Error completing sale');
                }
            } catch (error) {
                console.error('Sale error:', error);
                alert('Error completing sale');
            } finally {
                this.processing = false;
            }
        }
    }
}
</script>
@endpush
@endsection
