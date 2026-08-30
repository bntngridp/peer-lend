@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{
    showConfirmModal: false,
    showTermSheetModal: false,
    linkCopied: false,
    investAmount: {{ old('amount', min(500000, max(100000, $loan->amount - $loan->fundings()->sum('amount')))) }},
    get estMonthlyReturn() {
        return Math.round((this.investAmount * ({{ $loan->interest_rate }} / 100)) / 12);
    },
    get totalEstReturn() {
        return Math.round((this.investAmount * ({{ $loan->interest_rate }} / 100)) * ({{ $loan->duration }} / 12));
    },
    get totalPayout() {
        return Math.round(parseFloat(this.investAmount || 0) + this.totalEstReturn);
    },
    copyListingUrl() {
        navigator.clipboard.writeText(window.location.href);
        this.linkCopied = true;
        setTimeout(() => { this.linkCopied = false; }, 3000);
    }
}">
    
    <!-- Top Back Navigation -->
    <div class="flex items-center justify-between">
        <div class="flex items-center gap-3">
            @if(Auth::id() === $loan->borrower_id)
                <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>{{ __('Back to My Loans') }}</span>
                </a>
                <span class="text-slate-300 dark:text-slate-700">|</span>
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400 hover:text-slate-700 dark:hover:text-slate-200 transition-colors">
                    <span>{{ __('Browse Marketplace') }}</span>
                </a>
            @else
                <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 transition-colors">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18"/></svg>
                    <span>{{ __('Back to Marketplace') }}</span>
                </a>
            @endif
        </div>

        @if(Auth::id() === $loan->borrower_id)
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span>{{ __('Your Active Loan Campaign') }}</span>
            </span>
        @endif
    </div>

    <!-- SUCCESS INVESTMENT BANNER (If query param status=success or session) -->
    @if(request('status') === 'success' || session('success'))
    <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4 max-w-2xl mx-auto">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-2xl bg-emerald-100 dark:bg-emerald-950/80 text-emerald-700 dark:text-emerald-400 font-bold flex items-center justify-center text-lg shrink-0 border border-emerald-200 dark:border-emerald-800">
                <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/></svg>
            </div>
            <div>
                <h2 class="text-base font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Investment Confirmed') }}</h2>
                <p class="text-xs text-slate-600 dark:text-slate-400 font-medium">
                    {{ session('success') ?? __('You have successfully allocated capital to this loan.') }}
                </p>
            </div>
        </div>

        <div class="flex justify-end gap-3 pt-2">
            <a href="{{ route('loans.installments', $loan->id) }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs inline-flex items-center gap-1.5">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                <span>{{ __('Open Live Chat & Schedule') }}</span>
            </a>
        </div>
    </div>
    @endif

    <!-- Header Title & Loan Target Banner -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-3">
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ $loan->category->name ?? __('Business') }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-700">•</span>
                    <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                        {{ __('Grade') }} {{ $loan->risk_grade }}
                    </span>
                    <span class="text-slate-300 dark:text-slate-700">•</span>
                    <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                        <span class="h-1.5 w-1.5 rounded-full shrink-0
                            @if($loan->status === 'open_funding') bg-amber-500
                            @elseif($loan->status === 'active') bg-emerald-500
                            @elseif($loan->status === 'completed') bg-slate-400
                            @else bg-rose-500 @endif"></span>
                        <span>{{ __(ucwords(str_replace('_', ' ', $loan->status))) }}</span>
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight mt-2">{{ $loan->purpose }}</h1>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                    {{ __('Application ID:') }} #LN-{{ substr($loan->id, 0, 8) }} &bull; {{ __n($loan->duration) }} {{ __('Months Term') }} &bull; {{ __('Rate:') }} {{ __n($loan->interest_rate) }}% APR
                </p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('TARGET LOAN AMOUNT') }}</span>
                <span class="text-2xl font-black text-slate-900 dark:text-slate-100 tracking-tight">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</span>
            </div>
        </div>

        <!-- Funding Progress Bar -->
        <div class="space-y-1.5 pt-2 border-t border-slate-100 dark:border-slate-800">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="text-emerald-700 dark:text-emerald-400">{{ __n((int)$loan->funded_percentage) }}% {{ __('Funded') }} (Rp {{ __n(number_format($loan->fundings()->sum('amount'), 0, ',', '.')) }})</span>
                <span class="text-slate-400">Rp {{ __n(number_format(max(0, $loan->amount - $loan->fundings()->sum('amount')), 0, ',', '.')) }} {{ __('Remaining') }}</span>
            </div>
            <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-700 h-2 rounded-full transition-all duration-500" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
        
        <!-- Left Column: Key Terms, Security, Use of Funds & Borrower Overview (Spans 8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Key Terms & Security Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Key Terms Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2 flex items-center justify-between">
                        <span>{{ __('Key Terms') }}</span>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-extrabold">{{ __('Grade') }} {{ $loan->risk_grade }}</span>
                    </h3>
                    <div class="space-y-2 text-xs font-medium">
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Interest Rate (Fixed)') }}</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">{{ __n($loan->interest_rate) }}% APR</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Term Length') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ __n($loan->duration) }} {{ __('Months') }}</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Payment Frequency') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ __('Monthly Interest & Principal') }}</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('LTV Ratio') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ __n($loan->initial_ltv ?? 65) }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Security Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                        {{ __('Security & Guarantee') }}
                    </h3>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs">{{ __('Senior Secured Legal Claim') }}</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium block mt-0.5">
                                {{ __('Binding loan contract registered with escrow protection.') }}
                            </span>
                        </div>
                        @if($loan->isCryptoLoan())
                        <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800">
                            <span class="font-bold text-emerald-950 dark:text-emerald-300 block text-xs">{{ __('Crypto Collateral Locked') }}</span>
                            <span class="text-[10px] text-emerald-800 dark:text-emerald-400 font-medium block mt-0.5">
                                {{ __n(number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places ?? 4)) }} {{ $loan->collateralCurrency->code ?? 'ETH' }} {{ __('held in secure platform escrow.') }}
                            </span>
                        </div>
                        @else
                        <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-950/50 border border-blue-200 dark:border-blue-800">
                            <span class="font-bold text-blue-950 dark:text-blue-300 block text-xs">{{ __('Unsecured Credit Scored Facility') }}</span>
                            <span class="text-[10px] text-blue-800 dark:text-blue-400 font-medium block mt-0.5">
                                {{ __('Underwritten via comprehensive algorithmic risk scoring.') }}
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Use of Funds -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    {{ __('Use of Funds') }}
                </h3>
                <p class="text-xs text-slate-600 dark:text-slate-300 font-medium leading-relaxed">
                    {{ $loan->description ?: __('Capital will be deployed immediately to expand business operations, fund working capital, and optimize supply chain logistics.') }}
                </p>
            </div>

            <!-- Borrower Overview -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-2">
                    {{ __('Borrower Overview') }}
                </h3>
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-slate-900 dark:bg-slate-800 text-white font-black flex items-center justify-center text-sm shadow-xs border border-slate-700">
                        {{ strtoupper(substr($loan->borrower->profile->full_name ?? 'Client', 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $loan->borrower->profile->full_name ?? __('Institutional Borrower') }}</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                            {{ __('Verified') }} {{ $loan->borrower->profile->occupation ?? __('Enterprise Client') }} &bull; {{ __('Account status active and KYC verified.') }}
                        </p>
                    </div>
                </div>

                <!-- Borrower Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Credit Rating') }}</span>
                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 block mt-0.5">{{ __('Grade') }} {{ $loan->risk_grade }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Loan Tenor') }}</span>
                        <span class="text-xs font-black text-slate-900 dark:text-slate-100 block mt-0.5">{{ __n($loan->duration) }} {{ __('Months') }}</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Facility Amount') }}</span>
                        <span class="text-xs font-black text-slate-900 dark:text-slate-100 block mt-0.5">Rp {{ __n(number_format($loan->amount / 1000000, 1)) }}M</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Defaults') }}</span>
                        <span class="text-xs font-black text-emerald-700 dark:text-emerald-400 block mt-0.5">0 {{ __('Defaults') }}</span>
                    </div>
                </div>
            </div>

            <!-- Participating Lenders / Escrow Funding Log -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                        {{ __('Participating Investors') }} ({{ $loan->fundings->count() }})
                    </h3>
                    <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">
                        Rp {{ __n(number_format($loan->fundings->sum('amount'), 0, ',', '.')) }} {{ __('Allocated') }}
                    </span>
                </div>

                @if($loan->fundings->isNotEmpty())
                    <div class="divide-y divide-slate-100 dark:divide-slate-800 text-xs">
                        @foreach($loan->fundings as $funding)
                            <div class="py-2.5 flex items-center justify-between">
                                <div class="flex items-center gap-2.5">
                                    <div class="h-7 w-7 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 font-bold flex items-center justify-center text-[10px] border border-emerald-200 dark:border-emerald-800">
                                        {{ strtoupper(substr($funding->lender->profile->full_name ?? $funding->lender->name ?? 'Investor', 0, 2)) }}
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-slate-100 block">
                                            {{ $funding->lender_id === Auth::id() ? __('You') . ' (' . ($funding->lender->profile->full_name ?? $funding->lender->name) . ')' : ($funding->lender->profile->full_name ?? 'Investor #' . substr($funding->id, 0, 6)) }}
                                        </span>
                                        <span class="text-[10px] text-slate-400">
                                            {{ $funding->created_at->format('d M Y, H:i') }}
                                        </span>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <span class="font-bold text-emerald-700 dark:text-emerald-400 block">
                                        Rp {{ __n(number_format($funding->amount, 0, ',', '.')) }}
                                    </span>
                                    <span class="text-[10px] text-slate-400">
                                        {{ $loan->amount > 0 ? __n(round(($funding->amount / $loan->amount) * 100, 1)) : 0 }}% {{ __('Share') }}
                                    </span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="py-6 text-center text-slate-400 dark:text-slate-500 text-xs space-y-1">
                        <svg class="w-8 h-8 mx-auto text-slate-300 dark:text-slate-700" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        <p class="font-bold text-slate-700 dark:text-slate-300">{{ __('No Investments Yet') }}</p>
                        <p class="text-[11px]">{{ __('Be among the first investors to fund this institutional loan.') }}</p>
                    </div>
                @endif
            </div>

        </div>

        <!-- Right Side: Action Card (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4 sticky top-20">
            
            @if(Auth::id() === $loan->borrower_id)
                <!-- ═══════════════════════════════════════════════════════════════ -->
                <!-- BORROWER OWNER DASHBOARD CARD                                 -->
                <!-- ═══════════════════════════════════════════════════════════════ -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('Borrower Campaign Hub') }}</h3>
                        <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">
                            {{ __('Your Loan') }}
                        </span>
                    </div>

                    <!-- Owner Notice Box -->
                    <div class="p-3.5 rounded-xl bg-emerald-50/70 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-300 space-y-1">
                        <div class="flex items-center gap-1.5 font-bold">
                            <svg class="w-4 h-4 text-emerald-700 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span>{{ __('Listing is Live in Marketplace') }}</span>
                        </div>
                        <p class="text-[11px] text-emerald-800 dark:text-emerald-400/90 leading-relaxed font-medium">
                            {{ __('Your loan application is published to accredited investors. Funds will automatically disburse to your wallet once 100% target is reached.') }}
                        </p>
                    </div>

                    <!-- Funding Metrics Card -->
                    <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-2.5 text-xs font-medium">
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Target Amount') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Funded Amount') }}</span>
                            <span class="font-bold text-emerald-700 dark:text-emerald-400">Rp {{ __n(number_format($loan->fundings()->sum('amount'), 0, ',', '.')) }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Active Investors') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $loan->fundings()->count() }} {{ __('Backers') }}</span>
                        </div>
                        <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-2">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Remaining Target') }}</span>
                            <span class="font-black text-slate-900 dark:text-slate-100">Rp {{ __n(number_format(max(0, $loan->amount - $loan->fundings()->sum('amount')), 0, ',', '.')) }}</span>
                        </div>
                    </div>

                    <!-- Owner Action Buttons -->
                    <div class="space-y-2.5 pt-1">
                        <a href="{{ route('loans.installments', $loan->id) }}" 
                           class="w-full py-2.5 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs flex items-center justify-center gap-2">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 012.25-2.25h13.5A2.25 2.25 0 0121 7.5v11.25m-18 0A2.25 2.25 0 005.25 21h13.5A2.25 2.25 0 0021 18.75m-18 0v-7.5A2.25 2.25 0 015.25 9h13.5A2.25 2.25 0 0121 11.25v7.5"/></svg>
                            <span>{{ __('View Schedule & Installments') }} &rarr;</span>
                        </a>

                        <button type="button" @click="copyListingUrl()" 
                                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 011.242 7.244l-4.5 4.5a4.5 4.5 0 01-6.364-6.364l1.757-1.757m13.35-.622l1.757-1.757a4.5 4.5 0 00-6.364-6.364l-4.5 4.5a4.5 4.5 0 001.242 7.244"/></svg>
                            <span x-text="linkCopied ? '{{ __('Link Copied to Clipboard!') }}' : '{{ __('Share Marketplace Listing') }}'"></span>
                        </button>

                        <button type="button" @click="showTermSheetModal = true" 
                                class="w-full py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                            <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                            <span>{{ __('View Official Term Sheet') }}</span>
                        </button>
                    </div>
                </div>

            @else
                <!-- ═══════════════════════════════════════════════════════════════ -->
                <!-- INVESTOR / LENDER ACTION CARD                                 -->
                <!-- ═══════════════════════════════════════════════════════════════ -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-5">
                    <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                        <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('Invest in this Loan') }}</h3>
                        <span class="text-[11px] font-bold text-slate-400">{{ __('Min. Rp 100.000') }}</span>
                    </div>

                    @if($loan->fundings()->where('lender_id', Auth::id())->exists())
                        <div class="p-3.5 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs text-emerald-900 dark:text-emerald-300 space-y-1">
                            <div class="flex items-center gap-1.5 font-bold">
                                <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>{{ __('You have invested in this loan') }}</span>
                            </div>
                            <p class="text-[11px] font-medium text-emerald-800 dark:text-emerald-400">
                                {{ __('Total allocated:') }} <strong>Rp {{ __n(number_format($loan->fundings()->where('lender_id', Auth::id())->sum('amount'), 0, ',', '.')) }}</strong>
                            </p>
                        </div>
                    @endif

                    @if($loan->status === 'open_funding' && ($loan->amount - $loan->fundings()->sum('amount')) > 0)
                        <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">
                                    {{ __('AMOUNT TO INVEST (IDR)') }}
                                </label>
                                <div class="relative">
                                    <span class="absolute left-3.5 top-2.5 text-xs font-bold text-slate-400">Rp</span>
                                    <input type="number" name="amount" x-model="investAmount" required min="100000" max="{{ max(100000, $loan->amount - $loan->fundings()->sum('amount')) }}" step="any"
                                           class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 pl-10 pr-3.5 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                                </div>
                                
                                <!-- Presets -->
                                <div class="flex gap-1.5 mt-2">
                                    <button type="button" @click="investAmount = 500000" class="flex-1 py-1 px-2 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Rp 500K</button>
                                    <button type="button" @click="investAmount = 1000000" class="flex-1 py-1 px-2 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Rp 1M</button>
                                    <button type="button" @click="investAmount = 5000000" class="flex-1 py-1 px-2 rounded-lg border border-slate-200 dark:border-slate-700 text-[10px] font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800">Rp 5M</button>
                                    <button type="button" @click="investAmount = {{ max(100000, $loan->amount - $loan->fundings()->sum('amount')) }}" class="flex-1 py-1 px-2 rounded-lg border border-emerald-300 dark:border-emerald-700 text-[10px] font-bold text-emerald-800 dark:text-emerald-300 hover:bg-emerald-50 dark:hover:bg-emerald-950/60">{{ __('Max') }}</button>
                                </div>
                            </div>

                            <!-- Financial Projections Card -->
                            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-2 text-xs font-medium">
                                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                    <span>{{ __('Est. Monthly Return') }}</span>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-400">+Rp <span x-text="estMonthlyReturn.toLocaleString('id-ID')"></span></span>
                                </div>
                                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                                    <span>{{ __('Total Est. Return') }} ({{ $loan->duration }} {{ __('mo') }})</span>
                                    <span class="font-bold text-emerald-700 dark:text-emerald-400">+Rp <span x-text="totalEstReturn.toLocaleString('id-ID')"></span></span>
                                </div>
                                <div class="flex justify-between border-t border-slate-200 dark:border-slate-700 pt-2 text-xs">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('TOTAL EST. PAYOUT') }}</span>
                                    <span class="font-black text-slate-900 dark:text-slate-100">Rp <span x-text="totalPayout.toLocaleString('id-ID')"></span></span>
                                </div>
                            </div>

                            <button type="button" @click="showConfirmModal = true" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                                {{ __('INVEST NOW') }} &rarr;
                            </button>
                        </form>
                    @else
                        <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 text-center space-y-1">
                            <span class="text-xs font-bold text-slate-700 dark:text-slate-300 block">{{ __('Funding Completed') }}</span>
                            <p class="text-[11px] text-slate-400">{{ __('This loan has reached 100% target and is now active or completed.') }}</p>
                        </div>
                    @endif

                    @if($loan->fundings()->where('lender_id', Auth::id())->exists())
                        <a href="{{ route('loans.installments', $loan->id) }}" class="flex items-center justify-center gap-2 w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                            <svg class="w-4 h-4 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                            <span>{{ __('Open Live Chat & Schedule') }}</span>
                        </a>
                    @endif

                    <button type="button" @click="showTermSheetModal = true" 
                            class="w-full py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors flex items-center justify-center gap-2 cursor-pointer">
                        <svg class="w-4 h-4 text-slate-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <span>{{ __('VIEW TERM SHEET') }}</span>
                    </button>
                </div>
            @endif

        </div>

    </div>

    <!-- ─── CONFIRM INVESTMENT MODAL ────────────────────────────────────── -->
    <div x-show="showConfirmModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
        <div @click.away="showConfirmModal = false" class="w-full max-w-md rounded-3xl bg-white dark:bg-slate-900 p-6 shadow-2xl space-y-5 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100">{{ __('Confirm Investment') }}</h3>
                <button type="button" @click="showConfirmModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-xl font-bold cursor-pointer">&times;</button>
            </div>

            <!-- Loan Summary Banner -->
            <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/60 border border-slate-100 dark:border-slate-800 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ID: #LN-{{ substr($loan->id, 0, 8) }}</span>
                    <span class="font-extrabold text-emerald-700 dark:text-emerald-400 block mt-0.5">{{ __n($loan->interest_rate) }}% APR</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Tenor') }}</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 block mt-0.5">{{ __n($loan->duration) }} {{ __('Months') }}</span>
                </div>
            </div>

            <!-- Investment Amount Field -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Investment Amount') }}</label>
                <div class="p-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800/50 text-sm font-black text-slate-900 dark:text-slate-100">
                    Rp <span x-text="parseInt(investAmount || 0).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- Financial Projections Box -->
            <div class="p-3.5 rounded-xl bg-emerald-50/50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 space-y-2 text-xs font-medium">
                <div class="flex justify-between">
                    <span class="text-slate-600 dark:text-slate-400">{{ __('Estimated Monthly Interest') }}</span>
                    <span class="font-bold text-emerald-800 dark:text-emerald-300">+Rp <span x-text="estMonthlyReturn.toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between border-t border-emerald-200/60 dark:border-emerald-800/80 pt-1.5">
                    <span class="font-bold text-emerald-950 dark:text-emerald-200">{{ __('Total Return at Maturity') }}</span>
                    <span class="font-black text-emerald-800 dark:text-emerald-300">Rp <span x-text="totalPayout.toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <!-- Agreement Checkbox -->
            <div class="flex items-start gap-2.5 text-xs text-slate-600 dark:text-slate-400 font-medium">
                <input type="checkbox" id="agree_terms" checked required class="mt-0.5 accent-emerald-700 rounded cursor-pointer">
                <label for="agree_terms" class="text-[11px] leading-tight">
                    {{ __('I agree to the Loan Participation Agreement and acknowledge the risk disclosure associated with P2P investments.') }}
                </label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-2">
                <button type="button" @click="showConfirmModal = false" class="w-1/2 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                    {{ __('Cancel') }}
                </button>
                <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="w-1/2">
                    @csrf
                    <input type="hidden" name="amount" :value="investAmount">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                        {{ __('Confirm Investment') }} &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ─── OFFICIAL TERM SHEET MODAL ────────────────────────────────────── -->
    <div x-show="showTermSheetModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/70 backdrop-blur-xs p-4">
        <div @click.away="showTermSheetModal = false" class="w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-3xl bg-white dark:bg-slate-900 p-8 shadow-2xl space-y-6 border border-slate-200 dark:border-slate-800 animate-in fade-in zoom-in duration-150">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-2xl bg-emerald-700 text-white flex items-center justify-center font-black text-base shadow-xs">
                        LF
                    </div>
                    <div>
                        <h3 class="text-base font-black text-slate-900 dark:text-slate-100 tracking-tight">{{ __('OFFICIAL LOAN TERM SHEET') }}</h3>
                        <p class="text-[11px] text-slate-400 font-medium">Ref: LF-TS-{{ strtoupper(substr($loan->id, 0, 8)) }} &bull; {{ now()->format('d M Y') }}</p>
                    </div>
                </div>
                <button type="button" @click="showTermSheetModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 text-2xl font-bold leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Term Sheet Breakdown Table -->
            <div class="space-y-4 text-xs">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('BORROWER ENTITY') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100 text-sm block">{{ $loan->borrower->profile->full_name ?? __('Registered Client') }}</span>
                        <span class="text-[11px] text-slate-500">{{ $loan->borrower->email }}</span>
                    </div>
                    <div class="p-3.5 rounded-2xl bg-slate-50 dark:bg-slate-800/50 border border-slate-100 dark:border-slate-800 space-y-1">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('RISK & CREDIT RATING') }}</span>
                        <span class="font-bold text-emerald-700 dark:text-emerald-400 text-sm block">{{ __('Risk Grade') }} {{ $loan->risk_grade }}</span>
                        <span class="text-[11px] text-slate-500">{{ __('Category:') }} {{ $loan->category->name ?? __('Standard') }}</span>
                    </div>
                </div>

                <div class="border border-slate-200 dark:border-slate-800 rounded-2xl overflow-hidden divide-y divide-slate-100 dark:divide-slate-800">
                    <div class="grid grid-cols-2 p-3 bg-slate-50 dark:bg-slate-800/50 font-bold text-slate-500 dark:text-slate-400 text-[11px]">
                        <span>{{ __('TERMS CLAUSE') }}</span>
                        <span>{{ __('SPECIFICATION') }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ __('Principal Loan Facility') }}</span>
                        <span class="font-extrabold text-slate-900 dark:text-slate-100">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ __('Interest Rate (APR Fixed)') }}</span>
                        <span class="font-extrabold text-emerald-700 dark:text-emerald-400">{{ __n($loan->interest_rate) }}% per annum</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ __('Repayment Tenor') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ __n($loan->duration) }} {{ __('Monthly Installments') }}</span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ __('Security & Collateral') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">
                            @if($loan->isCryptoLoan())
                                {{ __n(number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places ?? 4)) }} {{ $loan->collateralCurrency->code ?? 'ETH' }} {{ __('Collateral Locked') }}
                            @else
                                {{ __('Institutional Senior Claim & Asset Lien') }}
                            @endif
                        </span>
                    </div>
                    <div class="grid grid-cols-2 p-3">
                        <span class="text-slate-600 dark:text-slate-400 font-medium">{{ __('Loan Purpose') }}</span>
                        <span class="font-medium text-slate-900 dark:text-slate-100">{{ $loan->purpose }}</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-2xl bg-amber-50 dark:bg-amber-950/40 border border-amber-200 dark:border-amber-900/50 text-amber-900 dark:text-amber-300 text-[11px] leading-relaxed">
                    <strong>{{ __('Confidentiality & Governance:') }}</strong> {{ __('This Term Sheet summarizes the principal terms and conditions of the loan participation agreement. Validated and timestamped by the LendFlow P2P platform escrow engine.') }}
                </div>
            </div>

            <!-- Modal Footer -->
            <div class="flex items-center justify-between pt-2 border-t border-slate-100 dark:border-slate-800">
                <button type="button" @click="showTermSheetModal = false" class="py-2.5 px-5 rounded-xl border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800 cursor-pointer">
                    {{ __('Close') }}
                </button>
                <button type="button" onclick="window.print()" class="py-2.5 px-5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs inline-flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-3.279 2.05-6.079 5.28-6.079 3.23 0 5.52 2.8 5.28 6.079m-10.56 0h10.56m-10.56 0a3.75 3.75 0 00-3.72 3.328l-.5 4.5a.75.75 0 00.745.833h17.07a.75.75 0 00.745-.833l-.5-4.5a3.75 3.75 0 00-3.72-3.328m-10.56 0V6a2.25 2.25 0 012.25-2.25h6A2.25 2.25 0 0118 6v7.829"/></svg>
                    <span>{{ __('Print Term Sheet') }}</span>
                </button>
            </div>

        </div>
    </div>

</div>
@endsection
