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
<body class="bg-gray-100">
    @auth
    <!-- Navigation -->
    <nav class="bg-blue-600 text-white shadow-lg">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-bold">BLORIEN Pharma</h1>
                    <div class="hidden md:flex space-x-4">
                        <a href="{{ route('dashboard') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('dashboard') ? 'bg-blue-700' : '' }}">
                            Dashboard
                        </a>
                        <a href="{{ route('pos.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('pos.*') ? 'bg-blue-700' : '' }}">
                            POS
                        </a>
                        <a href="{{ route('products.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('products.*') ? 'bg-blue-700' : '' }}">
                            Products
                        </a>
                        <a href="{{ route('transactions.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('transactions.*') ? 'bg-blue-700' : '' }}">
                            Transactions
                        </a>
                        <a href="{{ route('alerts') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('alerts') ? 'bg-blue-700' : '' }}">
                            Alerts
                        </a>
                        <a href="{{ route('customers.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('customers.*') ? 'bg-blue-700' : '' }}">
                            Customers
                        </a>
                        <a href="{{ route('reports.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('reports.*') ? 'bg-blue-700' : '' }}">
                            Reports
                        </a>
                        <a href="{{ route('analytics.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('analytics.*') ? 'bg-blue-700' : '' }}">
                            Analytics
                        </a>
                        @if(auth()->user()->hasRole(['owner', 'manager']))
                        <a href="{{ route('suppliers.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('suppliers.*') ? 'bg-blue-700' : '' }}">
                            Suppliers
                        </a>
                        <a href="{{ route('users.index') }}" class="hover:bg-blue-700 px-3 py-2 rounded {{ request()->routeIs('users.*') ? 'bg-blue-700' : '' }}">
                            Users
                        </a>
                        @endif
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <span class="text-sm">{{ auth()->user()->name }} ({{ ucfirst(auth()->user()->role) }})</span>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="bg-red-500 hover:bg-red-600 px-4 py-2 rounded">
                            Logout
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
