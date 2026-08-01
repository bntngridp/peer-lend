<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-gray-100">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Panel - {{ config('app.name', 'Peer-Lend') }}</title>
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
        body, h1, h2, h3, h4, h5, h6 {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
    </style>
</head>
<body class="h-full flex text-gray-900 antialiased" x-data="{ sidebarOpen: false, sidebarCollapsed: false, logoutModalOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-50 bg-gray-900/80 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-gray-200 bg-white transition-all duration-300 ease-in-out md:static md:translate-x-0"
           :class="{
               'translate-x-0': sidebarOpen,
               '-translate-x-full md:translate-x-0': !sidebarOpen,
               'w-64': !sidebarCollapsed,
               'w-64 md:w-20': sidebarCollapsed
           }">
        
        <!-- Sidebar Header (Logo & Collapse Toggle) -->
        <div class="flex h-16 items-center px-4 border-b border-gray-100 justify-between select-none overflow-hidden shrink-0">
            <a href="{{ route('home') }}" class="flex items-center gap-3 min-w-0 group" title="LendFlow Home">
                <div class="w-10 h-10 rounded-2xl bg-gradient-to-tr from-indigo-800 via-indigo-600 to-sky-400 p-1.5 shadow-md shadow-indigo-700/20 shrink-0 flex items-center justify-center group-hover:scale-105 transition-transform duration-200">
                    <img src="{{ asset('images/persegi-nobg.png') }}" alt="LendFlow Logo" class="w-full h-full object-contain filter drop-shadow-sm">
                </div>
                <div x-show="!sidebarCollapsed" x-transition.opacity class="min-w-0">
                    <div class="flex items-center gap-1.5">
                        <span class="text-base font-black tracking-tight text-slate-900 leading-none">LendFlow</span>
                        <span class="rounded bg-indigo-50 px-1.5 py-0.5 text-[9px] font-bold text-indigo-600 border border-indigo-100">ADMIN</span>
                    </div>
                    <span class="text-[9px] font-bold text-indigo-600 tracking-wider uppercase block mt-1">Control Panel</span>
                </div>
            </a>

            <!-- Desktop Toggle Button -->
            <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" id="btn_toggle_admin_sidebar"
                    class="hidden md:flex items-center justify-center p-1.5 text-slate-400 hover:text-indigo-700 rounded-xl hover:bg-slate-100 transition-colors shrink-0"
                    :title="sidebarCollapsed ? 'Buka Sidebar / Expand' : 'Tutup Sidebar / Collapse'">
                <svg class="w-5 h-5 transform transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M11 19l-7-7 7-7m8 14l-7-7 7-7" />
                </svg>
            </button>

            <!-- Mobile Close Button -->
            <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-slate-600 p-1 rounded-lg">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Dashboard Overview' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Dashboard Overview</span>
            </a>
            
            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Approvals</div>
            <a href="{{ route('admin.kyc.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'KYC Verifications' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">KYC Verifications</span>
            </a>
            <a href="{{ route('admin.loans.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Loan Approvals' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Loan Approvals</span>
            </a>

            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Governance</div>
            <a href="{{ route('admin.users.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'User Management' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6 0 3.375 3.375 0 016 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">User Management</span>
            </a>
            <a href="{{ route('admin.financials.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Financial Configuration' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Financial Configuration</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Role Management' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Role Management</span>
            </a>

            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Monitoring</div>
            <a href="{{ route('admin.transactions.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Transaction Monitoring' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 18.75a60.07 60.07 0 0115.797 2.101c.727.198 1.453-.342 1.453-1.096V18.75M3.75 4.5v.75A.75.75 0 013 6h-.75m0 0v.375c0 .621.504 1.125 1.125 1.125H20.25M2.25 6v9m18-9v9m0-9a2.25 2.25 0 00-2.25-2.25H4.5A2.25 2.25 0 002.25 6m18 0v6M2.25 6v6"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Transaction Monitoring</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" 
               :class="sidebarCollapsed ? 'justify-center px-0' : 'px-3.5'"
               class="flex items-center gap-3 rounded-xl py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all"
               :title="sidebarCollapsed ? 'Platform Analytics' : ''">
                <svg class="h-5 w-5 text-indigo-600 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3 13.125C3 12.504 3.504 12 4.125 12h2.25c.621 0 1.125.504 1.125 1.125v6.75C7.5 20.496 6.996 21 6.375 21h-2.25A2.25 2.25 0 013 18.75v-5.625zM10.5 7.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v12c0 .621-.504 1.125-1.125 1.125h-2.25a2.25 2.25 0 01-2.25-2.25v-12zM18 3.375c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125v16.5c0 .621-.504 1.125-1.125 1.125h-2.25a2.25 2.25 0 01-2.25-2.25V3.375z"/></svg>
                <span x-show="!sidebarCollapsed" class="truncate">Platform Analytics</span>
            </a>
        </nav>

        <!-- Sidebar Footer (User Logout) -->
        <div class="border-t border-gray-100 p-3 shrink-0">
            <div class="flex items-center" :class="sidebarCollapsed ? 'justify-center' : 'gap-3 mb-3 px-2'">
                <div class="h-9 w-9 rounded-xl bg-indigo-100 text-indigo-700 font-semibold flex items-center justify-center shrink-0">
                    AD
                </div>
                <div x-show="!sidebarCollapsed" class="min-w-0 flex-1">
                    <p class="text-xs font-semibold text-gray-900 truncate">Administrator</p>
                    <p class="text-[10px] text-gray-500 truncate">{{ Auth::user()->email ?? 'admin@peerlend.com' }}</p>
                </div>
            </div>
            <!-- Trigger Logout Modal -->
            <button type="button" @click="logoutModalOpen = true" id="btn_admin_logout_trigger"
                    :class="sidebarCollapsed ? 'hidden' : 'flex w-full'"
                    class="items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer">
                Log out
            </button>
            <form id="admin-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
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

    <!-- ═══════════════════════════════════════════════════════════════════════ -->
    <!-- ADMIN LOGOUT CONFIRMATION MODAL OVERLAY -->
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
                Apakah Anda yakin ingin keluar dari Admin Panel LendFlow?
            </p>

            <div class="mt-6 flex items-center justify-center gap-3">
                <button type="button" @click="logoutModalOpen = false" id="btn_cancel_admin_logout"
                        class="w-1/2 py-2.5 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs transition-colors shadow-xs">
                    Batal
                </button>
                <button type="button" onclick="document.getElementById('admin-logout-form').submit();" id="btn_confirm_admin_logout"
                        class="w-1/2 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs transition-colors shadow-xs cursor-pointer">
                    Ya, Keluar
                </button>
            </div>
        </div>
    </div>
    @endauth

</body>
</html>
