@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ collateralTab: 'overview' }">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Crypto Collateral') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Institutional monitoring of pledged digital assets and liquidation thresholds.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                {{ __('Export Report') }}
            </button>
        </div>
    </div>

    <!-- 3 Summary Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: TOTAL PLEDGED VALUE -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('TOTAL PLEDGED VALUE') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">+{{ __n('2.4%') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    Rp {{ __n(number_format($totalPledgedValue ?? 1700000000, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Across') }} {{ __n($cryptoLoans->count()) }} {{ __('active collateral positions') }}</p>
            </div>
        </div>

        <!-- Card 2: WEIGHTED AVG LTV -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('WEIGHTED AVG LTV') }}</span>
                <span class="text-xs font-bold text-emerald-700">{{ __('Target') }} &lt;{{ __n('65%') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    {{ __n(number_format($weightedAvgLtv ?? 78.0, 1)) }}%
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Margin Call Threshold:') }} {{ __n('75%') }}</p>
            </div>
        </div>

        <!-- Card 3: PORTFOLIO RISK STATUS -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('PORTFOLIO RISK STATUS') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">{{ __('Monitor') }}</span>
            </div>
            <div class="mt-3 space-y-1 text-xs font-semibold">
                <div class="flex items-center gap-1.5 text-rose-600">
                    <span class="h-2 w-2 rounded-full bg-rose-600"></span> {{ __n($atRiskCount) }} {{ __('Loans in Liquidation Zone') }}
                </div>
                <div class="flex items-center gap-1.5 text-amber-600">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> {{ __n($warningCount) }} {{ __('Loans near Margin Call') }}
                </div>
                <div class="flex items-center gap-1.5 text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span> {{ __n($healthyCount) }} {{ __('Healthy Positions') }}
                </div>
            </div>
        </div>
    </div>

    <!-- Collateral Distribution & LTV Monitoring Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Collateral Distribution (Spans 6 Cols) -->
        <div class="lg:col-span-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Collateral Distribution') }}</h3>
                <span class="text-xs font-bold text-emerald-700">{{ __n('3') }} {{ __('Currencies') }}</span>
            </div>

            <div class="space-y-3">
                @forelse($collateralDistribution as $item)
                <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        @if($item['code'] === 'BTC')
                            <img src="{{ asset('images/crypto/btc.png') }}" alt="BTC" class="h-9 w-9 rounded-xl object-cover shadow-xs border border-amber-500/20">
                        @elseif($item['code'] === 'ETH')
                            <img src="{{ asset('images/crypto/eth.png') }}" alt="ETH" class="h-9 w-9 rounded-xl object-cover shadow-xs border border-indigo-500/20">
                        @else
                            <div class="h-9 w-9 rounded-xl bg-emerald-600 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                ₮
                            </div>
                        @endif
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ $item['name'] }} ({{ $item['code'] }})</span>
                            <span class="text-[10px] text-slate-400 font-medium block">{{ __n(number_format($item['total_locked'], $item['code'] === 'BTC' ? 4 : 2)) }} {{ $item['code'] }} {{ __('Locked') }}</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 dark:text-slate-100 text-xs block">Rp {{ __n(number_format($item['total_amount'], 0, ',', '.')) }}</span>
                        <span class="text-[10px] text-emerald-700 dark:text-emerald-400 font-bold block">{{ __n($item['percentage']) }}% {{ __('of Pool') }}</span>
                    </div>
                </div>
                @empty
                <p class="text-xs text-slate-400 text-center py-4">{{ __('No active collateral positions.') }}</p>
                @endforelse
            </div>
        </div>

        <!-- Right: LTV Monitoring & Stress Test (Spans 6 Cols) -->
        <div class="lg:col-span-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('LTV Monitoring & Stress Test') }}</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">{{ __('Live Thresholds') }}</span>
            </div>

            <!-- Risk Tiers -->
            <div class="space-y-3 text-xs font-medium">
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-rose-900 block">{{ __('High Risk') }} (&gt;{{ __n('80%') }} LTV)</span>
                        <span class="text-[10px] text-rose-700">{{ __n($atRiskCount) }} {{ __('Loans Affected') }}</span>
                    </div>
                    <span class="font-black text-rose-700">Rp {{ __n(number_format($highRiskAmount, 0, ',', '.')) }}</span>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-amber-900 block">{{ __('Medium Risk') }} ({{ __n('75-80%') }} LTV)</span>
                        <span class="text-[10px] text-amber-700">{{ __n($warningCount) }} {{ __('Loans Affected') }}</span>
                    </div>
                    <span class="font-black text-amber-700">Rp {{ __n(number_format($mediumRiskAmount, 0, ',', '.')) }}</span>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-emerald-900 block">{{ __('Low Risk') }} (&lt;{{ __n('75%') }} LTV)</span>
                        <span class="text-[10px] text-emerald-700">{{ __n($healthyCount) }} {{ __('Loans Healthy') }}</span>
                    </div>
                    <span class="font-black text-emerald-700">Rp {{ __n(number_format($lowRiskAmount, 0, ',', '.')) }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Liquidation Warnings Table Card -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4 bg-slate-50/50 dark:bg-slate-800/40">
            <div class="flex items-center gap-2">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Liquidation Warnings & Margin Calls') }}</h3>
            </div>
            
            <!-- Real-Time Market Prices Indicator with Interactive Tooltip -->
            <div class="relative inline-flex items-center" x-data="{ showTooltip: false }">
                <button type="button" 
                        @mouseenter="showTooltip = true" 
                        @mouseleave="showTooltip = false"
                        @focus="showTooltip = true"
                        @blur="showTooltip = false"
                        class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-800 dark:text-emerald-300 transition-all cursor-pointer select-none">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-emerald-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-emerald-600 dark:bg-emerald-400"></span>
                    </span>
                    <span>{{ __('Real-Time Market Prices') }}</span>
                    <svg class="w-3.5 h-3.5 text-emerald-600 dark:text-emerald-400 opacity-80" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M11.25 11.25l.041-.02a.75.75 0 011.063.852l-.708 2.836a.75.75 0 001.063.853l.041-.021M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-9-3.75h.008v.008H12V8.25z" />
                    </svg>
                </button>

                <!-- Tooltip Popup -->
                <div x-show="showTooltip" 
                     x-cloak 
                     x-transition:enter="transition ease-out duration-150"
                     x-transition:enter-start="opacity-0 translate-y-1 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                     x-transition:leave="transition ease-in duration-100"
                     x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                     x-transition:leave-end="opacity-0 translate-y-1 scale-95"
                     class="absolute right-0 top-full mt-2 w-72 p-3 rounded-2xl bg-slate-900 text-white text-[11px] font-medium leading-relaxed shadow-xl border border-slate-700 z-30 pointer-events-none">
                    <div class="flex items-start gap-2">
                        <svg class="w-4 h-4 text-emerald-400 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <p>
                            {{ __('Collateral values and LTV ratios are calculated automatically using live market price feeds to ensure safety.') }}
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('LOAN ID') }}</th>
                        <th class="py-3.5 px-6">{{ __('BORROWER') }}</th>
                        <th class="py-3.5 px-6">{{ __('COLLATERAL ASSET') }}</th>
                        <th class="py-3.5 px-6">{{ __('CURRENT LTV') }}</th>
                        <th class="py-3.5 px-6">{{ __('LIQ. PRICE') }}</th>
                        <th class="py-3.5 px-6">{{ __('STATUS') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('ACTION') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($cryptoLoans as $loan)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">#LN-{{ substr($loan->id, 0, 6) }}</td>
                        <td class="py-4 px-6 font-semibold text-slate-800">
                            {{ $loan->borrower?->profile?->full_name ?? __('Borrower') }}
                        </td>
                        <td class="py-4 px-6 font-bold text-indigo-700 dark:text-indigo-400">
                            <div class="inline-flex items-center gap-1.5">
                                @if($loan->collateralCurrency?->code === 'BTC')
                                    <img src="{{ asset('images/crypto/btc.png') }}" alt="BTC" class="w-4 h-4 rounded-md object-cover shrink-0 shadow-2xs border border-amber-500/20">
                                @elseif($loan->collateralCurrency?->code === 'ETH')
                                    <img src="{{ asset('images/crypto/eth.png') }}" alt="ETH" class="w-4 h-4 rounded-md object-cover shrink-0 shadow-2xs border border-indigo-500/20">
                                @endif
                                <span>{{ $loan->collateralCurrency?->code ?? 'CRYPTO' }} ({{ __n(number_format($loan->collateral_amount, 4)) }})</span>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold {{ $loan->current_ltv >= 80 ? 'text-rose-600' : 'text-amber-600' }}">
                            {{ __n(number_format($loan->current_ltv, 1)) }}%
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-900">
                            Rp {{ __n(number_format($loan->liquidation_price ?? 0, 0, ',', '.')) }}
                        </td>
                        <td class="py-4 px-6">
                            @if($loan->current_ltv >= 80)
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                    {{ __('Margin Call Sent') }}
                                </span>
                            @else
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                    {{ __('Warning Active') }}
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            @php
                                $canManage = Auth::check() && (
                                    Auth::id() === $loan->borrower_id ||
                                    $loan->fundings()->where('lender_id', Auth::id())->exists() ||
                                    Auth::user()->isInternalStaff()
                                );
                            @endphp
                            <a href="{{ $canManage ? route('loans.installments', $loan->id) : route('marketplace.show', $loan->id) }}" 
                               class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-slate-900 text-white text-xs font-bold hover:bg-slate-800 transition-colors shadow-xs">
                                {{ $canManage ? __('Manage') : __('View Loan') }} &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-8 text-center text-slate-400 font-medium">
                            {{ __('No active crypto collateral positions found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
