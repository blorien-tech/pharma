<!-- Sidebar Navigation Component -->
<div
    x-data="{
        collapsed: $persist(window.__sidebarCollapsed || false).as('sidebar_collapsed'),
        mobileMenuOpen: false,
        init() {
            // Dispatch initial state to parent
            this.$dispatch('sidebar-collapse-changed', { collapsed: this.collapsed });
        },
        toggleCollapse() {
            this.collapsed = !this.collapsed;
            this.$dispatch('sidebar-collapse-changed', { collapsed: this.collapsed });
        }
    }"
    @toggle-mobile-menu.window="mobileMenuOpen = !mobileMenuOpen"
    :class="collapsed ? 'lg:w-20' : 'lg:w-64'"
    class="fixed left-0 top-0 h-screen bg-gradient-to-b from-blue-900 to-blue-800 text-white shadow-xl transition-all duration-300 z-50 flex flex-col w-64 lg:translate-x-0"
    :class="mobileMenuOpen ? 'translate-x-0' : '-translate-x-full lg:translate-x-0'"
>
    <!-- Logo & Branding -->
    <div class="p-4 border-b border-blue-700">
        <!-- Expanded State -->
        <div x-show="!collapsed" class="flex items-center justify-between" x-transition>
            <div class="flex items-center space-x-3">
                <!-- Logo/Icon -->
                <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-lg">
                    <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                    </svg>
                </div>
                <!-- Brand Name -->
                <div class="overflow-hidden">
                    <h1 class="text-lg font-bold leading-tight">{{ __('navigation.app_name') }}</h1>
                    <p class="text-xs text-blue-300">{{ __('common.pharmacy_system') }}</p>
                </div>
            </div>

            <div class="flex items-center space-x-2">
                <!-- Mobile Close Button -->
                <button
                    @click="mobileMenuOpen = false; $dispatch('toggle-mobile-menu')"
                    class="p-2 hover:bg-blue-700 rounded-lg transition lg:hidden"
                    :title="'{{ __('common.close') }}'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>

                <!-- Desktop Collapse Toggle (Collapse) -->
                <button
                    @click="toggleCollapse()"
                    class="p-2 hover:bg-blue-700 rounded-lg transition hidden lg:block"
                    :title="'{{ __('navigation.collapse_sidebar') }}'"
                >
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 19l-7-7 7-7m8 14l-7-7 7-7"/>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Collapsed State -->
        <div x-show="collapsed" class="flex flex-col items-center space-y-3" x-transition>
            <!-- Logo/Icon -->
            <div class="inline-flex items-center justify-center w-12 h-12 bg-gradient-to-br from-blue-600 to-indigo-600 rounded-2xl shadow-lg">
                <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                </svg>
            </div>

            <!-- Desktop Expand Toggle -->
            <button
                @click="toggleCollapse()"
                class="p-2 hover:bg-blue-700 rounded-lg transition hidden lg:block w-full"
                :title="'{{ __('navigation.expand_sidebar') }}'"
            >
                <svg class="w-5 h-5 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 5l7 7-7 7M5 5l7 7-7 7"/>
                </svg>
            </button>
        </div>
    </div>

    <!-- Navigation Menu -->
    <nav class="flex-1 overflow-y-auto py-4 scrollbar-thin scrollbar-thumb-blue-700 scrollbar-track-blue-900">
        <ul class="space-y-1 px-3">
            <!-- Dashboard -->
            <li>
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('dashboard') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.dashboard') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.dashboard') }}</span>
                </a>
            </li>

            <!-- POS (Point of Sale) -->
            <li>
                <a
                    href="{{ route('pos.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('pos.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.pos') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.pos') }}</span>
                </a>
            </li>

            <!-- Products -->
            <li>
                <a
                    href="{{ route('products.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('products.*') || request()->routeIs('batches.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.products') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.products') }}</span>
                </a>
            </li>

            <!-- Dues  -->
            <li>
                <a
                    href="{{ route('dues.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('dues.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.dues_bangla') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.dues_bangla') }}</span>
                </a>
            </li>

            <!-- Alerts -->
            <li>
                <a
                    href="{{ route('alerts') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('alerts') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.alerts') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.alerts') }}</span>
                    @if(isset($lowStockCount) && $lowStockCount > 0)
                        <span x-show="!collapsed" class="ml-auto bg-red-500 text-white text-xs px-2 py-1 rounded-full" x-transition>{{ $lowStockCount }}</span>
                    @endif
                </a>
            </li>

            <!-- Divider -->
            <li class="my-3">
                <hr class="border-blue-700">
            </li>

            <!-- Transactions -->
            <li>
                <a
                    href="{{ route('transactions.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('transactions.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.transactions') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.transactions') }}</span>
                </a>
            </li>

            <!-- Customers -->
            <li>
                <a
                    href="{{ route('customers.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('customers.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.customers') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.customers') }}</span>
                </a>
            </li>

            @if(auth()->user()->hasRole(['owner', 'manager']))
            <!-- Suppliers -->
            <li>
                <a
                    href="{{ route('suppliers.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('suppliers.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.suppliers') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.suppliers') }}</span>
                </a>
            </li>
            @endif

            <!-- Divider -->
            <li class="my-3">
                <hr class="border-blue-700">
            </li>

            <!-- Reports -->
            <li>
                <a
                    href="{{ route('reports.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('reports.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.reports') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.reports') }}</span>
                </a>
            </li>

            <!-- Analytics -->
            <li>
                <a
                    href="{{ route('analytics.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('analytics.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.analytics') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 8v8m-4-5v5m-4-2v2m-2 4h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.analytics') }}</span>
                </a>
            </li>

            <!-- Daily Closing -->
            <li>
                <a
                    href="{{ route('daily-closing.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('daily-closing.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.daily_closing') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.daily_closing') }}</span>
                </a>
            </li>

            @if(auth()->user()->hasRole(['owner', 'manager']))
            <!-- Divider -->
            <li class="my-3">
                <hr class="border-blue-700">
            </li>

            <!-- Users -->
            <li>
                <a
                    href="{{ route('users.index') }}"
                    class="flex items-center space-x-3 px-3 py-3 rounded-lg transition {{ request()->routeIs('users.*') ? 'bg-blue-700 shadow-md' : 'hover:bg-blue-700/50' }}"
                    :title="collapsed ? '{{ __('navigation.users') }}' : ''"
                >
                    <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                    <span x-show="!collapsed" class="font-medium" x-transition>{{ __('navigation.users') }}</span>
                </a>
            </li>
            @endif
        </ul>
    </nav>

    <!-- Footer / User Info -->
    <div class="p-4 border-t border-blue-700 bg-blue-950">
        <div class="flex items-center space-x-3">
            <div class="w-10 h-10 bg-blue-700 rounded-full flex items-center justify-center flex-shrink-0">
                <span class="text-white font-semibold text-sm">{{ substr(auth()->user()->name, 0, 2) }}</span>
            </div>
            <div x-show="!collapsed" class="overflow-hidden" x-transition>
                <p class="text-sm font-medium truncate">{{ auth()->user()->name }}</p>
                <p class="text-xs text-blue-300">{{ ucfirst(auth()->user()->role) }}</p>
            </div>
        </div>
    </div>
</div>

<!-- Mobile Overlay (when sidebar is open) - Managed by app.blade.php -->
<!-- Overlay is now in app.blade.php for better state management -->

<!-- Custom Scrollbar Styles -->
<style>
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    .scrollbar-thumb-blue-700::-webkit-scrollbar-thumb {
        background-color: rgb(29, 78, 216);
        border-radius: 3px;
    }
    .scrollbar-track-blue-900::-webkit-scrollbar-track {
        background-color: rgb(30, 58, 138);
    }
</style>
