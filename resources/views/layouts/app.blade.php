<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LendFlow') }} - Institutional Grade P2P Lending</title>
    <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- AlpineJS for interactive components -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        .bg-primary-green { background-color: #15803D; }
        .text-primary-green { color: #15803D; }
        .border-primary-green { border-color: #15803D; }
        .hover\:bg-primary-green-dark:hover { background-color: #166534; }
    </style>
</head>
<body class="h-full bg-slate-50 text-slate-900 antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false, logoutModalOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- ─── Left Sidebar Navigation (Collapsible & Toggleable) ────────────────── -->
        <aside class="fixed inset-y-0 left-0 z-50 bg-white border-r border-slate-200 transform transition-all duration-300 ease-in-out md:static flex flex-col shrink-0"
               :class="{
                   'translate-x-0': sidebarOpen,
                   '-translate-x-full md:translate-x-0': !sidebarOpen,
                   'w-64': !sidebarCollapsed,
                   'w-20': sidebarCollapsed
               }">
            
            <!-- Brand Logo & Header with Modern Logo Image & Toggle Button -->
            <div class="h-16 flex items-center justify-between px-4 border-b border-slate-100 select-none">
                <div class="flex items-center gap-3 overflow-hidden">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-emerald-600 via-emerald-700 to-emerald-900 p-1.5 shadow-xs ring-2 ring-emerald-500/20 flex items-center justify-center shrink-0">
                        <img src="{{ asset('images/persegi-nobg.png') }}" alt="LendFlow Logo" class="h-full w-full object-contain filter drop-shadow">
                    </div>
                    <div x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">
                        <span class="text-base font-black tracking-tight text-slate-900 block leading-none">LendFlow</span>
                        <span class="text-[9px] font-extrabold text-emerald-700 tracking-wider uppercase block mt-1">Institutional P2P</span>
                    </div>
                </div>

                <!-- Desktop Sidebar Collapse Toggle Button (Buka-Tutup Sidebar) -->
                <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" id="btn_toggle_sidebar_desktop"
                        class="hidden md:flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-slate-100 transition-colors shrink-0"
                        :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                    <svg class="h-5 w-5 transform transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                    </svg>
                </button>

                <!-- Mobile Close Button -->
                <button type="button" @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-3 py-6 space-y-1.5">
                @auth
                    <!-- Main Menu Section Header -->
                    <div x-show="!sidebarCollapsed" class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Main Navigation</div>

                    <!-- 1. Dashboard -->
                    <a href="{{ route('dashboard') }}" title="Dashboard"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard</span>
                    </a>

                    <!-- 2. Marketplace (Shopping Bag / Storefront) -->
                    <a href="{{ route('marketplace.index') }}" title="Marketplace"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('marketplace.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('marketplace.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 10.5V6a3.75 3.75 0 10-7.5 0v4.5m11.356-1.993l1.263 12c.07.665-.45 1.243-1.119 1.243H4.25a1.125 1.125 0 01-1.12-1.243l1.264-12A1.125 1.125 0 015.513 7.5h12.974c.576 0 1.059.435 1.119.993z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Marketplace</span>
                    </a>

                    <!-- 3. My Loans (Banknotes / Money) -->
                    <a href="{{ route('loans.index') }}" title="My Loans"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('loans.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('loans.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5h16.5A2.25 2.25 0 0122.5 6.75v10.5a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 17.25V6.75A2.25 2.25 0 013.75 4.5zM12 12a2.25 2.25 0 100-4.5 2.25 2.25 0 000 4.5zM6 8.25h.008v.008H6V8.25zm12 0h.008v.008H18V8.25z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">My Loans</span>
                    </a>

                    <!-- 4. Wallet & Saldo (Real Wallet Card) -->
                    <a href="{{ route('wallet.index') }}" title="Wallet & Saldo"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('wallet.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('wallet.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a2.25 2.25 0 00-2.25-2.25H15a3 3 0 11-6 0H5.25A2.25 2.25 0 003 12m18 0v6a2.25 2.25 0 01-2.25 2.25H5.25A2.25 2.25 0 013 18v-6m18 0V9M3 12V9m18 0a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 9m18 0V6a2.25 2.25 0 00-2.25-2.25H5.25A2.25 2.25 0 003 6v3"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Wallet &amp; Saldo</span>
                    </a>

                    <!-- 5. Crypto Collateral (Blockchain Cube / Shield-Coin) -->
                    <a href="{{ route('collateral.index') }}" title="Crypto Collateral"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('collateral.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('collateral.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 7.5l-9-5.25L3 7.5m18 0l-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Crypto Collateral</span>
                    </a>

                    <!-- 6. Simulasi Kalkulator (Calculator) -->
                    <a href="{{ route('calculator.index') }}" title="Simulasi Kalkulator"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('calculator.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('calculator.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm3.75-6h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zm3.75-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zM3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Simulasi Kalkulator</span>
                    </a>

                    <!-- Admin Menu Section -->
                    @if(Auth::user()->isAdmin())
                        <div x-show="!sidebarCollapsed" class="pt-4 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Administration</div>
                        <a href="{{ route('admin.kyc.index') }}" title="Review KYC"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.kyc.*') ? 'bg-amber-50 text-amber-800 border-l-4 border-amber-600 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Review KYC</span>
                        </a>
                        <a href="{{ route('admin.loans.index') }}" title="Review Loans"
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.loans.*') ? 'bg-amber-50 text-amber-800 border-l-4 border-amber-600 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Review Loans</span>
                        </a>
                    @endif

                    <!-- Account & Settings -->
                    <div x-show="!sidebarCollapsed" class="pt-4 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider whitespace-nowrap">Account Settings</div>
                    <a href="{{ route('profile.edit') }}" title="Settings & Profile"
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 shrink-0 {{ request()->routeIs('profile.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h3.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 011.37.49l1.796 3.111a1.125 1.125 0 01-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 010 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.798 3.111a1.125 1.125 0 01-1.37.491l-1.216-.456c-.356-.133-.75-.072-1.076.124a6.57 6.57 0 01-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-3.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 01-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 01-1.369-.49l-1.797-3.111a1.125 1.125 0 01.26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 010-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 01-.26-1.43l1.797-3.111a1.125 1.125 0 011.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.281z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Settings &amp; Profile</span>
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Sign in</a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800">Get started</a>
                @endauth
            </nav>

            <!-- Bottom Profile Summary -->
            @auth
            <div class="p-3 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shadow-xs shrink-0">
                        {{ strtoupper(substr(Auth::user()->profile->full_name ?? Auth::user()->email, 0, 2)) }}
                    </div>
                    <div x-show="!sidebarCollapsed" class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->profile->full_name ?? 'User' }}</p>
                        <p class="text-[11px] font-medium text-slate-500 truncate capitalize">{{ Auth::user()->roles->first()?->name ?? 'Member' }}</p>
                    </div>
                    <!-- Logout Trigger Button -->
                    <button type="button" @click="logoutModalOpen = true" id="btn_logout_trigger"
                            class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors cursor-pointer shrink-0" title="Sign out">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                    </button>
                    <!-- Hidden Form for actual POST logout -->
                    <form id="app-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                        @csrf
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- ─── Main Content Area ─────────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Navbar with Sidebar Toggle Button -->
            <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-xs">
                
                <div class="flex items-center gap-3">
                    <!-- Mobile Hamburger Menu Button -->
                    <button type="button" @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>

                    <!-- Desktop Toggle Button in Top Navbar (Buka-Tutup Sidebar) -->
                    <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" id="btn_header_toggle_sidebar"
                            class="hidden md:flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 px-3 py-1.5 rounded-xl hover:bg-slate-100 transition-colors border border-slate-200/80"
                            :title="sidebarCollapsed ? 'Buka Sidebar' : 'Tutup Sidebar'">
                        <svg class="h-4 w-4 transform transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                        </svg>
                        <span x-text="sidebarCollapsed ? 'Buka Menu' : 'Tutup Menu'">Tutup Menu</span>
                    </button>
                    
                    <!-- Search Input -->
                    <div class="relative hidden sm:block w-56 md:w-72">
                        <span class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none text-slate-400">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                        </span>
                        <input type="text" placeholder="Search marketplace, loans..." class="w-full pl-9 pr-4 py-1.5 text-xs font-medium bg-slate-100/70 border border-slate-200 rounded-xl text-slate-900 placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-emerald-600 focus:bg-white transition-all">
                    </div>
                </div>

                <!-- Right Tools -->
                <div class="flex items-center gap-3">
                    @auth
                        <!-- Apply for New Loan Button -->
                        <a href="{{ route('loans.create') }}" class="hidden sm:inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all hover:scale-[1.01] active:scale-[0.99]">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            New Application
                        </a>

                        <!-- Notification Bell -->
                        @php
                            $unreadNotifCount = \App\Models\Notification::where('user_id', Auth::id())
                                ->whereNull('read_at')
                                ->count();
                        @endphp
                        <a href="{{ route('notifications.index') }}" class="relative rounded-xl p-2 text-slate-500 hover:bg-slate-100 hover:text-slate-900 transition-colors">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                            </svg>
                            @if($unreadNotifCount > 0)
                                <span class="absolute right-1 top-1 flex h-4 w-4 items-center justify-center rounded-full bg-rose-600 text-[10px] font-bold text-white ring-2 ring-white">
                                    {{ $unreadNotifCount > 99 ? '99+' : $unreadNotifCount }}
                                </span>
                            @endif
                        </a>
                    @endauth
                </div>
            </header>

            <!-- Alert Toast Banner -->
            <div class="px-4 sm:px-6 lg:px-8 mt-4">
                @if(session('success'))
                    <div x-data="{ show: true }" x-show="show" class="rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-emerald-900 shadow-xs flex justify-between items-start">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-emerald-700 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-emerald-800">Success</p>
                                <p class="text-sm font-medium mt-0.5">{{ session('success') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-emerald-600 hover:text-emerald-800 font-bold text-lg">&times;</button>
                    </div>
                @endif

                @if(session('error'))
                    <div x-data="{ show: true }" x-show="show" class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-rose-900 shadow-xs flex justify-between items-start">
                        <div class="flex gap-3">
                            <svg class="h-5 w-5 text-rose-600 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            <div>
                                <p class="text-xs font-bold uppercase tracking-wider text-rose-800">Error</p>
                                <p class="text-sm font-medium mt-0.5">{{ session('error') }}</p>
                            </div>
                        </div>
                        <button @click="show = false" class="text-rose-600 hover:text-rose-800 font-bold text-lg">&times;</button>
                    </div>
                @endif
            </div>

            <!-- Page Content Body -->
            <main class="flex-1 p-4 sm:p-6 lg:p-8">
                @yield('content')
            </main>

            <!-- Footer -->
            <footer class="border-t border-slate-200 bg-white py-4 px-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
                <p>&copy; {{ date('Y') }} <strong>LendFlow</strong> &mdash; Institutional Grade Peer-to-Peer Lending Platform.</p>
                <div class="flex gap-4 font-medium">
                    <a href="{{ route('privacy.show') }}" class="hover:text-emerald-700">Privacy Policy</a>
                    <a href="{{ route('terms.show') }}" class="hover:text-emerald-700">Terms of Service</a>
                    <a href="#" class="hover:text-emerald-700">Support</a>
                </div>
            </footer>

        </div>
    </div>

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- LOGOUT CONFIRMATION MODAL OVERLAY -->
    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    @auth
    <div id="modal-logout-confirm" x-show="logoutModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm z-[100] flex items-center justify-center p-4"
         style="display: none;">
        
        <div class="bg-white rounded-2xl shadow-2xl border border-slate-200 w-full max-w-sm p-6 text-center overflow-hidden">
            <!-- Icon Badge with Soft Warning Glow -->
            <div class="w-12 h-12 rounded-2xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center mx-auto mb-4 shadow-xs">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9" />
                </svg>
            </div>

            <h3 class="text-base font-extrabold text-slate-900 tracking-tight">Konfirmasi Keluar / Confirm Logout</h3>
            <p class="text-xs font-medium text-slate-500 mt-1.5 leading-relaxed">
                Apakah Anda yakin ingin keluar dari akun LendFlow? Anda perlu sign in kembali untuk mengakses dasbor.
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="logoutModalOpen = false" id="btn_cancel_logout"
                        class="w-1/2 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-xs">
                    Batal
                </button>
                <button type="button" onclick="document.getElementById('app-logout-form').submit();" id="btn_confirm_logout"
                        class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
    @endauth

</body>
</html>
