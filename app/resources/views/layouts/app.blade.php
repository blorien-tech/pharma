<!DOCTYPE html>
<html lang="{{ app()->getLocale() }}" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BLORIEN Pharma')</title>

    <!-- TailwindCSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js with Persist Plugin -->
    <script defer src="https://cdn.jsdelivr.net/npm/@alpinejs/persist@3.x.x/dist/cdn.min.js"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

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
<body class="h-full bg-gray-100 overflow-hidden" x-data="{ mobileMenuOpen: false, sidebarCollapsed: false }" @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen" @sidebar-collapse-changed.window="sidebarCollapsed = $event.detail.collapsed">
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
            <main class="flex-1 overflow-y-auto bg-gray-50">
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

                <!-- Footer -->
                <footer class="bg-white border-t mt-8 py-6">
                    <div class="px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col md:flex-row justify-between items-center space-y-2 md:space-y-0">
                            <div class="text-gray-600 text-sm">
                                <p>&copy; {{ date('Y') }} {{ __('navigation.app_name') }}. All rights reserved.</p>
                            </div>
                            <div class="text-gray-500 text-xs">
                                <p>Version 2.6 | Powered by BLORIEN Tech</p>
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
    <main class="min-h-screen flex items-center justify-center bg-gradient-to-br from-blue-900 to-blue-700">
        @yield('content')
    </main>
    @endauth

    @stack('scripts')
</body>
</html>
