<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'BLORIEN Pharma')</title>

    <!-- TailwindCSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-100" x-data="{
    advancedMode: localStorage.getItem('nav_mode') !== 'basic',
    toggleMode() {
        this.advancedMode = !this.advancedMode;
        localStorage.setItem('nav_mode', this.advancedMode ? 'advanced' : 'basic');
    }
}">
    @auth
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">{{ __('navigation.app_name') }}</h1>
                    <div class="hidden md:flex space-x-4">
                        <!-- Basic Mode Links (Always visible) -->
                        <a href="{{ route('dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                            {{ __('navigation.dashboard') }}
                        </a>
                        <a href="{{ route('pos.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('pos.*') ? 'bg-blue-700' : '' }}">
                            {{ __('navigation.pos') }}
                        </a>
                        <a href="{{ route('products.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('products.*') ? 'bg-blue-700' : '' }}">
                            {{ __('navigation.products') }}
                        </a>
                        <a href="{{ route('dues.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('dues.*') ? 'bg-blue-700' : '' }}">
                            {{ __('navigation.dues_bangla') }}
                        </a>
                        <a href="{{ route('alerts') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('alerts') ? 'bg-blue-700' : '' }}">
                            {{ __('navigation.alerts') }}
                        </a>

                        <!-- Advanced Mode Links (Conditionally visible) -->
                        <template x-if="advancedMode">
                            <a href="{{ route('transactions.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('transactions.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.transactions') }}
                            </a>
                        </template>
                        <template x-if="advancedMode">
                            <a href="{{ route('customers.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('customers.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.customers') }}
                            </a>
                        </template>
                        <template x-if="advancedMode">
                            <a href="{{ route('reports.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('reports.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.reports') }}
                            </a>
                        </template>
                        <template x-if="advancedMode">
                            <a href="{{ route('daily-closing.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('daily-closing.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.daily_closing') }}
                            </a>
                        </template>
                        <template x-if="advancedMode">
                            <a href="{{ route('analytics.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('analytics.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.analytics') }}
                            </a>
                        </template>
                        @if(auth()->user()->hasRole(['owner', 'manager']))
                        <template x-if="advancedMode">
                            <a href="{{ route('suppliers.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('suppliers.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.suppliers') }}
                            </a>
                        </template>
                        <template x-if="advancedMode">
                            <a href="{{ route('users.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('users.*') ? 'bg-blue-700' : '' }}">
                                {{ __('navigation.users') }}
                            </a>
                        </template>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <!-- Language Toggle -->
                    <form action="{{ route('language.switch') }}" method="POST" class="inline">
                        @csrf
                        <input type="hidden" name="locale" value="{{ app()->getLocale() === 'en' ? 'bn' : 'en' }}">
                        <button type="submit" class="bg-green-500 hover:bg-green-700 px-3 py-2 rounded text-sm transition font-semibold" title="{{ __('navigation.toggle_language', [], app()->getLocale() === 'en' ? 'bn' : 'en') }}">
                            {{ app()->getLocale() === 'en' ? '🇧🇩 বাংলা' : '🇬🇧 English' }}
                        </button>
                    </form>

                    <!-- Navigation Mode Toggle (Phase 3B) -->
                    <button @click="toggleMode()" class="bg-blue-500 hover:bg-blue-700 px-3 py-2 rounded text-sm transition" title="{{ __('navigation.toggle_navigation') }}">
                        <span x-show="advancedMode">📋 {{ __('navigation.advanced_mode') }}</span>
                        <span x-show="!advancedMode">⚡ {{ __('navigation.basic_mode') }}</span>
                    </button>
                    <span class="text-sm">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            {{ __('navigation.logout') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>
    @endauth

    <!-- Flash Messages -->
    @if(session('success'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('success') }}</span>
        </div>
    </div>
    @endif

    @if(session('error'))
    <div class="container mx-auto px-4 mt-4">
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
            <span class="block sm:inline">{{ session('error') }}</span>
        </div>
    </div>
    @endif

    <!-- Main Content -->
    <main class="container mx-auto px-4 py-6">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-white border-t mt-8 py-4">
        <div class="container mx-auto px-4 text-center text-gray-600 text-sm">
            <p>&copy; {{ date('Y') }} BLORIEN Pharma. All rights reserved.</p>
        </div>
    </footer>

    @stack('scripts')
</body>
</html>
