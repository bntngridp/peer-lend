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
<body class="h-full flex text-gray-900 antialiased" x-data="{ sidebarOpen: false, logoutModalOpen: false }">

    <!-- Mobile Sidebar Backdrop -->
    <div x-show="sidebarOpen" class="fixed inset-0 z-50 bg-gray-900/80 md:hidden" @click="sidebarOpen = false" style="display: none;"></div>

    <!-- Sidebar Navigation -->
    <aside class="fixed inset-y-0 left-0 z-50 flex w-64 flex-col border-r border-gray-200 bg-white transition-transform duration-300 md:static md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full md:translate-x-0'">
        
        <!-- Sidebar Header (Logo - Non-clickable static header) -->
        <div class="flex h-16 items-center px-6 border-b border-gray-100 justify-between select-none">
            <div class="flex items-center gap-2">
                <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-base shadow-xs shrink-0">L</span>
                <span class="text-base font-extrabold tracking-tight text-slate-900 block leading-none">LendFlow</span>
            </div>
            <span class="rounded bg-indigo-50 px-2 py-0.5 text-xs font-bold text-indigo-600 border border-indigo-100">ADMIN</span>
        </div>

        <!-- Sidebar Navigation Menu -->
        <nav class="flex-1 space-y-1 px-4 py-4 overflow-y-auto">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Dashboard Overview
            </a>
            
            <div class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Approvals</div>
            <a href="{{ route('admin.kyc.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                KYC Verifications
            </a>
            <a href="{{ route('admin.loans.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Loan Approvals
            </a>

            <div class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Governance</div>
            <a href="{{ route('admin.users.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                User Management
            </a>
            <a href="{{ route('admin.financials.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Financial Configuration
            </a>
            <a href="{{ route('admin.roles.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Role Management
            </a>

            <div class="py-2 text-[10px] font-bold tracking-wider text-slate-400 uppercase px-3">Monitoring</div>
            <a href="{{ route('admin.transactions.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Transaction Monitoring
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-all">
                Platform Analytics
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
            <!-- Trigger Logout Modal -->
            <button type="button" @click="logoutModalOpen = true" id="btn_admin_logout_trigger"
                    class="flex w-full items-center gap-2 rounded-lg px-3 py-2 text-sm font-medium text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors cursor-pointer">
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
