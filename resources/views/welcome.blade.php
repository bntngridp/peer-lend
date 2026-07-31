<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="LendFlow — Institutional Grade P2P Lending Platform. High-yield credit opportunities and scalable capital infrastructure.">
    <title>LendFlow | Institutional P2P Lending</title>
    
    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('images/persegi-nobg.png') }}">
    
    <!-- Google Fonts & Tailwind -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        emerald: { 700: '#15803D', 800: '#166534', 900: '#14532D' },
                        slate: { 900: '#111827', 950: '#0B0F19' }
                    },
                    fontFamily: { sans: ['Plus Jakarta Sans', 'sans-serif'] }
                }
            }
        }
    </script>
</head>
<body class="bg-slate-50 text-slate-900 font-sans antialiased min-h-screen flex flex-col justify-between">

    <!-- Top Navigation Header -->
    <header class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between">
            <!-- Brand Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-2">
                <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-base shadow-xs">L</span>
                <span class="text-xl font-extrabold text-slate-900 tracking-tight">LendFlow</span>
            </a>

            <!-- Navigation Links -->
            <nav class="hidden md:flex items-center gap-8">
                <a href="#lenders" class="text-xs font-bold text-slate-600 hover:text-emerald-700 tracking-wider uppercase transition-colors">LENDERS</a>
                <a href="#borrowers" class="text-xs font-bold text-slate-600 hover:text-emerald-700 tracking-wider uppercase transition-colors">BORROWERS</a>
                <a href="#features" class="text-xs font-bold text-slate-600 hover:text-emerald-700 tracking-wider uppercase transition-colors">FEATURES</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3 py-2">
                        Log In
                    </a>
                    <a href="{{ route('register') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Get Started &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <!-- Main Hero Content -->
    <main class="flex-grow max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16 lg:py-24 grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
        
        <!-- Left Hero Text (Spans 7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Regulated Badge -->
            <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                <span class="h-2 w-2 rounded-full bg-emerald-700 animate-pulse"></span>
                Regulated &amp; Compliant
            </div>

            <!-- Headline -->
            <h1 class="text-4xl sm:text-6xl font-black text-slate-900 tracking-tight leading-[1.08]">
                Institutional Grade<br>
                <span class="text-emerald-700">P2P Lending.</span>
            </h1>

            <!-- Subtitle -->
            <p class="text-sm sm:text-base text-slate-600 font-medium leading-relaxed max-w-2xl">
                Access high-yield credit opportunities or secure scalable capital. LendFlow provides the infrastructure, risk assessment, and liquidity for modern enterprise finance.
            </p>

            <!-- Action Buttons -->
            <div class="flex flex-wrap items-center gap-3 pt-2">
                @guest
                    <a href="{{ route('register') }}" class="py-3 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Start Lending
                    </a>
                    <a href="{{ route('register') }}" class="py-3 px-6 rounded-xl border border-slate-200 bg-white text-slate-800 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs">
                        Apply for Credit
                    </a>
                @else
                    <a href="{{ route('dashboard') }}" class="py-3 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Go to Dashboard &rarr;
                    </a>
                @endauth
            </div>

            <!-- Key Metric Stats Strip -->
            <div class="grid grid-cols-3 gap-6 pt-10 border-t border-slate-200 max-w-xl">
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">$2.4B+</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Capital Deployed</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">8.4%</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Avg. Historical Yield</p>
                </div>
                <div>
                    <p class="text-2xl font-black text-slate-900 tracking-tight">0%</p>
                    <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider mt-0.5">Principal Loss Rate</p>
                </div>
            </div>

        </div>

        <!-- Right Hero Graphic / Mockup Preview (Spans 5 Cols) -->
        <div class="lg:col-span-5">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md relative overflow-hidden space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Active Portfolio</span>
                        <span class="text-2xl font-extrabold text-slate-900">$4.2M</span>
                        <span class="text-[10px] font-bold text-emerald-700 block mt-0.5">↑ 12.4% MoM</span>
                    </div>
                    <div class="h-10 w-10 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-center text-emerald-700 text-lg">
                        
                    </div>
                </div>

                <div class="p-4 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Risk Assessment</span>
                        <span class="text-xs font-bold text-slate-900 block mt-0.5">Default Risk</span>
                    </div>
                    <span class="px-2.5 py-1 rounded text-xs font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">
                        AA-
                    </span>
                </div>
            </div>
        </div>

    </main>

    <!-- Trusted Institutional Partners Footer Strip -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center space-y-4">
            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-widest">TRUSTED BY INSTITUTIONAL PARTNERS GLOBALLY</p>
            <div class="flex flex-wrap items-center justify-center gap-8 sm:gap-12 opacity-60 font-black text-xs text-slate-500 tracking-wider uppercase">
                <span>Apex Capital</span>
                <span>Nova Fund</span>
                <span>Meridian Wealth</span>
                <span>Vanguard Alt</span>
                <span>Stratos</span>
            </div>
        </div>
    </footer>

</body>
</html>
