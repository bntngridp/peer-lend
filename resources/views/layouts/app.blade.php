<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Peer-Lend') }} - P2P Lending Platform</title>

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
<body class="h-full flex flex-col text-gray-900 antialiased">

    <!-- Header Navbar -->
    <header class="sticky top-0 z-40 w-full border-b border-gray-200/80 bg-white/80 backdrop-blur-md" x-data="{ mobileMenuOpen: false }">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8">
            <div class="flex h-16 items-center justify-between">
                
                <!-- Logo & Left Nav -->
                <div class="flex items-center gap-8">
                    <a href="{{ route('home') }}" class="flex items-center gap-2 group">
                        <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-indigo-600 text-white shadow-lg shadow-indigo-600/20 group-hover:scale-105 transition-transform duration-200">
                            <span class="text-xl font-bold">PL</span>
                        </div>
                        <span class="text-xl font-bold tracking-tight text-gray-900 group-hover:text-indigo-600 transition-colors">Peer<span class="text-indigo-600">Lend</span></span>
                    </a>

                    <!-- Desktop Nav Links -->
                    <nav class="hidden md:flex items-center gap-6">
                        <a href="{{ route('marketplace.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Marketplace</a>
                        @auth
                            <a href="{{ route('dashboard') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Dashboard</a>
                            <a href="{{ route('loans.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">My Loans</a>
                            <a href="{{ route('wallet.index') }}" class="text-sm font-medium text-gray-600 hover:text-indigo-600 transition-colors">Wallet</a>
                        @endauth
                    </nav>
                </div>

                <!-- Right Action Buttons / User Menu -->
                <div class="flex items-center gap-4">
                    @guest
                        <a href="{{ route('login') }}" class="text-sm font-semibold text-gray-700 hover:text-indigo-600 transition-colors">Sign in</a>
                        <a href="{{ route('register') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 hover:scale-[1.02] active:scale-[0.98] transition-all">Get started</a>
                    @else
                        <!-- 🔔 In-App Notification Bell -->
                        @php
                            $unreadNotifCount = \App\Models\Notification::where('user_id', Auth::id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        <a href="{{ route('notifications.index') }}" class="relative rounded-xl p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 transition-colors" title="Notifications">
                            <span class="sr-only">View notifications</span>
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            @if($unreadNotifCount > 0)
                                <span class="absolute -right-0.5 -top-0.5 flex h-5 w-5 items-center justify-center rounded-full bg-rose-500 text-xs font-bold text-white ring-2 ring-white">
                                    {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                </span>
                            @endif
                        </a>

                        <!-- User Profile Dropdown -->
                        <div class="relative" x-data="{ open: false }" @click.away="open = false">
                            <button @click="open = !open" class="flex items-center gap-2 rounded-xl p-1 hover:bg-gray-100 transition-colors focus:outline-none">
                                <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center border border-indigo-200">
                                    {{ strtoupper(substr(Auth::user()->profile->full_name ?? Auth::user()->email, 0, 2)) }}
                                </div>
                                <span class="hidden sm:inline text-sm font-medium text-gray-700">{{ Auth::user()->profile->full_name ?? 'User' }}</span>
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <!-- Dropdown Menu -->
                            <div x-show="open" x-transition:enter="transition ease-out duration-100" x-transition:enter-start="transform opacity-0 scale-95" x-transition:enter-end="transform opacity-100 scale-100" x-transition:leave="transition ease-in duration-75" x-transition:leave-start="transform opacity-100 scale-100" x-transition:leave-end="transform opacity-0 scale-95" class="absolute right-0 mt-2.5 w-52 origin-top-right rounded-xl bg-white p-1.5 shadow-lg ring-1 ring-black/5 focus:outline-none" style="display: none;">
                                @if(Auth::user()->isAdmin())
                                    <a href="#" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                        Admin Panel
                                    </a>
                                @endif
                                <a href="{{ route('profile.edit') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    Profile Settings
                                </a>
                                <a href="{{ route('kyc.index') }}" class="flex items-center gap-2 rounded-lg px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 transition-colors">
                                    KYC Verification
                                </a>
                                <div class="my-1 border-t border-gray-100"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                        Sign out
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endguest

                    <!-- Mobile Menu Button -->
                    <button @click="mobileMenuOpen = !mobileMenuOpen" class="rounded-xl p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-900 md:hidden transition-colors">
                        <span class="sr-only">Open main menu</span>
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Mobile Menu -->
        <div x-show="mobileMenuOpen" class="border-b border-gray-200 bg-white md:hidden" style="display: none;">
            <div class="space-y-1 px-4 pb-4 pt-2">
                <a href="{{ route('marketplace.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">Marketplace</a>
                @auth
                    <a href="{{ route('dashboard') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">Dashboard</a>
                    <a href="{{ route('loans.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">My Loans</a>
                    <a href="{{ route('wallet.index') }}" class="block rounded-lg px-3 py-2 text-base font-medium text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors">Wallet</a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Alert / Toast Messages -->
    <div class="mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8 mt-4">
        @if(session('success'))
            <div x-data="{ show: true }" x-show="show" class="rounded-xl border border-emerald-200 bg-emerald-50/50 p-4 text-emerald-800 shadow-sm flex justify-between items-start" x-transition>
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-emerald-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold">Success</p>
                        <p class="text-sm mt-1 opacity-90">{{ session('success') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-emerald-500 hover:text-emerald-800"><span class="text-lg">&times;</span></button>
            </div>
        @endif

        @if(session('warning'))
            <div x-data="{ show: true }" x-show="show" class="rounded-xl border border-amber-200 bg-amber-50/50 p-4 text-amber-800 shadow-sm flex justify-between items-start" x-transition>
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-amber-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold">Warning</p>
                        <p class="text-sm mt-1 opacity-90">{{ session('warning') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-amber-500 hover:text-amber-800"><span class="text-lg">&times;</span></button>
            </div>
        @endif

        @if(session('error'))
            <div x-data="{ show: true }" x-show="show" class="rounded-xl border border-red-200 bg-red-50/50 p-4 text-red-800 shadow-sm flex justify-between items-start" x-transition>
                <div class="flex gap-3">
                    <svg class="h-5 w-5 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <div>
                        <p class="text-sm font-semibold">Error</p>
                        <p class="text-sm mt-1 opacity-90">{{ session('error') }}</p>
                    </div>
                </div>
                <button @click="show = false" class="text-red-500 hover:text-red-800"><span class="text-lg">&times;</span></button>
            </div>
        @endif
    </div>

    <!-- Main Content -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="mt-auto border-t border-gray-200 bg-white py-8">
        <div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 text-center md:flex md:items-center md:justify-between">
            <p class="text-sm text-gray-500">&copy; {{ date('Y') }} Peer-Lend. All rights reserved.</p>
            <div class="mt-4 flex justify-center gap-6 md:mt-0">
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Privacy Policy</a>
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Terms of Service</a>
                <a href="#" class="text-sm text-gray-500 hover:text-indigo-600 transition-colors">Contact Support</a>
            </div>
        </div>
    </footer>

</body>
</html>
