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
            0%, 100% { transform: translate(0, 0) scale(1); opacity: 0.25; }
            50% { transform: translate(30px, -20px) scale(1.1); opacity: 0.4; }
        }
        .animate-glow-slow {
            animation: floatGlow 10s ease-in-out infinite;
        }
        .animate-glow-reverse {
            animation: floatGlow 12s ease-in-out infinite reverse;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Ambient Green Background Glow Blobs -->
    <div class="pointer-events-none fixed inset-0 z-0 overflow-hidden">
        <div class="absolute -top-32 -left-32 h-[30rem] w-[30rem] rounded-full bg-emerald-400/25 blur-3xl animate-glow-slow"></div>
        <div class="absolute -bottom-32 -right-32 h-[32rem] w-[32rem] rounded-full bg-emerald-500/20 blur-3xl animate-glow-reverse"></div>
        <div class="absolute top-1/2 left-1/2 -translate-x-1/2 -translate-y-1/2 h-[40rem] w-[40rem] rounded-full bg-emerald-300/15 blur-[120px] pointer-events-none"></div>
    </div>

    <!-- Main Auth Page Content (No Top Header Bar) -->
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
