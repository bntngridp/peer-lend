<!DOCTYPE html>
<html lang="id" class="scroll-smooth">
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
            <nav class="hidden md:flex items-center gap-8 text-xs font-bold text-slate-600">
                <a href="#borrowers" class="hover:text-emerald-700 tracking-wider uppercase transition-colors">Borrowers</a>
                <a href="#lenders" class="hover:text-emerald-700 tracking-wider uppercase transition-colors">Lenders</a>
                <a href="#features" class="hover:text-emerald-700 tracking-wider uppercase transition-colors">Features</a>
                <a href="{{ route('calculator.index') }}" class="hover:text-emerald-700 tracking-wider uppercase transition-colors">Calculator</a>
            </nav>

            <!-- Actions -->
            <div class="flex items-center gap-3">
                @auth
                    <a href="{{ route('dashboard') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Dashboard &rarr;
                    </a>
                @else
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-700 hover:text-emerald-700 px-3 py-2">
                        Sign In
                    </a>
                    <a href="{{ route('register') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                        Create Account &rarr;
                    </a>
                @endauth
            </div>
        </div>
    </header>

    <main class="flex-grow space-y-24 py-12">
        
        <!-- ─── Section 1: Borrowers (#borrowers) ───────────────────────────── -->
        <section id="borrowers" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pt-8">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Content (Spans 6 Cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-bold">
                        <span class="h-2 w-2 rounded-full bg-emerald-700 animate-pulse"></span>
                        Institutional Credit Access
                    </div>

                    <h1 class="text-3xl sm:text-5xl font-black text-slate-900 tracking-tight leading-tight">
                        Flexible Loans Built Around Your Financial Goals
                    </h1>

                    <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                        Access institutional-grade capital with full transparency. Our streamlined process gets you funded faster with custom repayment structures tailored to your liquidity.
                    </p>

                    <!-- Feature Grid 2x2 -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-xs pt-2">
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-1">
                            <h4 class="font-bold text-slate-900">Quick Application</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Complete your request in minutes.</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-1">
                            <h4 class="font-bold text-slate-900">KYC Verification</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Secure, automated identity checks.</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-1">
                            <h4 class="font-bold text-slate-900">Fast Approval</h4>
                            <p class="text-[11px] text-slate-500 font-medium">Receive decisions within hours.</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-white border border-slate-200 shadow-xs space-y-1">
                            <h4 class="font-bold text-slate-900">Transparent Interest</h4>
                            <p class="text-[11px] text-slate-500 font-medium">No hidden fees, ever.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('register') }}" class="py-3 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                            Apply for a Loan
                        </a>
                        <a href="{{ route('calculator.index') }}" class="py-3 px-6 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs">
                            Calculate Loan
                        </a>
                    </div>
                </div>

                <!-- Right Dashboard Graphic (Spans 6 Cols) -->
                <div class="lg:col-span-6">
                    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-md space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                            <span class="text-xs font-extrabold text-slate-900">Borrower Dashboard Preview</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800">Verified Borrower</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Outstanding Loan</span>
                                <span class="text-base font-extrabold text-slate-900 block mt-1">Rp 12.450.000</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Monthly Installment</span>
                                <span class="text-base font-extrabold text-slate-900 block mt-1">Rp 850.200</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── Section 2: Lenders (#lenders) ───────────────────────────────── -->
        <section id="lenders" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 bg-white py-16 rounded-3xl border border-slate-200 shadow-xs">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-center">
                
                <!-- Left Content (Spans 6 Cols) -->
                <div class="lg:col-span-6 space-y-6">
                    <h2 class="text-3xl sm:text-4xl font-black text-slate-900 tracking-tight">
                        Grow Your Wealth Through Smarter Lending
                    </h2>

                    <p class="text-xs sm:text-sm text-slate-600 font-medium leading-relaxed">
                        Build a diversified portfolio of high-quality loans with enterprise-grade analytics, credit scoring models, and automated deployment tools.
                    </p>

                    <div class="space-y-3 text-xs font-semibold text-slate-700">
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <h4 class="font-bold text-slate-900">Verified Borrowers</h4>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">All applicants pass strict AML/KYC checks before listing.</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <h4 class="font-bold text-slate-900">Risk Grading &amp; Analytics</h4>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Proprietary scoring models and real-time portfolio performance tracking.</p>
                        </div>
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200">
                            <h4 class="font-bold text-slate-900">Auto-Invest Rule Engine</h4>
                            <p class="text-[11px] text-slate-500 font-medium mt-0.5">Set your parameters and let our engine deploy capital automatically.</p>
                        </div>
                    </div>

                    <div class="flex items-center gap-3 pt-2">
                        <a href="{{ route('register') }}" class="py-3 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                            Start Investing
                        </a>
                        <a href="{{ route('marketplace.index') }}" class="py-3 px-6 rounded-xl border border-slate-200 bg-white text-slate-700 text-xs font-bold hover:bg-slate-50 transition-colors shadow-xs">
                            Explore Marketplace
                        </a>
                    </div>
                </div>

                <!-- Right Lender Graphic (Spans 6 Cols) -->
                <div class="lg:col-span-6">
                    <div class="rounded-2xl border border-slate-200 bg-slate-900 text-white p-6 shadow-md space-y-4">
                        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
                            <span class="text-xs font-extrabold text-white">Lender Portfolio Preview</span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-700 text-white">+12.4% APY</span>
                        </div>
                        <div class="grid grid-cols-2 gap-3 text-xs">
                            <div class="p-3 rounded-xl bg-slate-800 border border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Portfolio Value</span>
                                <span class="text-base font-extrabold text-white block mt-1">Rp 48.250.000</span>
                            </div>
                            <div class="p-3 rounded-xl bg-slate-800 border border-slate-700">
                                <span class="text-[10px] text-slate-400 font-bold uppercase block">Active Deals</span>
                                <span class="text-base font-extrabold text-white block mt-1">34 Loans</span>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </section>

        <!-- ─── Section 3: Features (#features) ──────────────────── -->
        <section id="features" class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center space-y-3 max-w-2xl mx-auto mb-10">
                <h2 class="text-3xl font-black text-slate-900 tracking-tight">Institutional Yield &amp; Risk Tools</h2>
                <p class="text-xs text-slate-500 font-medium">Real-time simulation, collateral tracking, and automated settlement engines.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-2">
                    <h3 class="text-sm font-bold text-slate-900">Yield Calculator</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Estimate annual yields and monthly interest payouts across 4 risk grades.</p>
                    <a href="{{ route('calculator.index') }}" class="text-xs font-bold text-emerald-700 inline-block pt-2 hover:underline">Calculate Yield &rarr;</a>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-2">
                    <h3 class="text-sm font-bold text-slate-900">Crypto Collateral (LTV)</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Multi-asset collateral monitoring with automated margin call alerts.</p>
                    <a href="{{ route('collateral.index') }}" class="text-xs font-bold text-emerald-700 inline-block pt-2 hover:underline">Monitor LTV &rarr;</a>
                </div>

                <div class="p-6 rounded-2xl bg-white border border-slate-200 shadow-xs space-y-2">
                    <h3 class="text-sm font-bold text-slate-900">Automated Settlement</h3>
                    <p class="text-xs text-slate-500 font-medium leading-relaxed">Integrated Midtrans &amp; virtual wallet for instant 24/7 disbursements.</p>
                    <a href="{{ route('wallet.index') }}" class="text-xs font-bold text-emerald-700 inline-block pt-2 hover:underline">View Wallet &rarr;</a>
                </div>
            </div>
        </section>

        <!-- ─── Section 4: CTA Banner ────────────────────────────────────────── -->
        <section class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="rounded-3xl bg-emerald-700 text-white p-8 sm:p-14 text-center space-y-6 shadow-md">
                <h2 class="text-2xl sm:text-4xl font-black text-white tracking-tight">
                    Ready to Take Control of Your Financial Future?
                </h2>
                <p class="text-xs sm:text-sm text-emerald-100 font-medium max-w-xl mx-auto leading-relaxed">
                    Join thousands of verified users on the most secure institutional-grade lending platform.
                </p>
                <div class="flex flex-wrap items-center justify-center gap-3 pt-2">
                    <a href="{{ route('register') }}" class="py-3 px-8 rounded-xl bg-white text-slate-900 font-extrabold text-xs hover:bg-slate-100 transition-colors shadow-xs">
                        Create Free Account
                    </a>
                    <a href="mailto:sales@lendflow.com" class="py-3 px-8 rounded-xl border border-emerald-500 text-white font-extrabold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        Contact Sales
                    </a>
                </div>
            </div>
        </section>

    </main>

    <!-- Footer -->
    <footer class="bg-white border-t border-slate-200 py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs font-medium text-slate-500">
            <p>&copy; 2026 LendFlow Financial Systems. All rights reserved. Institutional Grade P2P Lending.</p>
            <div class="flex items-center gap-6">
                <a href="#" class="hover:text-slate-800">Privacy Policy</a>
                <a href="#" class="hover:text-slate-800">Terms of Service</a>
                <a href="#" class="hover:text-slate-800">Security</a>
                <a href="#" class="hover:text-slate-800">Contact Support</a>
            </div>
        </div>
    </footer>

</body>
</html>
