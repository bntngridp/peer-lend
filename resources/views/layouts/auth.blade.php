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
        @keyframes pulseOrb {
            0%, 100% { transform: scale(1) translate(0, 0); opacity: 0.6; }
            50% { transform: scale(1.15) translate(25px, -15px); opacity: 0.85; }
        }
        .green-glow-orb-top {
            position: fixed;
            top: -150px;
            left: -150px;
            width: 650px;
            height: 650px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.22) 0%, rgba(21, 128, 61, 0.12) 50%, rgba(255, 255, 255, 0) 75%);
            filter: blur(60px);
            animation: pulseOrb 8s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
        }
        .green-glow-orb-bottom {
            position: fixed;
            bottom: -150px;
            right: -150px;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21, 128, 61, 0.2) 0%, rgba(34, 197, 94, 0.1) 50%, rgba(255, 255, 255, 0) 75%);
            filter: blur(65px);
            animation: pulseOrb 10s ease-in-out infinite reverse;
            pointer-events: none;
            z-index: 0;
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-900 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Soft Page Background Ambient Green Orbs -->
    <div class="green-glow-orb-top"></div>
    <div class="green-glow-orb-bottom"></div>

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
