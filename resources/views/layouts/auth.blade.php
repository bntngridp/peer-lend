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
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">

    <!-- Styles & Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body {
            font-family: 'Plus Jakarta Sans', sans-serif;
        }
        @keyframes floatGlow {
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.5; }
            50% { transform: translate(25px, -15px) scale(1.15); opacity: 0.8; }
        }
        .animate-glow-slow {
            animation: floatGlow 8s ease-in-out infinite;
        }
        .animate-glow-reverse {
            animation: floatGlow 10s ease-in-out infinite reverse;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Rich Ambient Green Background Gradient Blobs -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <div class="absolute -top-20 -left-20 h-[32rem] w-[32rem] rounded-full bg-gradient-to-br from-emerald-400/40 to-teal-500/30 blur-3xl animate-glow-slow"></div>
        <div class="absolute -bottom-20 -right-20 h-[36rem] w-[36rem] rounded-full bg-gradient-to-tl from-emerald-500/45 to-emerald-300/30 blur-3xl animate-glow-reverse"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[45rem] w-[45rem] rounded-full bg-emerald-400/20 blur-[140px]"></div>
    </div>

    <!-- Main Auth Page Content -->
    <main class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        @yield('content')
    </main>

    <!-- Minimalist Footer -->
    <footer class="bg-white/80 backdrop-blur-md border-t border-slate-200 py-4 relative z-10">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium text-slate-400">
            &copy; {{ date('Y') }} LendFlow Inc. Regulated Financial Services Platform. All rights reserved.
        </div>
    </footer>

</body>
</html>
