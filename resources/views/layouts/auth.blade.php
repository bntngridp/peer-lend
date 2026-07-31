<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'LendFlow') }} - Institutional P2P Authentication</title>
    <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Inter', sans-serif;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col justify-between">

    <!-- ─── Top Navigation Header (Matching Welcome Landing Page) ──────────── -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                <img src="{{ asset('images/persegi-panjang-liegt-mode.png') }}" alt="LendFlow Logo" class="h-8 w-auto object-contain">
                <div>
                    <span class="text-base font-bold tracking-tight text-slate-900 block leading-none">LendFlow</span>
                    <span class="text-[10px] font-semibold text-emerald-700 tracking-wider uppercase block mt-1">Institutional Grade P2P</span>
                </div>
            </a>

            <!-- Header Right Navigation Actions -->
            <div class="flex items-center gap-4">
                <a href="{{ route('calculator.index') }}" class="hidden sm:inline-flex text-xs font-bold text-slate-600 hover:text-emerald-700 tracking-wider uppercase transition-colors">
                    Simulasi Kalkulator
                </a>

                @if(request()->routeIs('login'))
                    <a href="{{ route('register') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        Create Account
                    </a>
                @elseif(request()->routeIs('register'))
                    <a href="{{ route('login') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        Sign In
                    </a>
                @else
                    <a href="{{ route('home') }}" class="text-xs font-bold text-slate-600 hover:text-slate-900 transition-colors">
                        &larr; Back to Home
                    </a>
                @endif
            </div>
        </div>
    </header>

    <!-- ─── Main Auth Page Content ─────────────────────────────────────────── -->
    <main class="flex-1 flex flex-col justify-center py-10 px-4 sm:px-6 lg:px-8">
        @yield('content')
    </main>

    <!-- ─── Minimalist Footer ──────────────────────────────────────────────── -->
    <footer class="bg-white border-t border-slate-200 py-6">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium text-slate-400">
            &copy; {{ date('Y') }} LendFlow Inc. Regulated Financial Services Platform. All rights reserved.
        </div>
    </footer>

</body>
</html>
