@extends('layouts.app')

@section('title', __('Platform Analytics') . ' - Admin Terminal')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Platform Analytics') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Comprehensive view of institutional lending metrics, liquidity ratios, & credit risk distribution.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <select class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none">
                <option value="30">{{ __('Last 30 Days') }}</option>
                <option value="90">{{ __('Last 90 Days') }}</option>
                <option value="365">{{ __('Last 1 Year') }}</option>
            </select>
            <button type="button" @click="alert('Exporting Institutional Analytics Report...')" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                {{ __('Export Report') }}
            </button>
        </div>
    </div>

    <!-- 4 High-Density Stats Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Total Liquidity Pool') }}</span>
            <p class="text-2xl font-black text-slate-900 mt-2">${{ __n('842.5') }}M</p>
            <span class="text-[10px] font-bold text-emerald-700 mt-1 block">+{{ __n('6.3') }}% {{ __('from last month') }}</span>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Platform Default Rate') }}</span>
            <p class="text-2xl font-black text-emerald-700 mt-2">{{ __n('1.24') }}%</p>
            <span class="text-[10px] font-bold text-slate-500 mt-1 block">{{ __('Stable vs benchmark (1.5%)') }}</span>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Liquidity Coverage (LCR)') }}</span>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ __n(145) }}%</p>
            <span class="text-[10px] font-bold text-emerald-700 mt-1 block">{{ __('Exceeds 100% Basel target') }}</span>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Net Stable Funding (NSFR)') }}</span>
            <p class="text-2xl font-black text-slate-900 mt-2">{{ __n(118) }}%</p>
            <span class="text-[10px] font-bold text-emerald-700 mt-1 block">{{ __('Compliant') }}</span>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Loan Disbursement vs Repayment Chart (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Loan Disbursement vs. Repayment') }}</h3>
            <div class="h-[280px]">
                <canvas id="disbursementChart"></canvas>
            </div>
        </div>

        <!-- Liquidity & Risk Ratios (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Liquidity Ratios') }}</h3>
                
                <div class="space-y-4 text-center">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-lg font-black text-emerald-700 block">{{ __n(75) }}%</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">{{ __('LCR Ratio') }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-lg font-black text-emerald-700 block">{{ __n(90) }}%</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">{{ __('NSFR') }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                            <span class="text-lg font-black text-emerald-700 block">{{ __n(40) }}%</span>
                            <span class="text-[9px] font-bold text-slate-400 uppercase block">{{ __('Cash Reserve') }}</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('disbursementChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
                datasets: [
                    {
                        label: '{{ __("Loan Disbursement") }} ($M)',
                        data: [12.4, 18.2, 15.6, 22.4],
                        borderColor: '#15803D',
                        backgroundColor: 'rgba(21, 128, 61, 0.1)',
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: '{{ __("Repayments Received") }} ($M)',
                        data: [10.1, 14.5, 13.8, 19.1],
                        borderColor: '#3B82F6',
                        backgroundColor: 'transparent',
                        tension: 0.3
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { position: 'bottom' } }
            }
        });
    });
</script>
@endsection
