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
<body class="h-full bg-slate-50 text-slate-900 antialiased" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex flex-col md:flex-row">

        <!-- ─── Left Sidebar Navigation ───────────────────────────────────────────── -->
        <aside class="fixed inset-y-0 left-0 z-50 w-64 bg-white border-r border-slate-200 transform transition-transform duration-200 ease-in-out md:static md:translate-x-0 flex flex-col"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
            
            <!-- Brand Logo & Header -->
            <div class="h-16 flex items-center justify-between px-6 border-b border-slate-100">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <img src="{{ asset('images/persegi-panjang-liegt-mode.png') }}" alt="LendFlow Logo" class="h-8 w-auto object-contain">
                    <div>
                        <span class="text-base font-bold tracking-tight text-slate-900 block leading-none">LendFlow</span>
                        <span class="text-[10px] font-semibold text-emerald-700 tracking-wider uppercase block mt-1">Institutional Grade P2P</span>
                    </div>
                </a>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="flex-1 overflow-y-auto px-4 py-6 space-y-1.5">
                @auth
                    <!-- Main Menu -->
                    <div class="px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Main Navigation</div>

                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('dashboard') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('dashboard') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/>
                        </svg>
                        Dashboard
                    </a>

                    <a href="{{ route('marketplace.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('marketplace.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('marketplace.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 21v-7.5a.75.75 0 01.75-.75h3a.75.75 0 01.75.75V21m-4.5 0H2.36rem-3.75 0h16.5m-16.5 0a2.25 2.25 0 01-2.25-2.25V6.75A2.25 2.25 0 013.75 4.5h16.5a2.25 2.25 0 012.25 2.25v12a2.25 2.25 0 01-2.25 2.25H3.75z"/>
                        </svg>
                        Marketplace
                    </a>

                    <a href="{{ route('loans.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('loans.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('loans.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                        </svg>
                        My Loans
                    </a>

                    <a href="{{ route('wallet.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('wallet.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('wallet.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0zM15.91 11.672a.375.375 0 010 .656l-5.603 3.113a.375.375 0 01-.557-.328V8.887c0-.286.307-.466.557-.327l5.603 3.112z"/>
                        </svg>
                        Wallet &amp; Saldo
                    </a>

                    <a href="{{ route('collateral.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('collateral.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('collateral.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                        Crypto Collateral
                    </a>

                    <a href="{{ route('calculator.index') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('calculator.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 {{ request()->routeIs('calculator.*') ? 'text-emerald-700' : 'text-slate-400' }}" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 15.75V18m-7.5-6.75h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm0 3h.008v.008H8.25v-.008zm3.75-6h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zm0 3h.008v.008H12v-.008zm3.75-6h.008v.008h-.008v-.008zm0 3h.008v.008h-.008v-.008zM3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z"/>
                        </svg>
                        Simulasi Kalkulator
                    </a>

                    <!-- Admin Menu Section -->
                    @if(Auth::user()->isAdmin())
                        <div class="pt-4 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Administration</div>
                        <a href="{{ route('admin.kyc.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.kyc.*') ? 'bg-amber-50 text-amber-800 border-l-4 border-amber-600 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/>
                            </svg>
                            Review KYC
                        </a>
                        <a href="{{ route('admin.loans.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('admin.loans.*') ? 'bg-amber-50 text-amber-800 border-l-4 border-amber-600 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                            <svg class="h-5 w-5 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/>
                            </svg>
                            Review Loans
                        </a>
                    @endif

                    <!-- Account & Settings -->
                    <div class="pt-4 px-3 pb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider">Account Settings</div>
                    <a href="{{ route('profile.edit') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold transition-all {{ request()->routeIs('profile.*') ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900' }}">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.343 3.94c.09-.542.56-.94 1.11-.94h1.093c.55 0 1.02.398 1.11.94l.149.894c.07.424.384.764.78.93.398.164.855.142 1.205-.108l.737-.527a1.125 1.125 0 011.45.12l.773.774c.39.389.44 1.002.12 1.45l-.527.737c-.25.35-.272.806-.107 1.204.165.397.505.71.93.78l.893.15c.543.09.94.56.94 1.109v1.094c0 .55-.397 1.02-.94 1.11l-.894.149c-.424.07-.764.383-.929.78-.165.398-.143.854.107 1.204l.527.738c.32.447.269 1.06-.12 1.45l-.774.773a1.125 1.125 0 01-1.449.12l-.738-.527c-.35-.25-.806-.272-1.203-.107-.398.165-.71.505-.781.929l-.149.894c-.09.542-.56.94-1.11.94h-1.094c-.55 0-1.019-.398-1.11-.94l-.148-.894c-.071-.424-.384-.764-.781-.93-.398-.164-.854-.142-1.204.108l-.738.527a1.125 1.125 0 01-1.45-.12l-.773-.774a1.125 1.125 0 01-.12-1.45l.527-.737c.25-.35.272-.806.108-1.204-.165-.397-.506-.71-.93-.78l-.894-.15c-.542-.09-.94-.56-.94-1.109v-1.094c0-.55.398-1.02.94-1.11l.894-.149c.424-.07.765-.383.93-.78.165-.398.143-.854-.108-1.204l-.527-.738a1.125 1.125 0 01.12-1.45l.773-.773a1.125 1.125 0 011.45-.12l.737.527c.35.25.807.272 1.204.107.397-.165.71-.505.78-.929l.149-.894z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                        Settings &amp; Profile
                    </a>
                @else
                    <a href="{{ route('login') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold text-slate-700 hover:bg-slate-100">Sign in</a>
                    <a href="{{ route('register') }}" class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-sm font-semibold bg-emerald-700 text-white hover:bg-emerald-800">Get started</a>
                @endauth
            </nav>

            <!-- Bottom Profile Summary -->
            @auth
            <div class="p-4 border-t border-slate-100 bg-slate-50/50">
                <div class="flex items-center gap-3">
                    <div class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-bold flex items-center justify-center text-xs shadow-xs">
                        {{ strtoupper(substr(Auth::user()->profile->full_name ?? Auth::user()->email, 0, 2)) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-bold text-slate-900 truncate">{{ Auth::user()->profile->full_name ?? 'User' }}</p>
                        <p class="text-[11px] font-medium text-slate-500 truncate capitalize">{{ Auth::user()->roles->first()?->name ?? 'Member' }}</p>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="p-1.5 text-slate-400 hover:text-rose-600 rounded-lg hover:bg-rose-50 transition-colors" title="Sign out">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                        </button>
                    </form>
                </div>
            </div>
            @endauth
        </aside>

        <!-- ─── Main Content Area ─────────────────────────────────────────────────── -->
        <div class="flex-1 flex flex-col min-w-0">

            <!-- Top Header Navbar -->
            <header class="h-16 bg-white border-b border-slate-200 sticky top-0 z-40 flex items-center justify-between px-4 sm:px-6 lg:px-8 shadow-xs">
                
                <div class="flex items-center gap-4">
                    <button @click="sidebarOpen = true" class="md:hidden text-slate-500 hover:text-slate-700 p-1.5 rounded-lg hover:bg-slate-100">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                    
                    <!-- Search Input -->
                    <div class="relative hidden sm:block w-64 md:w-80">
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
                    <a href="#" class="hover:text-emerald-700">Privacy Policy</a>
                    <a href="#" class="hover:text-emerald-700">Terms of Service</a>
                    <a href="#" class="hover:text-emerald-700">Support</a>
                </div>
            </footer>

        </div>
    </div>

</body>
</html>
