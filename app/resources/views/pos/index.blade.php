@extends('layouts.app')

@section('title', __('pos.title') . ' - ' . __('navigation.app_name'))

@push('styles')
<style>
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div x-data="posApp()" x-init="init()" class="space-y-4">
    <!-- Page Header -->
    <div class="bg-blue-600 text-white rounded-lg shadow p-4">
        <h1 class="text-2xl font-bold">{{ __('pos.title') }}</h1>
        <p class="text-sm opacity-90">{{ __('pos.subtitle') }}</p>
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
                <div x-show="searchQuery.length >= 2" class="mt-4 max-h-96 overflow-y-auto border border-gray-200 rounded-lg bg-white">
                    <!-- Loading State -->
                    <div x-show="isSearching" class="p-4 text-center text-gray-500">
                        <svg class="animate-spin h-6 w-6 mx-auto mb-2 text-blue-600" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        <p class="text-sm">Searching products...</p>
                    </div>

                    <!-- No Results State -->
                    <div x-show="!isSearching && searchResults.length === 0" class="p-4 text-center text-gray-500">
                        <svg class="h-12 w-12 mx-auto mb-2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        <p class="text-sm">No products found for "<span x-text="searchQuery"></span>"</p>
                        <p class="text-xs text-gray-400 mt-1">Try searching by product name, SKU, or barcode</p>
                    </div>

                    <!-- Results List -->
                    <template x-for="product in searchResults" :key="product.id">
                        <div @click="addToCart(product)" class="p-3 border-b last:border-b-0 hover:bg-blue-50 cursor-pointer transition">
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
                                    <p class="text-sm"
                                       :class="item.max_stock <= 3 ? 'text-red-600 font-medium' : 'text-gray-600'">
                                        Available: <span x-text="item.max_stock"></span> units
                                    </p>
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
                                    @input="validateQuantity(index)"
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

                <!-- Customer Phone Lookup -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer Phone (Optional)</label>
                    <input
                        type="tel"
                        x-model="customerPhone"
                        @input="lookupCustomerByPhone()"
                        placeholder="Type phone number..."
                        class="w-full px-3 py-2 border rounded-lg text-sm">
                    <!-- Lookup Status Messages -->
                    <div x-show="customerLookupStatus" class="mt-2 p-2 rounded-lg text-xs" :class="customerFound ? 'bg-green-50 text-green-700' : 'bg-blue-50 text-blue-700'">
                        <span x-text="customerLookupStatus"></span>
                    </div>
                </div>

                <!-- Customer Name (Auto-filled or Manual) -->
                <div x-show="customerPhone.length > 0 || customerId" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Customer Name</label>
                    <input
                        type="text"
                        x-model="customerName"
                        placeholder="Customer name"
                        class="w-full px-3 py-2 border rounded-lg text-sm">
                </div>

                <!-- Credit Sale Option -->
                <div x-show="canUseCredit" class="mb-4">
                    <label class="flex items-center">
                        <input type="checkbox" x-model="isCredit" @change="onCreditChange()"
                            class="rounded border-gray-300 text-blue-600 shadow-sm focus:border-blue-500 focus:ring-blue-500">
                        <span class="ml-2 text-sm text-gray-700">Use Credit (Available: ৳<span x-text="availableCredit.toFixed(2)"></span>)</span>
                    </label>
                </div>

                <!-- Quick Due Option (Simple notebook-style) -->
                <div class="mb-4">
                    <label class="flex items-center p-3 bg-yellow-50 border border-yellow-200 rounded-lg cursor-pointer">
                        <input type="checkbox" x-model="markAsDue" @change="onDueChange()"
                            class="rounded border-gray-300 text-yellow-600 shadow-sm focus:border-yellow-500 focus:ring-yellow-500">
                        <div class="ml-2">
                            <span class="text-sm font-medium text-yellow-900">Mark as Due</span>
                            <p class="text-xs text-yellow-700">Customer will pay later</p>
                        </div>
                    </label>
                </div>

                <!-- Due Details (Optional date and notes) -->
                <div x-show="markAsDue" class="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                    <div class="space-y-2">
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Due Date (Optional)</label>
                            <input
                                type="date"
                                x-model="dueDate"
                                class="w-full px-2 py-1 border rounded text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-medium text-gray-700 mb-1">Notes (Optional)</label>
                            <textarea
                                x-model="dueNotes"
                                placeholder="Any additional notes..."
                                rows="2"
                                class="w-full px-2 py-1 border rounded text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div x-show="!isCredit" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <select x-model="paymentMethod" class="w-full px-3 py-2 border rounded-lg">
                        <option value="CASH">Cash</option>
                        <option value="CARD">Card</option>
                        <option value="MOBILE">Mobile Money (bKash/Nagad)</option>
                        <option value="OTHER">Other</option>
                    </select>
                </div>

                <!-- Amount Paid (Always show, smart handling) -->
                <div x-show="!isCredit" class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">
                        <span x-show="!markAsDue">Amount Paid</span>
                        <span x-show="markAsDue">Amount Paying Now (Optional)</span>
                    </label>
                    <input
                        type="number"
                        x-model.number="amountPaid"
                        @input="calculateDueAmount()"
                        step="0.01"
                        min="0"
                        :placeholder="markAsDue ? 'Leave empty for full due' : '0.00'"
                        class="w-full px-3 py-2 border rounded-lg">

                    <!-- Change calculation for cash -->
                    <div x-show="paymentMethod === 'CASH' && amountPaid > 0 && !markAsDue" class="mt-2 p-2 bg-blue-50 rounded">
                        <span class="text-sm text-blue-900">Change: ৳<span x-text="change.toFixed(2)"></span></span>
                    </div>

                    <!-- Smart Due Breakdown -->
                    <div x-show="markAsDue && amountPaid > 0" class="mt-2 p-3 bg-yellow-50 border border-yellow-300 rounded-lg">
                        <div class="space-y-1 text-sm">
                            <div class="flex justify-between">
                                <span class="text-gray-700">Total Bill:</span>
                                <span class="font-semibold">৳<span x-text="total.toFixed(2)"></span></span>
                            </div>
                            <div class="flex justify-between text-green-700">
                                <span>Paying Now:</span>
                                <span class="font-semibold">- ৳<span x-text="amountPaid.toFixed(2)"></span></span>
                            </div>
                            <div x-show="paymentMethod === 'CASH' && change > 0" class="flex justify-between text-xs text-blue-700">
                                <span>Change to Return:</span>
                                <span>৳<span x-text="change.toFixed(2)"></span></span>
                            </div>
                            <div class="border-t border-yellow-400 pt-1 mt-1"></div>
                            <div x-show="dueAmount > 0" class="flex justify-between font-bold text-red-700">
                                <span>Due Amount:</span>
                                <span>৳<span x-text="dueAmount.toFixed(2)"></span></span>
                            </div>
                            <div x-show="dueAmount <= 0" class="text-xs text-green-700 font-medium">
                                ✓ Fully paid - no due will be recorded
                            </div>
                        </div>
                    </div>

                    <!-- Full due message -->
                    <div x-show="markAsDue && (!amountPaid || amountPaid == 0)" class="mt-2 p-2 bg-yellow-50 border border-yellow-300 rounded text-sm">
                        <span class="text-red-700 font-semibold">Full amount will be marked as due: ৳<span x-text="total.toFixed(2)"></span></span>
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

    <!-- Sale Confirmation Modal -->
    <div x-show="showConfirmModal"
         x-cloak
         class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
         @click.self="showConfirmModal = false">
        <div class="bg-white rounded-2xl shadow-2xl max-w-md w-full mx-4 overflow-hidden">
            <!-- Modal Header -->
            <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                <h3 class="text-xl font-bold text-white">Confirm Sale</h3>
                <p class="text-blue-100 text-sm mt-1">Review transaction details before completing</p>
            </div>

            <!-- Modal Body -->
            <div class="p-6">
                <!-- Transaction Summary -->
                <div class="bg-gray-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                        </svg>
                        Transaction Summary
                    </h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Items:</span>
                            <span class="font-medium text-gray-900" x-text="cart.length"></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Gross Amount:</span>
                            <span class="font-semibold text-gray-900">৳<span x-text="subtotal.toFixed(2)"></span></span>
                        </div>
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Discount:</span>
                            <span class="font-semibold text-red-600">- ৳<span x-text="discount.toFixed(2)"></span></span>
                        </div>
                        <div class="border-t border-gray-300 pt-2 mt-2"></div>
                        <div class="flex justify-between">
                            <span class="font-semibold text-gray-900">Net Amount:</span>
                            <span class="font-bold text-lg text-green-600">৳<span x-text="total.toFixed(2)"></span></span>
                        </div>
                    </div>
                </div>

                <!-- Payment Details -->
                <div class="bg-blue-50 rounded-lg p-4 mb-4">
                    <h4 class="font-semibold text-gray-900 mb-3 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                        </svg>
                        Payment Details
                    </h4>
                    <div class="space-y-2">
                        <div class="flex justify-between text-sm">
                            <span class="text-gray-600">Method:</span>
                            <span class="font-semibold text-gray-900">
                                <span x-show="markAsDue && amountPaid > 0 && dueAmount > 0" class="bg-blue-100 text-blue-800 px-2 py-1 rounded text-xs font-medium">PARTIAL + DUE</span>
                                <span x-show="markAsDue && (!amountPaid || amountPaid == 0)" class="bg-yellow-100 text-yellow-800 px-2 py-1 rounded text-xs font-medium">FULL DUE</span>
                                <span x-show="markAsDue && amountPaid >= total" class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium">PAID (No Due)</span>
                                <span x-show="!markAsDue && isCredit" class="bg-purple-100 text-purple-800 px-2 py-1 rounded text-xs font-medium">CREDIT</span>
                                <span x-show="!markAsDue && !isCredit" class="bg-green-100 text-green-800 px-2 py-1 rounded text-xs font-medium" x-text="paymentMethod"></span>
                            </span>
                        </div>

                        <!-- Payment breakdown when due and paid amount > 0 -->
                        <div x-show="markAsDue && amountPaid > 0" class="bg-blue-50 -mx-2 px-2 py-2 rounded space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Payment Method:</span>
                                <span class="font-medium text-gray-900" x-text="paymentMethod"></span>
                            </div>
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-700">Paying Now:</span>
                                <span class="font-semibold text-green-700">৳<span x-text="amountPaid.toFixed(2)"></span></span>
                            </div>
                            <div x-show="dueAmount > 0" class="border-t border-blue-200 pt-1 mt-1"></div>
                            <div x-show="dueAmount > 0" class="flex justify-between text-sm font-bold">
                                <span class="text-red-700">Remaining Due:</span>
                                <span class="text-red-700">৳<span x-text="dueAmount.toFixed(2)"></span></span>
                            </div>
                            <div x-show="dueAmount <= 0" class="text-xs text-green-700 font-medium text-center pt-1">
                                ✓ Fully paid - no due will be recorded
                            </div>
                        </div>

                        <!-- Full payment (non-due) -->
                        <div x-show="!markAsDue" class="bg-gray-50 -mx-2 px-2 py-2 rounded space-y-1">
                            <div class="flex justify-between text-sm">
                                <span class="text-gray-600">Amount Paid:</span>
                                <span class="font-semibold text-gray-900">৳<span x-text="amountPaid.toFixed(2)"></span></span>
                            </div>
                            <div x-show="paymentMethod === 'CASH' && change > 0" class="flex justify-between text-sm bg-green-100 -mx-2 px-2 py-1 rounded">
                                <span class="text-green-700 font-medium">Change to Return:</span>
                                <span class="font-bold text-green-700">৳<span x-text="change.toFixed(2)"></span></span>
                            </div>
                        </div>

                        <!-- Full due (no payment) -->
                        <div x-show="markAsDue && (!amountPaid || amountPaid == 0)" class="bg-yellow-50 -mx-2 px-2 py-2 rounded">
                            <div class="flex justify-between text-sm font-bold text-red-700">
                                <span>Full Amount Due:</span>
                                <span>৳<span x-text="total.toFixed(2)"></span></span>
                            </div>
                        </div>

                        <!-- Customer Info -->
                        <div x-show="markAsDue && (customerName || customerPhone)" class="flex justify-between text-sm border-t border-gray-200 pt-2 mt-2">
                            <span class="text-gray-600">Customer:</span>
                            <span class="font-semibold text-gray-900">
                                <span x-text="customerName || 'Unknown'"></span>
                                <span x-show="customerPhone" class="text-xs text-gray-600 block" x-text="'(' + customerPhone + ')'"></span>
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Validation Warning -->
                <div x-show="validationError" class="bg-red-50 border-l-4 border-red-500 p-3 mb-4">
                    <div class="flex items-start">
                        <svg class="w-5 h-5 text-red-500 mr-2 mt-0.5 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" />
                        </svg>
                        <p class="text-sm text-red-700 font-medium" x-text="validationError"></p>
                    </div>
                </div>

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button
                        @click="showConfirmModal = false; validationError = ''"
                        class="flex-1 px-4 py-3 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button
                        @click="confirmAndCompleteSale()"
                        :disabled="processing || !!validationError"
                        class="flex-1 px-4 py-3 bg-gradient-to-r from-green-600 to-emerald-600 text-white rounded-lg font-semibold hover:from-green-700 hover:to-emerald-700 disabled:opacity-50 disabled:cursor-not-allowed transition flex items-center justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                        </svg>
                        <span x-show="!processing">Confirm Sale</span>
                        <span x-show="processing">Processing...</span>
                    </button>
                </div>
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
        searchDebounceTimer: null,
        isSearching: false,
        cart: [],
        subtotal: 0,
        discount: 0,
        total: 0,
        paymentMethod: 'CASH',
        amountPaid: 0,
        change: 0,
        processing: false,
        customerId: '',
        customerName: '',
        customerPhone: '',
        customerFound: false,
        customerLookupStatus: '',
        customerLookupTimer: null,
        isCredit: false,
        canUseCredit: false,
        availableCredit: 0,
        markAsDue: false,
        dueAmount: 0,
        dueDate: new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
        dueNotes: '',
        showConfirmModal: false,
        validationError: '',

        init() {
            // Initialize
        },

        async lookupCustomerByPhone() {
            // Clear existing timer
            if (this.customerLookupTimer) {
                clearTimeout(this.customerLookupTimer);
            }

            // Reset status if phone is too short
            if (this.customerPhone.length < 3) {
                this.customerLookupStatus = '';
                this.customerFound = false;
                this.customerId = '';
                this.customerName = '';
                this.canUseCredit = false;
                this.availableCredit = 0;
                return;
            }

            // Debounce the API call
            this.customerLookupTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/customers/search-by-phone?phone=${encodeURIComponent(this.customerPhone)}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        console.error('Customer lookup failed:', response.status);
                        this.customerLookupStatus = 'Error looking up customer';
                        this.customerFound = false;
                        return;
                    }

                    const data = await response.json();

                    if (data.found) {
                        // Customer exists
                        this.customerFound = true;
                        this.customerId = data.customer.id;
                        this.customerName = data.customer.name;
                        this.customerLookupStatus = `✓ Customer found: ${data.customer.name}`;
                        this.canUseCredit = data.customer.credit_enabled;
                        this.availableCredit = data.customer.available_credit;
                    } else {
                        // Customer doesn't exist - will be created
                        this.customerFound = false;
                        this.customerId = '';
                        this.customerName = '';
                        this.customerLookupStatus = '→ New customer - will be created automatically';
                        this.canUseCredit = false;
                        this.availableCredit = 0;
                    }
                } catch (error) {
                    console.error('Customer lookup error:', error);
                    this.customerLookupStatus = 'Error looking up customer';
                    this.customerFound = false;
                }
            }, 500);
        },

        onCreditChange() {
            if (this.isCredit) {
                this.paymentMethod = 'CREDIT';
                this.markAsDue = false; // Cannot use both credit and due
            } else {
                this.paymentMethod = 'CASH';
            }
        },

        onDueChange() {
            if (this.markAsDue) {
                this.isCredit = false; // Cannot use both due and credit
                this.calculateDueAmount();
            } else {
                // Reset due fields when unchecked
                this.dueAmount = 0;
                this.dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
                this.dueNotes = '';
            }
        },

        calculateDueAmount() {
            if (this.markAsDue) {
                // Smart calculation: due = total - amount paid
                const paymentAmount = parseFloat(this.amountPaid) || 0;
                this.dueAmount = this.total - paymentAmount;
                if (this.dueAmount < 0) this.dueAmount = 0;
            } else {
                this.dueAmount = 0;
            }
            // Also recalculate change
            this.calculateChange();
        },

        async searchProducts() {
            // Clear existing timer (Phase 3B: Debounce search for performance)
            if (this.searchDebounceTimer) {
                clearTimeout(this.searchDebounceTimer);
            }

            if (this.searchQuery.length < 2) {
                this.searchResults = [];
                this.isSearching = false;
                return;
            }

            this.isSearching = true;

            // Debounce search by 300ms
            this.searchDebounceTimer = setTimeout(async () => {
                try {
                    const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`, {
                        method: 'GET',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        },
                        credentials: 'same-origin'
                    });

                    if (!response.ok) {
                        console.error('Search failed:', response.status, response.statusText);
                        const errorText = await response.text();
                        console.error('Error response:', errorText);
                        this.searchResults = [];
                        this.isSearching = false;
                        return;
                    }

                    const data = await response.json();
                    this.searchResults = Array.isArray(data) ? data : [];
                    this.isSearching = false;
                    console.log('Search results:', this.searchResults);
                } catch (error) {
                    console.error('Search error:', error);
                    this.searchResults = [];
                    this.isSearching = false;
                }
            }, 300);
        },

        addToCart(product) {
            const existingItem = this.cart.find(item => item.id === product.id);

            if (existingItem) {
                const availableStock = product.current_stock;
                const remainingStock = availableStock - existingItem.quantity;

                if (existingItem.quantity < availableStock) {
                    existingItem.quantity++;
                    this.updateItemTotal(this.cart.indexOf(existingItem));

                    // Show warning if stock is low
                    if (remainingStock <= 3 && remainingStock > 0) {
                        showWarning(`Only ${remainingStock} ${remainingStock === 1 ? 'item' : 'items'} remaining in stock for ${product.name}`, 'Low Stock Alert');
                    }
                } else {
                    showWarning(`Cannot add more. Only ${availableStock} ${availableStock === 1 ? 'item is' : 'items are'} available in stock.`, 'Stock Limit Reached');
                }
            } else {
                // Check if product has stock before adding
                if (product.current_stock <= 0) {
                    showError('This product is out of stock and cannot be added to cart.', 'Out of Stock');
                    return;
                }

                this.cart.push({
                    id: product.id,
                    name: product.name,
                    sku: product.sku,
                    price: parseFloat(product.selling_price),
                    quantity: 1,
                    max_stock: product.current_stock,
                    total: parseFloat(product.selling_price)
                });

                // Show warning if stock is low when first adding
                if (product.current_stock <= 3) {
                    showWarning(`Low stock alert: Only ${product.current_stock} ${product.current_stock === 1 ? 'item' : 'items'} available for ${product.name}`, 'Low Stock');
                }
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
            const item = this.cart[index];
            const availableStock = item.max_stock;
            const remainingStock = availableStock - item.quantity;

            if (item.quantity < availableStock) {
                item.quantity++;
                this.updateItemTotal(index);

                // Show warning if stock is low
                if (remainingStock <= 3 && remainingStock > 0) {
                    showWarning(`Only ${remainingStock} ${remainingStock === 1 ? 'item' : 'items'} remaining in stock for ${item.name}`, 'Low Stock Alert');
                }
            } else {
                showWarning(`Cannot add more. Only ${availableStock} ${availableStock === 1 ? 'item is' : 'items are'} available in stock for ${item.name}.`, 'Stock Limit Reached');
            }
        },

        decrementQuantity(index) {
            if (this.cart[index].quantity > 1) {
                this.cart[index].quantity--;
                this.updateItemTotal(index);
            }
        },

        validateQuantity(index) {
            const item = this.cart[index];

            // Ensure quantity is a valid number
            if (isNaN(item.quantity) || item.quantity < 1) {
                item.quantity = 1;
            }

            // Enforce max stock limit
            if (item.quantity > item.max_stock) {
                item.quantity = item.max_stock;
                showWarning(`Cannot exceed available stock. Maximum ${item.max_stock} ${item.max_stock === 1 ? 'unit is' : 'units are'} available for ${item.name}.`, 'Stock Limit Reached');
            }

            this.updateItemTotal(index);
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
            // For CASH: amountPaid is what customer gives, calculate change
            // For other methods: no change
            if (this.paymentMethod === 'CASH') {
                // Change = what customer gave - what they're actually paying
                // If marking as due: paying = amountPaid (whatever they want to pay)
                // If not marking as due: paying = total (full payment required)
                if (this.markAsDue) {
                    // No change for due payments - amount paid is exact
                    this.change = 0;
                } else {
                    // Normal sale: calculate change from total
                    this.change = this.amountPaid - this.total;
                }
            } else {
                this.change = 0;
            }
        },

        getTotalItems() {
            return this.cart.reduce((sum, item) => sum + item.quantity, 0);
        },

        async clearCart(skipConfirm = false) {
            if (!skipConfirm) {
                const confirmed = await showConfirm('All items in the cart will be removed. This action cannot be undone.', 'Clear Cart?');
                if (!confirmed) {
                    return;
                }
            }

            this.cart = [];
            this.discount = 0;
            this.amountPaid = 0;
            this.customerId = '';
            this.customerName = '';
            this.customerPhone = '';
            this.customerFound = false;
            this.customerLookupStatus = '';
            this.isCredit = false;
            this.canUseCredit = false;
            this.availableCredit = 0;
            this.markAsDue = false;
            this.dueAmount = 0;
            this.dueDate = new Date(Date.now() + 30 * 24 * 60 * 60 * 1000).toISOString().split('T')[0];
            this.dueNotes = '';
            this.paymentMethod = 'CASH';
            this.calculateTotals();
        },

        completeSale() {
            // Initial validation before showing modal
            if (this.cart.length === 0) return;

            this.validationError = '';

            // Validate due entry - require customer name when creating a due
            if (this.markAsDue && this.dueAmount > 0) {
                if (!this.customerName || this.customerName.trim() === '') {
                    this.validationError = 'Please enter customer name for due entry';
                    this.showConfirmModal = true;
                    return;
                }
            }

            // Validate credit sale
            if (this.isCredit) {
                if (this.total > this.availableCredit) {
                    this.validationError = `Insufficient credit. Available: ৳${this.availableCredit.toFixed(2)}, Required: ৳${this.total.toFixed(2)}`;
                    this.showConfirmModal = true;
                    return;
                }
            } else if (!this.markAsDue && this.paymentMethod === 'CASH' && this.amountPaid < this.total) {
                this.validationError = 'Amount paid is less than total';
                this.showConfirmModal = true;
                return;
            }

            // Stock validation is now done during cart operations
            // All validations passed, show confirmation modal
            this.showConfirmModal = true;
        },

        async confirmAndCompleteSale() {
            this.processing = true;

            try {
                // Simple: Use amountPaid as is
                // If not marking as due: full payment (amountPaid for cash, total for others)
                // If marking as due: whatever amount paid (could be 0, partial, or full)
                let actualAmountPaid = 0;
                if (!this.markAsDue) {
                    // Normal sale - full payment required
                    actualAmountPaid = this.paymentMethod === 'CASH' ? this.amountPaid : this.total;
                } else {
                    // Due sale - use whatever they're paying
                    actualAmountPaid = parseFloat(this.amountPaid) || 0;
                }

                const payload = {
                    items: this.cart.map(item => ({
                        product_id: item.id,
                        quantity: item.quantity,
                        unit_price: item.price
                    })),
                    payment_method: this.paymentMethod,
                    discount: this.discount,
                    amount_paid: actualAmountPaid
                };

                // Add customer and credit info if applicable
                if (this.customerId) {
                    payload.customer_id = this.customerId;
                }
                if (this.isCredit) {
                    payload.is_credit = true;
                }

                console.log('Transaction payload:', payload);

                const response = await fetch('/api/transactions', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    },
                    body: JSON.stringify(payload)
                });

                let data;
                try {
                    data = await response.json();
                    console.log('Transaction response:', response.status, data);
                } catch (e) {
                    console.error('Failed to parse transaction response:', e);
                    this.validationError = 'Server error - please check the logs and try again';
                    this.processing = false;
                    return;
                }

                if (response.ok) {
                    // If marked as due, create due entry (only if there's a due amount)
                    if (this.markAsDue && this.dueAmount > 0 && data.transaction && data.transaction.id) {
                        const duePayload = {
                            customer_name: this.customerName,
                            customer_phone: this.customerPhone || null,
                            transaction_id: data.transaction.id,
                            amount: this.dueAmount,  // Use calculated due amount (not total)
                            due_date: this.dueDate || null,
                            notes: this.dueNotes || null
                        };

                        console.log('Due payload:', duePayload);

                        const dueResponse = await fetch('/api/dues', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify(duePayload)
                        });

                        let dueData;
                        try {
                            dueData = await dueResponse.json();
                            console.log('Due response:', dueResponse.status, dueData);
                        } catch (e) {
                            console.error('Failed to parse due response:', e);
                            this.showSuccessNotification('Sale completed but error recording due entry');
                            this.showConfirmModal = false;
                            this.validationError = '';
                            if (data.transaction && data.transaction.id) {
                                window.open(`/transactions/${data.transaction.id}`, '_blank');
                            }
                            this.clearCart(true);
                            this.processing = false;
                            return;
                        }

                        if (dueResponse.ok) {
                            let message = '';
                            const paidAmount = parseFloat(this.amountPaid) || 0;

                            if (paidAmount > 0) {
                                // Partial payment
                                message = `Sale completed! Paid ৳${paidAmount.toFixed(2)}, Due ৳${this.dueAmount.toFixed(2)}`;
                                if (!this.customerFound && this.customerPhone) {
                                    message += ' (New customer created)';
                                }
                            } else {
                                // Full due
                                if (this.customerFound) {
                                    message = 'Sale completed and full amount marked as due for existing customer!';
                                } else if (this.customerPhone) {
                                    message = 'Sale completed, new customer created, and full amount marked as due!';
                                } else {
                                    message = 'Sale completed and full amount marked as due!';
                                }
                            }
                            this.showSuccessNotification(message);
                        } else {
                            const dueError = dueData.message || 'Unknown error creating due entry';
                            console.error('Due creation failed:', dueError);
                            this.showSuccessNotification('Sale completed but error recording due: ' + dueError);
                        }
                    } else {
                        this.showSuccessNotification('Sale completed successfully!');
                    }

                    // Close modal
                    this.showConfirmModal = false;
                    this.validationError = '';

                    // Open receipt in new tab
                    if (data.transaction && data.transaction.id) {
                        window.open(`/transactions/${data.transaction.id}`, '_blank');
                    }
                    this.clearCart(true); // Skip confirmation after successful sale
                } else {
                    // Transaction failed - show detailed error
                    const errorMessage = data.message || data.error || 'Error completing sale';
                    console.error('Transaction failed:', errorMessage, data);
                    this.validationError = errorMessage;
                }
            } catch (error) {
                console.error('Sale error (caught exception):', error);
                this.validationError = 'Network error - please check your connection and try again: ' + error.message;
            } finally {
                this.processing = false;
            }
        },

        showSuccessNotification(message) {
            // Create a temporary success notification
            const notification = document.createElement('div');
            notification.className = 'fixed top-4 right-4 bg-green-600 text-white px-6 py-4 rounded-lg shadow-lg z-50 flex items-center';
            notification.innerHTML = `
                <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span class="font-medium">${message}</span>
            `;
            document.body.appendChild(notification);

            // Remove after 3 seconds
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }
    }
}
</script>
@endpush
@endsection
