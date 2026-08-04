<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full bg-slate-50 dark:bg-slate-950">
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

        /* ─── Dynamic Moving Green Ambient Orbs ────────────────────────────── */
        @keyframes floatOrbitOne {
            0%   { transform: translate(0px, 0px) scale(1); opacity: 0.7; }
            25%  { transform: translate(160px, 90px) scale(1.25); opacity: 0.95; }
            50%  { transform: translate(80px, 200px) scale(0.9); opacity: 0.65; }
            75%  { transform: translate(-100px, 110px) scale(1.2); opacity: 0.9; }
            100% { transform: translate(0px, 0px) scale(1); opacity: 0.7; }
        }

        @keyframes floatOrbitTwo {
            0%   { transform: translate(0px, 0px) scale(1); opacity: 0.65; }
            25%  { transform: translate(-180px, -100px) scale(1.3); opacity: 0.9; }
            50%  { transform: translate(-90px, -220px) scale(0.85); opacity: 0.6; }
            75%  { transform: translate(110px, -110px) scale(1.25); opacity: 0.95; }
            100% { transform: translate(0px, 0px) scale(1); opacity: 0.65; }
        }

        @keyframes floatOrbitThree {
            0%   { transform: translate(-50%, -50%) scale(0.9) rotate(0deg); opacity: 0.5; }
            50%  { transform: translate(-42%, -58%) scale(1.35) rotate(180deg); opacity: 0.85; }
            100% { transform: translate(-50%, -50%) scale(0.9) rotate(360deg); opacity: 0.5; }
        }

        .green-glow-orb-top {
            position: fixed;
            top: -150px;
            left: -150px;
            width: 650px;
            height: 650px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.3) 0%, rgba(21, 128, 61, 0.15) 50%, rgba(255, 255, 255, 0) 75%);
            filter: blur(60px);
            animation: floatOrbitOne 14s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
            will-change: transform, opacity;
        }

        .green-glow-orb-bottom {
            position: fixed;
            bottom: -150px;
            right: -150px;
            width: 700px;
            height: 700px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(21, 128, 61, 0.28) 0%, rgba(34, 197, 94, 0.14) 50%, rgba(255, 255, 255, 0) 75%);
            filter: blur(65px);
            animation: floatOrbitTwo 16s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
            will-change: transform, opacity;
        }

        .green-glow-orb-center {
            position: fixed;
            top: 50%;
            left: 50%;
            width: 800px;
            height: 800px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(34, 197, 94, 0.2) 0%, rgba(22, 101, 52, 0.1) 60%, rgba(255, 255, 255, 0) 80%);
            filter: blur(80px);
            animation: floatOrbitThree 20s ease-in-out infinite;
            pointer-events: none;
            z-index: 0;
            will-change: transform, opacity;
        }
    </style>
    <script>
        (function() {
            const theme = localStorage.getItem('lendflow_theme') ?? 'light';
            if (theme === 'dark') {
                document.documentElement.classList.add('dark');
            } else {
                document.documentElement.classList.remove('dark');
            }
        })();
    </script>
</head>
<body class="bg-slate-50 dark:bg-slate-950 text-slate-900 dark:text-slate-100 antialiased min-h-screen flex flex-col justify-between relative overflow-x-hidden">

    <!-- Dynamic Moving Background Green Ambient Orbs -->
    <div class="green-glow-orb-top"></div>
    <div class="green-glow-orb-bottom"></div>
    <div class="green-glow-orb-center"></div>

    <!-- Main Auth Page Content -->
    <main class="flex-1 flex flex-col justify-center py-12 px-4 sm:px-6 lg:px-8 relative z-10">
        @yield('content')
    </main>

    <!-- Minimalist Footer -->
    <footer class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-md border-t border-slate-200 dark:border-slate-800 py-4 relative z-10">
        <div class="max-w-7xl mx-auto px-4 text-center text-xs font-medium text-slate-400 dark:text-slate-500">
            &copy; {{ date('Y') }} LendFlow Inc. Regulated Financial Services Platform. All rights reserved.
        </div>
    </footer>

</body>
</html>
