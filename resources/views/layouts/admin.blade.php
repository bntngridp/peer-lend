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

    <!-- Sidebar Navigation (Collapsible & Toggleable) -->
    <aside class="fixed inset-y-0 left-0 z-50 flex flex-col border-r border-gray-200 bg-white transition-all duration-300 md:static shrink-0"
           :class="{
               'translate-x-0': sidebarOpen,
               '-translate-x-full md:translate-x-0': !sidebarOpen,
               'w-64': !sidebarCollapsed,
               'w-20': sidebarCollapsed
           }">
        
        <!-- Sidebar Header (Matching Welcome Page 'L' Logo Badge) -->
        <div class="flex h-16 items-center px-4 border-b border-gray-100 justify-between select-none">
            <div class="flex items-center gap-3 overflow-hidden">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs shrink-0 select-none">L</span>
                <div x-show="!sidebarCollapsed" class="whitespace-nowrap transition-opacity duration-200">
                    <span class="text-base font-extrabold tracking-tight text-slate-900 block leading-none">LendFlow</span>
                    <span class="text-[9px] font-bold text-indigo-600 tracking-wider uppercase block mt-1">ADMIN CONTROL</span>
                </div>
            </div>
            
            <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" id="btn_admin_toggle_sidebar_desktop"
                    class="hidden md:flex items-center justify-center h-8 w-8 rounded-lg text-slate-400 hover:text-emerald-700 hover:bg-slate-100 transition-colors shrink-0"
                    :title="sidebarCollapsed ? 'Expand Sidebar' : 'Collapse Sidebar'">
                <svg class="h-5 w-5 transform transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M18.75 19.5l-7.5-7.5 7.5-7.5m-6 15L5.25 12l7.5-7.5" />
                </svg>
            </button>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 space-y-1 px-3 py-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" title="Dashboard Overview"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Dashboard Overview</span>
            </a>
            
            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3 whitespace-nowrap">Approvals</div>
            <a href="{{ route('admin.kyc.index') }}" title="KYC Verifications"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">KYC Verifications</span>
            </a>
            <a href="{{ route('admin.loans.index') }}" title="Loan Approvals"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-amber-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Loan Approvals</span>
            </a>

            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3 whitespace-nowrap">Governance</div>
            <a href="{{ route('admin.users.index') }}" title="User Management"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-indigo-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6 0 3.375 3.375 0 016 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">User Management</span>
            </a>
            <a href="{{ route('admin.financials.index') }}" title="Financial Configuration"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-emerald-600" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-6h6m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Financial Configuration</span>
            </a>
            <a href="{{ route('admin.roles.index') }}" title="Role Management"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Role Management</span>
            </a>

            <div x-show="!sidebarCollapsed" class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3 whitespace-nowrap">Monitoring</div>
            <a href="{{ route('admin.transactions.index') }}" title="Transaction Monitoring"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 14.25v2.25m3-4.5v4.5m3-6.75v6.75m3-9v9M6 20.25h12A2.25 2.25 0 0020.25 18V6A2.25 2.25 0 0018 3.75H6A2.25 2.25 0 003.75 6v12A2.25 2.25 0 006 20.25z"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Transaction Monitoring</span>
            </a>
            <a href="{{ route('admin.analytics.index') }}" title="Platform Analytics"
               class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                <svg class="h-5 w-5 shrink-0 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 3v11.25A2.25 2.25 0 006 16.5h2.25M3.75 3h-1.5m1.5 0h16.5m0 0h1.5m-1.5 0v11.25A2.25 2.25 0 0118 16.5h-2.25m-7.5 0h7.5m-7.5 0l-1 3m8.5-3l1 3m0 0l.5 1.5m-.5-1.5h-9.5m0 0l-.5 1.5m.75-9l3-3 2.148 2.148A12.061 12.061 0 0116.5 7.605"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Platform Analytics</span>
            </a>
        </nav>

        <!-- Sidebar Footer (User Logout) -->
        <div class="border-t border-gray-100 p-3">
            <div class="flex items-center gap-3 mb-3 px-1">
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
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer shrink-0">
                <svg class="h-5 w-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15m3 0l3-3m0 0l-3-3m3 3H9"/></svg>
                <span x-show="!sidebarCollapsed" class="whitespace-nowrap">Log out</span>
            </button>
            <form id="admin-logout-form" method="POST" action="{{ route('logout') }}" class="hidden">
                @csrf
            </form>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="flex flex-1 flex-col overflow-hidden min-w-0">
        
        <!-- Admin Header with Sidebar Toggle -->
        <header class="flex h-16 items-center justify-between border-b border-gray-200 bg-white px-6">
            <div class="flex items-center gap-4">
                <button type="button" @click="sidebarOpen = true" class="rounded-lg p-1.5 text-gray-500 hover:bg-gray-100 hover:text-gray-900 md:hidden">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                    </svg>
                </button>
                <button type="button" @click="sidebarCollapsed = !sidebarCollapsed" id="btn_admin_header_toggle"
                        class="hidden md:flex items-center gap-2 text-xs font-bold text-slate-600 hover:text-emerald-700 px-3 py-1.5 rounded-xl hover:bg-slate-100 transition-colors border border-slate-200/80">
                    <svg class="h-4 w-4 transform transition-transform duration-300" :class="sidebarCollapsed ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/>
                    </svg>
                    <span x-text="sidebarCollapsed ? 'Buka Menu' : 'Tutup Menu'">Tutup Menu</span>
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
