@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ collateralTab: 'overview' }">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Crypto Collateral Overview</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Institutional monitoring of pledged digital assets and liquidation thresholds.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                Export Report
            </button>
        </div>
    </div>

    <!-- 3 Summary Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: TOTAL PLEDGED VALUE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL PLEDGED VALUE</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">+2.4%</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    $142,550,000
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Across 1,245 active collateral positions</p>
            </div>
        </div>

        <!-- Card 2: WEIGHTED AVG LTV -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">WEIGHTED AVG LTV</span>
                <span class="text-xs font-bold text-emerald-700">Target &lt;45%</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    42.8% <span class="text-xs font-bold text-slate-400 font-normal">(+1.2%)</span>
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Margin Call Threshold: 65%</p>
            </div>
        </div>

        <!-- Card 3: PORTFOLIO RISK STATUS -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">PORTFOLIO RISK STATUS</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 text-amber-800 border border-amber-200">Monitor</span>
            </div>
            <div class="mt-3 space-y-1 text-xs font-semibold">
                <div class="flex items-center gap-1.5 text-rose-600">
                    <span class="h-2 w-2 rounded-full bg-rose-600"></span> 3 Loans in Liquidation Zone
                </div>
                <div class="flex items-center gap-1.5 text-amber-600">
                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> 12 Loans near Margin Call
                </div>
                <div class="flex items-center gap-1.5 text-emerald-700">
                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span> 1,230 Healthy Positions
                </div>
            </div>
        </div>
    </div>

    <!-- Collateral Distribution & LTV Monitoring Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Collateral Distribution (Spans 6 Cols) -->
        <div class="lg:col-span-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Collateral Distribution</h3>
                <span class="text-xs font-bold text-emerald-700">3 Currencies</span>
            </div>

            <div class="space-y-3">
                <!-- Bitcoin -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-amber-500 text-white font-black flex items-center justify-center text-xs shadow-xs">₿</div>
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Bitcoin (BTC)</span>
                            <span class="text-[10px] text-slate-400 font-medium block">1,253.4 BTC Locked</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs block">$75,450,000</span>
                        <span class="text-[10px] text-emerald-700 font-bold block">53% of Pool</span>
                    </div>
                </div>

                <!-- Ethereum -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-indigo-600 text-white font-black flex items-center justify-center text-xs shadow-xs">Ξ</div>
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Ethereum (ETH)</span>
                            <span class="text-[10px] text-slate-400 font-medium block">14,220 ETH Locked</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs block">$42,100,000</span>
                        <span class="text-[10px] text-emerald-700 font-bold block">30% of Pool</span>
                    </div>
                </div>

                <!-- Tether -->
                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="h-9 w-9 rounded-full bg-emerald-600 text-white font-black flex items-center justify-center text-xs shadow-xs">₮</div>
                        <div>
                            <span class="font-bold text-slate-900 text-xs block">Tether (USDT)</span>
                            <span class="text-[10px] text-slate-400 font-medium block">25,050,000 USDT</span>
                        </div>
                    </div>
                    <div class="text-right">
                        <span class="font-extrabold text-slate-900 text-xs block">$25,050,000</span>
                        <span class="text-[10px] text-emerald-700 font-bold block">17% of Pool</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: LTV Monitoring & Stress Test (Spans 6 Cols) -->
        <div class="lg:col-span-6 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">LTV Monitoring &amp; Stress Test</h3>
                <span class="text-[10px] font-bold text-slate-400 uppercase">Live Thresholds</span>
            </div>

            <!-- Risk Tiers -->
            <div class="space-y-3 text-xs font-medium">
                <div class="p-3 rounded-xl bg-rose-50 border border-rose-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-rose-900 block">High Risk (&gt;75% LTV)</span>
                        <span class="text-[10px] text-rose-700">14 Loans Affected</span>
                    </div>
                    <span class="font-black text-rose-700">$12.4M Exposed</span>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-amber-900 block">Medium Risk (60-75% LTV)</span>
                        <span class="text-[10px] text-amber-700">64 Loans Affected</span>
                    </div>
                    <span class="font-black text-amber-700">$34.1M Exposed</span>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 flex items-center justify-between">
                    <div>
                        <span class="font-bold text-emerald-900 block">Low Risk (&lt;60% LTV)</span>
                        <span class="text-[10px] text-emerald-700">1,167 Loans Healthy</span>
                    </div>
                    <span class="font-black text-emerald-700">$96.0M Exposed</span>
                </div>
            </div>

            <!-- Stress Test Scenarios Box -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 space-y-2 text-xs">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">STRESS TEST SCENARIOS</span>
                <div class="flex justify-between text-slate-600">
                    <span>BTC drops 20%</span>
                    <span class="font-bold text-rose-600">-$12.8M Impact</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>ETH drops 15%</span>
                    <span class="font-bold text-rose-600">-$6.4M Impact</span>
                </div>
                <div class="flex justify-between text-slate-600">
                    <span>Stablecoin Depeg (5%)</span>
                    <span class="font-bold text-rose-600">-$1.0M Impact</span>
                </div>
            </div>
        </div>

    </div>

    <!-- Liquidation Warnings Table Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <div class="flex items-center gap-2">
                <span class="text-rose-600 text-sm"></span>
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Liquidation Warnings &amp; Margin Calls</h3>
            </div>
            <span class="text-xs font-bold text-rose-700">Action Required</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">LOAN ID</th>
                        <th class="py-3.5 px-6">BORROWER</th>
                        <th class="py-3.5 px-6">COLLATERAL ASSET</th>
                        <th class="py-3.5 px-6">CURRENT LTV</th>
                        <th class="py-3.5 px-6">LIQ. PRICE</th>
                        <th class="py-3.5 px-6">STATUS</th>
                        <th class="py-3.5 px-6 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">#LN-8842</td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Genesis Block Partners</td>
                        <td class="py-4 px-6 font-bold text-indigo-700">Ξ ETH (150 ETH)</td>
                        <td class="py-4 px-6 font-extrabold text-rose-600">82.4%</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">$2,840.00</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">
                                Margin Call Sent
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="py-1.5 px-3 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 shadow-xs">
                                Manage &rarr;
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">#LN-9011</td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Apex Capital Ltd.</td>
                        <td class="py-4 px-6 font-bold text-amber-600">₿ BTC (12.5 BTC)</td>
                        <td class="py-4 px-6 font-extrabold text-amber-600">76.1%</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">$59,200.00</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                Warning Active
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs">
                                Manage &rarr;
                            </button>
                        </td>
                    </tr>

                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">#LN-7722</td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Meridian Yield Fund</td>
                        <td class="py-4 px-6 font-bold text-amber-600">₿ BTC (45.0 BTC)</td>
                        <td class="py-4 px-6 font-extrabold text-amber-600">75.5%</td>
                        <td class="py-4 px-6 font-semibold text-slate-900">$58,100.00</td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">
                                Warning Active
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs">
                                Manage &rarr;
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
