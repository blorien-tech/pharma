<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BLORIEN Pharma')</title>

    <!-- Vite Assets (Tailwind CSS + JS) -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <!-- Alpine.js with Persist Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Initialize sidebar state from localStorage before Alpine loads to prevent flash -->
    <script>
        // Read sidebar state from localStorage synchronously before page renders
        (function() {
            try {
                const sidebarState = localStorage.getItem('_x_sidebar_collapsed');
                if (sidebarState !== null) {
                    window.__sidebarCollapsed = JSON.parse(sidebarState);
                } else {
                    window.__sidebarCollapsed = false;
                }
            } catch (e) {
                window.__sidebarCollapsed = false;
            }
        })();
    </script>

    <!-- Translation Support for JavaScript -->
    <script>
        window.translations = {
            // Common
            save: '{{ __('common.save') }}',
            cancel: '{{ __('common.cancel') }}',
            delete: '{{ __('common.delete') }}',
            confirm: '{{ __('common.confirm') }}',
            yes: '{{ __('common.yes') }}',
            no: '{{ __('common.no') }}',
            ok: '{{ __('common.ok') }}',
            success: '{{ __('common.success') }}',
            error: '{{ __('common.error') }}',
            loading: '{{ __('common.loading') }}',
            processing: '{{ __('common.processing') }}',
            are_you_sure: '{{ __('common.are_you_sure') }}',
            confirm_delete: '{{ __('common.confirm_delete') }}',
            cannot_be_undone: '{{ __('common.cannot_be_undone') }}',
            saved_successfully: '{{ __('common.saved_successfully') }}',
            deleted_successfully: '{{ __('common.deleted_successfully') }}',
            updated_successfully: '{{ __('common.updated_successfully') }}',
            operation_failed: '{{ __('common.operation_failed') }}',

            // POS specific
            insufficient_stock: '{{ __('pos.insufficient_stock') }}',
            sale_completed: '{{ __('pos.sale_completed') }}',
            sale_and_due_created: '{{ __('pos.sale_and_due_created') }}',
            sale_error: '{{ __('pos.sale_error') }}',
            customer_name_required: '{{ __('pos.customer_name_required') }}',
            amount_less_than_total: '{{ __('pos.amount_less_than_total') }}',

            // Products specific
            stock_added_success: '{{ __('products.stock_added_success') }}',
            product_created: '{{ __('products.product_created') }}',
            product_updated: '{{ __('products.product_updated') }}',
            product_deleted: '{{ __('products.product_deleted') }}',
        };

        // Helper function to translate strings in JavaScript
        function __(key, replacements = {}) {
            let translation = window.translations[key] || key;

            // Replace placeholders
            Object.keys(replacements).forEach(placeholder => {
                translation = translation.replace(`:${placeholder}`, replacements[placeholder]);
            });

            return translation;
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }

        /* Smooth Scrolling */
        html {
            scroll-behavior: smooth;
        }

        /* Smooth Transitions for Interactive Elements */
        * {
            transition-property: background-color, border-color, color, fill, stroke, opacity, box-shadow, transform;
            transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
            transition-duration: 150ms;
        }

        /* Remove transitions from Alpine.js elements to avoid conflicts */
        [x-cloak], [x-show], [x-transition] {
            transition: none !important;
        }

        /* Focus States for Accessibility */
        button:focus,
        a:focus,
        input:focus,
        select:focus,
        textarea:focus {
            outline: 2px solid #3b82f6;
            outline-offset: 2px;
        }

        /* Loading Animation */
        @keyframes spin {
            from { transform: rotate(0deg); }
            to { transform: rotate(360deg); }
        }

        .animate-spin {
            animation: spin 1s linear infinite;
        }

        /* Fade In Animation */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-out;
        }

        /* Pulse Animation for Notifications */
        @keyframes pulse {
            0%, 100% {
                opacity: 1;
            }
            50% {
                opacity: 0.5;
            }
        }

        .animate-pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }

        /* Slide In from Right */
        @keyframes slideInRight {
            from {
                transform: translateX(100%);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in-right {
            animation: slideInRight 0.3s ease-out;
        }

        /* Smooth Hover Effect for Cards */
        .card-hover {
            transition: all 0.3s ease;
        }

        .card-hover:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -6px rgba(0, 0, 0, 0.1);
        }

        /* Custom Scrollbar Styles */
        ::-webkit-scrollbar {
            width: 8px;
            height: 8px;
        }

        ::-webkit-scrollbar-track {
            background: #f1f5f9;
        }

        ::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 4px;
        }

        ::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        /* Button Ripple Effect */
        .btn-ripple {
            position: relative;
            overflow: hidden;
        }

        .btn-ripple::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 0;
            height: 0;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.5);
            transform: translate(-50%, -50%);
            transition: width 0.6s, height 0.6s;
        }

        .btn-ripple:active::after {
            width: 300px;
            height: 300px;
        }
    </style>
