<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - {{ config('app.name', 'Peer-Lend') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Outfit:wght@400;500;600;700&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
        h1, h2, h3, h4, h5, h6 {
            font-family: 'Outfit', sans-serif;
        }
    </style>
</head>
<body class="h-full flex text-gray-900 antialiased" x-data="{ sidebarOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-50 bg-gray-900/80 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-300 md:static md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        
        <!-- Sidebar Header (Logo) -->
        <div class="flex h-16 items-center px-6 border-b border-gray-100">
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <div class="flex h-9 w-9 items-center justify-center rounded-xl bg-indigo-600 text-white font-bold">
                    PL
                </div>
                <span class="text-lg font-bold tracking-tight text-gray-900">PL <span class="text-indigo-600">Admin</span></span>
            </a>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 space-y-1 px-4 py-4 overflow-y-auto">
            <a href="#" class="flex items-center gap-3 rounded-lg bg-gray-50 px-3 py-2 text-sm font-medium text-indigo-600">
                Dashboard
            </a>
            
            <div class="py-2 text-[10px] font-bold tracking-wider text-gray-400 uppercase px-3">Approvals</div>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                KYC Verifications
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                Loan Applications
            </a>

            <div class="py-2 text-[10px] font-bold tracking-wider text-gray-400 uppercase px-3">Management</div>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                Users & Wallets
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                Active Contracts
            </a>

            <div class="py-2 text-[10px] font-bold tracking-wider text-gray-400 uppercase px-3">System</div>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                Global Settings
            </a>
            <a href="#" class="flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-950 transition-colors">
                Audit Logs
            </a>
        </nav>

        <!-- Sidebar Footer (User Logout) -->
        <div class="border-t border-gray-100 p-4">
            <div class="flex items-center gap-3 mb-4 px-2">
                <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center">
                    AD
                </div>
                <div class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-900 truncate">Administrator</p>
                    <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@peerlend.com' }}</p>
                </div>
            </div>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                    Log out
                </button>
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex flex-1 flex-col overflow-hidden">
        
        <!-- Admin Header -->
        <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6">
            <div class="flex items-center gap-4">
                <button @click="sidebarOpen = true" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900 md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <h1 class="text-lg font-bold text-gray-900">Admin Control Panel</h1>
            </div>
        </header>

        <!-- Dynamic Content Body -->
        <main class="flex-1 overflow-y-auto p-6 bg-gray-50/50">
            <!-- Toast notification messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-800 flex justify-between items-center" x-transition>
                    <span class="text-sm font-medium">{{ session('success') }}</span>
                    <button @click="show = false" class="text-emerald-500 hover:text-emerald-800"><span class="text-lg">&times;</span></button>
                </div>
            @endif
            @if(session('warning'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-800 flex justify-between items-center" x-transition>
                    <span class="text-sm font-medium">{{ session('warning') }}</span>
                    <button @click="show = false" class="text-amber-500 hover:text-amber-800"><span class="text-lg">&times;</span></button>
                </div>
            @endif
            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" class="mb-6 rounded-xl border border-red-200 bg-red-50 p-4 text-red-800 flex justify-between items-center" x-transition>
                    <span class="text-sm font-medium">{{ session('error') }}</span>
                    <button @click="show = false" class="text-red-500 hover:text-red-800"><span class="text-lg">&times;</span></button>
                </div>
            @endif

            @yield('content')
        </main>
    </div>

</body>
</html>