</head>
<body class="h-full bg-gray-100 overflow-hidden" x-data="{ mobileMenuOpen: false, sidebarCollapsed: $persist(window.__sidebarCollapsed || false).as('sidebar_collapsed') }" @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen" @sidebar-collapse-changed.window="sidebarCollapsed = $event.detail.collapsed">
    @auth
    <!-- Main Container: Flex layout for sidebar + content -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Component -->
        @include('layouts.sidebar')

        <!-- Main Content Area: Topbar + Content + Footer -->
        <div class="flex-1 flex flex-col overflow-hidden transition-all duration-300 lg:ml-64"
             :class="{ 'lg:ml-20': sidebarCollapsed, 'lg:ml-64': !sidebarCollapsed }">
            <!-- Top Navbar Component -->
            @include('layouts.topbar')

            <!-- Main Content with Scroll -->
            <main class="flex-1 overflow-y-auto bg-gray-50 flex flex-col">
                <!-- Content Wrapper (takes available space) -->
                <div class="flex-1">
                    <!-- Flash Messages -->
                    @if(session('success'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 animate-slide-in-right">
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                                </svg>
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('error'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 animate-slide-in-right">
                        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                                </svg>
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('warning'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 animate-slide-in-right">
                        <div class="bg-yellow-100 border border-yellow-400 text-yellow-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/>
                                </svg>
                                <span class="block sm:inline">{{ session('warning') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    @if(session('info'))
                    <div class="mx-4 sm:mx-6 lg:mx-8 mt-4 animate-slide-in-right">
                        <div class="bg-blue-100 border border-blue-400 text-blue-700 px-4 py-3 rounded-lg relative shadow-sm" role="alert">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"/>
                                </svg>
                                <span class="block sm:inline">{{ session('info') }}</span>
                            </div>
                        </div>
                    </div>
                    @endif

                    <!-- Page Content -->
                    <div class="px-4 sm:px-6 lg:px-8 py-6 animate-fade-in">
                        @yield('content')
                    </div>
                </div>

                <!-- Sticky Footer -->
                <footer class="bg-white border-t py-6 mt-auto">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                            <div class="text-gray-600 text-sm">
                                <p>&copy; {{ date('Y') }} {{ __('navigation.app_name') }}. All rights reserved.</p>
                            </div>
                            <div class="text-gray-500 text-xs">
                                <p>Version 2.6 | Powered by BLORIEN</p>
                            </div>
                        </div>
                    </div>
                </footer>
            </main>
        </div>

        <!-- Mobile Menu Overlay -->
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition-opacity ease-linear duration-300"
            x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100"
            x-transition:leave="transition-opacity ease-linear duration-300"
            x-transition:leave-start="opacity-100"
            x-transition:leave-end="opacity-0"
            @click="mobileMenuOpen = false"
            class="fixed inset-0 bg-gray-600 bg-opacity-75 z-20 lg:hidden"
            style="display: none;"
        ></div>
    </div>
    @else
    <!-- Guest Layout (Login/Register pages) -->
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-gray-50 to-gray-100">
        @yield('content')
    </main>
    @endauth

    <!-- Global Quick Stock Modal -->
    @auth
    <div x-data="quickStockModal()" x-cloak>
        <div x-show="show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4"
             @open-quick-stock.window="openModal()"
             @click.self="closeModal()">
            <div x-show="show"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90"
                 class="bg-white rounded-2xl shadow-2xl max-w-2xl w-full overflow-hidden"
                 @click.stop>

                <form @submit.prevent="submitStock()">
                    <!-- Header -->
                    <div class="bg-gradient-to-r from-green-600 to-emerald-600 px-6 py-4">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center">
                                <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                                </svg>
                                <div>
                                    <h3 class="text-xl font-bold text-white">{{ __('products.quick_stock_add') }}</h3>
                                    <p class="text-sm text-green-100">{{ __('products.quick_stock_desc') }}</p>
                                </div>
                            </div>
                            <button @click="closeModal()" type="button" class="text-white hover:text-gray-200 transition">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                    </div>

                    <!-- Body -->
                    <div class="px-6 py-4 max-h-[70vh] overflow-y-auto space-y-4">
                        <!-- Product Search (with barcode support) -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('products.search_product') }}
                                <span class="text-xs text-gray-500">({{ __('products.type_or_scan_barcode') }})</span>
                            </label>
                            <div class="relative">
                                <input
                                    type="text"
                                    x-model="searchQuery"
                                    @input.debounce.300ms="searchProducts()"
                                    @focus="showSearchResults = true"
                                    :placeholder="__('products.search_by_name_or_barcode')"
                                    :disabled="processing"
                                    class="w-full px-4 py-3 pr-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
                                    autocomplete="off"
                                >
                                <div class="absolute right-3 top-3">
                                    <svg x-show="searching" class="w-5 h-5 text-gray-400 animate-spin" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    <svg x-show="!searching" class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                                    </svg>
                                </div>

                                <!-- Search Results Dropdown -->
                                <div x-show="showSearchResults && searchResults.length > 0"
                                     @click.away="showSearchResults = false"
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg max-h-64 overflow-y-auto">
                                    <template x-for="product in searchResults" :key="product.id">
                                        <button
                                            type="button"
                                            @click="selectProduct(product)"
                                            class="w-full text-left px-4 py-3 hover:bg-green-50 border-b border-gray-100 last:border-0 transition">
                                            <div class="flex justify-between items-start">
                                                <div class="flex-1">
                                                    <p class="font-medium text-gray-900" x-text="product.name"></p>
                                                    <p class="text-xs text-gray-500">
                                                        <span>SKU: </span><span x-text="product.sku"></span>
                                                        <span x-show="product.barcode"> | Barcode: <span x-text="product.barcode"></span></span>
                                                    </p>
                                                </div>
                                                <div class="text-right ml-3">
                                                    <p class="text-sm font-semibold" :class="product.current_stock < product.min_stock ? 'text-red-600' : 'text-gray-700'">
                                                        <span x-text="product.current_stock"></span> {{ __('products.units') }}
                                                    </p>
                                                    <p class="text-xs text-gray-500">{{ __('products.in_stock') }}</p>
                                                </div>
                                            </div>
                                        </button>
                                    </template>
                                </div>

                                <!-- No Results Message -->
                                <div x-show="showSearchResults && searchResults.length === 0 && searchQuery.length >= 2 && !searching"
                                     class="absolute z-10 w-full mt-1 bg-white border border-gray-300 rounded-lg shadow-lg p-4 text-center text-gray-500 text-sm">
                                    {{ __('products.no_products_found') }}
                                </div>
                            </div>
                        </div>

                        <!-- Selected Product Display -->
                        <div x-show="selectedProduct" class="p-4 bg-green-50 border border-green-200 rounded-lg">
                            <div class="flex justify-between items-start">
                                <div class="flex-1">
                                    <p class="text-sm text-gray-600">{{ __('products.selected_product') }}</p>
                                    <p class="text-lg font-bold text-gray-900" x-text="selectedProduct?.name"></p>
                                    <p class="text-sm text-gray-600 mt-1">
                                        {{ __('products.current_stock') }}:
                                        <span class="font-semibold" x-text="selectedProduct?.current_stock"></span> {{ __('products.units') }}
                                        <span x-show="selectedProduct && selectedProduct.current_stock < selectedProduct.min_stock" class="text-red-600 font-medium ml-2">
                                            ({{ __('products.low') }})
                                        </span>
                                    </p>
                                </div>
                                <button @click="clearSelection()" type="button" class="text-gray-500 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                    </svg>
                                </button>
                            </div>
                        </div>

                        <!-- Stock Details Form (only show when product selected) -->
                        <div x-show="selectedProduct" class="space-y-4">
                            <!-- Quantity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('products.quantity_to_add') }} *</label>
                                <input
                                    type="number"
                                    x-model.number="form.quantity"
                                    required
                                    min="1"
                                    step="1"
                                    :placeholder="__('products.enter_quantity')"
                                    :disabled="processing"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Batch Number -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('products.batch_number_label') }} *</label>
                                <input
                                    type="text"
                                    x-model="form.batch_number"
                                    required
                                    :placeholder="__('products.batch_example')"
                                    :disabled="processing"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Expiry Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('products.expiry_date_label') }} *</label>
                                <input
                                    type="date"
                                    x-model="form.expiry_date"
                                    required
                                    :min="new Date().toISOString().split('T')[0]"
                                    :disabled="processing"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                            </div>

                            <!-- Purchase Price (Optional) -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('products.purchase_price_optional') }}
                                    <span class="text-xs text-gray-500">
                                        ({{ __('products.default') }}: ৳<span x-text="selectedProduct?.purchase_price?.toFixed(2) || '0.00'"></span>)
                                    </span>
                                </label>
                                <input
                                    type="number"
                                    x-model.number="form.purchase_price"
                                    step="0.01"
                                    min="0"
                                    :placeholder="__('products.cost_per_unit')"
                                    :disabled="processing"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent">
                                <p class="text-xs text-gray-500 mt-1">{{ __('products.default_price_note') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Footer -->
                    <div class="px-6 py-4 bg-gray-50 border-t flex gap-3 justify-end">
                        <button
                            @click="closeModal()"
                            type="button"
                            :disabled="processing"
                            class="px-6 py-2.5 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition disabled:opacity-50 disabled:cursor-not-allowed">
                            {{ __('common.cancel') }}
                        </button>
                        <button
                            type="submit"
                            :disabled="!selectedProduct || processing"
                            :class="processing ? 'bg-gray-400' : 'bg-gradient-to-r from-green-600 to-emerald-600 hover:from-green-700 hover:to-emerald-700'"
                            class="px-6 py-2.5 text-white rounded-lg font-medium transition disabled:opacity-50 disabled:cursor-not-allowed">
                            <span x-show="!processing">{{ __('products.add_stock') }}</span>
                            <span x-show="processing" class="flex items-center">
                                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('products.adding') }}
                            </span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    @endauth

    <!-- Global Modal System -->
    <div x-data="modalSystem()" x-cloak>
        <!-- Alert Modal -->
        <div x-show="alert.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4"
             @click.self="alert.show = false">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                <!-- Header -->
                <div class="px-6 py-4"
                     :class="{
                         'bg-gradient-to-r from-blue-600 to-indigo-600': alert.type === 'info',
                         'bg-gradient-to-r from-green-600 to-emerald-600': alert.type === 'success',
                         'bg-gradient-to-r from-red-600 to-rose-600': alert.type === 'error',
                         'bg-gradient-to-r from-yellow-500 to-orange-500': alert.type === 'warning'
                     }">
                    <div class="flex items-center">
                        <!-- Icon -->
                        <div class="flex-shrink-0">
                            <svg x-show="alert.type === 'success'" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="alert.type === 'error'" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2m7-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <svg x-show="alert.type === 'warning'" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                            <svg x-show="alert.type === 'info'" class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <div class="ml-3">
                            <h3 class="text-lg font-semibold text-white" x-text="alert.title"></h3>
                        </div>
                    </div>
                </div>
                <!-- Body -->
                <div class="px-6 py-4">
                    <p class="text-gray-700" x-text="alert.message"></p>
                </div>
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 flex justify-end">
                    <button @click="alert.show = false"
                            class="px-6 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition">
                        OK
                    </button>
                </div>
            </div>
        </div>

        <!-- Confirm Modal -->
        <div x-show="confirm.show"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 px-4">
            <div x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-90"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-90"
                 class="bg-white rounded-2xl shadow-2xl max-w-md w-full overflow-hidden">
                <!-- Header -->
                <div class="bg-gradient-to-r from-blue-600 to-indigo-600 px-6 py-4">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-white mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        <h3 class="text-lg font-semibold text-white" x-text="confirm.title"></h3>
                    </div>
                </div>
                <!-- Body -->
                <div class="px-6 py-4">
                    <p class="text-gray-700" x-text="confirm.message"></p>
                </div>
                <!-- Footer -->
                <div class="px-6 py-4 bg-gray-50 flex gap-3">
                    <button @click="confirmCancel()"
                            class="flex-1 px-4 py-2 border border-gray-300 rounded-lg font-medium text-gray-700 hover:bg-gray-50 transition">
                        Cancel
                    </button>
                    <button @click="confirmOk()"
                            class="flex-1 px-4 py-2 bg-gradient-to-r from-blue-600 to-indigo-600 text-white rounded-lg font-medium hover:from-blue-700 hover:to-indigo-700 transition">
                        Confirm
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Global Quick Stock Modal Script -->
    <script>
        function quickStockModal() {
            return {
                show: false,
                processing: false,
                searching: false,
                searchQuery: '',
                searchResults: [],
                showSearchResults: false,
                selectedProduct: null,
                form: {
                    quantity: '',
                    batch_number: '',
                    expiry_date: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                    purchase_price: ''
                },

                openModal() {
                    this.show = true;
                    this.resetForm();
                    // Focus search input after modal opens
                    setTimeout(() => {
                        const input = document.querySelector('[x-model="searchQuery"]');
                        if (input) input.focus();
                    }, 100);
                },

                closeModal() {
                    this.show = false;
                    this.resetForm();
                },

                resetForm() {
                    this.searchQuery = '';
                    this.searchResults = [];
                    this.showSearchResults = false;
                    this.selectedProduct = null;
                    this.form = {
                        quantity: '',
                        batch_number: '',
                        expiry_date: new Date(Date.now() + 365 * 24 * 60 * 60 * 1000).toISOString().split('T')[0],
                        purchase_price: ''
                    };
                },

                async searchProducts() {
                    if (this.searchQuery.length < 2) {
                        this.searchResults = [];
                        return;
                    }

                    this.searching = true;

                    try {
                        const response = await fetch(`/api/products/search?q=${encodeURIComponent(this.searchQuery)}`, {
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            }
                        });

                        if (response.ok) {
                            this.searchResults = await response.json();
                            this.showSearchResults = true;
                        } else {
                            console.error('Search failed:', response.statusText);
                            this.searchResults = [];
                        }
                    } catch (error) {
                        console.error('Search error:', error);
                        this.searchResults = [];
                    } finally {
                        this.searching = false;
                    }
                },

                selectProduct(product) {
                    this.selectedProduct = product;
                    this.searchQuery = product.name;
                    this.showSearchResults = false;
                    this.form.purchase_price = ''; // Reset to use default
                },

                clearSelection() {
                    this.selectedProduct = null;
                    this.searchQuery = '';
                    this.showSearchResults = false;
                },

                async submitStock() {
                    if (!this.selectedProduct || this.processing) {
                        return;
                    }

                    this.processing = true;

                    try {
                        const response = await fetch('/api/products/quick-stock', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({
                                product_id: this.selectedProduct.id,
                                quantity: this.form.quantity,
                                batch_number: this.form.batch_number,
                                expiry_date: this.form.expiry_date,
                                purchase_price: this.form.purchase_price || null
                            })
                        });

                        const data = await response.json();

                        if (response.ok) {
                            // Success
                            window.showSuccess(
                                data.message || __('stock_added_success'),
                                __('success')
                            );
                            this.closeModal();

                            // Reload page after a short delay to show updated stock
                            setTimeout(() => {
                                window.location.reload();
                            }, 1500);
                        } else {
                            // Error
                            window.showError(
                                data.message || __('operation_failed'),
                                __('error')
                            );
                        }
                    } catch (error) {
                        console.error('Stock addition error:', error);
                        window.showError(
                            __('operation_failed'),
                            __('error')
                        );
                    } finally {
                        this.processing = false;
                    }
                }
            };
        }

        // Global helper to open quick stock modal
        window.openQuickStockModal = function() {
            window.dispatchEvent(new CustomEvent('open-quick-stock'));
        };
    </script>

    <!-- Global Modal System Script -->
    <script>
        function modalSystem() {
            return {
                alert: {
                    show: false,
                    title: '',
                    message: '',
                    type: 'info' // info, success, error, warning
                },
                confirm: {
                    show: false,
                    title: '',
                    message: '',
                    resolve: null
                },

                showAlert(message, title = 'Notification', type = 'info') {
                    this.alert = {
                        show: true,
                        title: title,
                        message: message,
                        type: type
                    };
                },

                showConfirm(message, title = 'Confirm Action') {
                    return new Promise((resolve) => {
                        this.confirm = {
                            show: true,
                            title: title,
                            message: message,
                            resolve: resolve
                        };
                    });
                },

                confirmOk() {
                    if (this.confirm.resolve) {
                        this.confirm.resolve(true);
                    }
                    this.confirm.show = false;
                },

                confirmCancel() {
                    if (this.confirm.resolve) {
                        this.confirm.resolve(false);
                    }
                    this.confirm.show = false;
                }
            };
        }

        // Global helper functions
        window.$modal = null;

        // Initialize modal system after Alpine loads
        document.addEventListener('alpine:init', () => {
            // Wait for modal system to be available
            setTimeout(() => {
                window.$modal = Alpine.$data(document.querySelector('[x-data="modalSystem()"]'));
            }, 100);
        });

        // Helper functions for easy access
        window.showAlert = function(message, title = 'Notification', type = 'info') {
            if (window.$modal) {
                window.$modal.showAlert(message, title, type);
            }
        };

        window.showSuccess = function(message, title = 'Success') {
            if (window.$modal) {
                window.$modal.showAlert(message, title, 'success');
            }
        };

        window.showError = function(message, title = 'Error') {
            if (window.$modal) {
                window.$modal.showAlert(message, title, 'error');
            }
        };

        window.showWarning = function(message, title = 'Warning') {
            if (window.$modal) {
                window.$modal.showAlert(message, title, 'warning');
            }
        };

        window.showConfirm = async function(message, title = 'Confirm Action') {
            if (window.$modal) {
                return await window.$modal.showConfirm(message, title);
            }
            return confirm(message); // Fallback to browser confirm
        };
    </script>

    @stack('scripts')
</body>
</html>
