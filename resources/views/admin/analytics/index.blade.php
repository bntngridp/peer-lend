@extends('layouts.admin')

@section('title', __('Platform Analytics') . ' - Admin Terminal')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="{ 
    showExportModal: false,
    exportFormat: 'csv',
    toastMessage: '',
    showToast: false,
    triggerToast(msg) {
        this.toastMessage = msg;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 4000);
    },
    downloadReport() {
        this.showExportModal = false;
        
        // Generate CSV file Blob for client download
        const headers = ['Metric / Period', 'Disbursement ($M)', 'Repayment ($M)', 'Default Rate (%)', 'LCR (%)'];
        const rows = [
            ['{{ $timeframeLabel }} Total', '{{ $totalDisbursement }}', '{{ $totalRepayment }}', '1.24%', '145%'],
            @foreach($disbursementData['labels'] as $idx => $label)
            ['{{ $label }}', '{{ $disbursementData['disbursements'][$idx] }}', '{{ $disbursementData['repayments'][$idx] }}', '1.24%', '145%'],
            @endforeach
        ];
        
        let csvContent = 'data:text/csv;charset=utf-8,' + [headers.join(','), ...rows.map(e => e.join(','))].join('\n');
        let encodedUri = encodeURI(csvContent);
        let link = document.createElement('a');
        link.setAttribute('href', encodedUri);
        link.setAttribute('download', `platform_analytics_report_${this.exportFormat}_{{ $days }}_days.csv`);
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);

        this.triggerToast(__('Institutional Analytics Report exported successfully!'));
    }
}" class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">
    
    {{-- ─── Toast Notification Popup ────────────────────────────────────────── --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-20 right-6 z-50 flex items-center gap-3 rounded-2xl bg-slate-900 dark:bg-slate-800 border border-slate-700 dark:border-slate-600 px-4 py-3 text-sm font-semibold text-white shadow-2xl"
         style="display: none;">
        <div class="h-8 w-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <span x-text="toastMessage"></span>
        <button type="button" @click="showToast = false" class="ml-2 text-slate-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- ─── Header & Date Filter Bar ────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ __('Platform Analytics') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Comprehensive view of institutional lending metrics, liquidity ratios, & credit risk distribution.') }}
            </p>
        </div>
        
        <form method="GET" action="{{ route('admin.analytics.index') }}" class="flex items-center gap-3 flex-shrink-0">
            {{-- Date Filter Dropdown (30 Days, 90 Days, 1 Year) --}}
            <div class="relative">
                <select name="days" 
                        onchange="this.form.submit()" 
                        class="appearance-none rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-4 py-2 pr-9 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-indigo-500 shadow-xs cursor-pointer">
                    <option value="30" {{ $days == 30 ? 'selected' : '' }}>{{ __('Last 30 Days') }}</option>
                    <option value="90" {{ $days == 90 ? 'selected' : '' }}>{{ __('Last 90 Days') }}</option>
                    <option value="365" {{ $days == 365 ? 'selected' : '' }}>{{ __('Last 1 Year') }}</option>
                </select>
                <svg class="w-4 h-4 absolute right-3 top-2.5 text-slate-400 pointer-events-none" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                </svg>
            </div>

            {{-- Export Report Button --}}
            <button type="button" 
                    @click="showExportModal = true" 
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                </svg>
                <span>{{ __('Export Report') }}</span>
            </button>
        </form>
    </div>

    {{-- ─── 4 High-Density Stats Grid ────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        {{-- Stat 1: Total Liquidity Pool --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                {{ __('Total Liquidity Pool') }}
            </span>
            <p class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">
                ${{ $days == 365 ? '2.84' : ($days == 90 ? '1.42' : '842.5') }}{{ $days == 30 ? 'M' : 'B' }}
            </p>
            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1 block">
                +6.3% {{ __('from previous period') }}
            </span>
        </div>

        {{-- Stat 2: Platform Default Rate --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                {{ __('Platform Default Rate') }}
            </span>
            <p class="text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-2">
                1.24%
            </p>
            <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 mt-1 block">
                {{ __('Stable vs benchmark (1.5%)') }}
            </span>
        </div>

        {{-- Stat 3: Liquidity Coverage (LCR) --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                {{ __('Liquidity Coverage (LCR)') }}
            </span>
            <p class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">
                145%
            </p>
            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1 block">
                {{ __('Exceeds 100% Basel target') }}
            </span>
        </div>

        {{-- Stat 4: Net Stable Funding (NSFR) --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                {{ __('Net Stable Funding (NSFR)') }}
            </span>
            <p class="text-2xl font-black text-slate-900 dark:text-slate-100 mt-2">
                118%
            </p>
            <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 mt-1 block">
                {{ __('Fully Compliant') }}
            </span>
        </div>

    </div>

    {{-- ─── Charts Row ───────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Loan Disbursement vs Repayment Chart (Spans 8 Cols) --}}
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                    {{ __('Loan Disbursement vs. Repayment') }}
                </h3>
                <span class="text-xs font-bold text-slate-500 dark:text-slate-400">
                    {{ $timeframeLabel }} (Total: ${{ $totalDisbursement }}M)
                </span>
            </div>
            
            <div class="h-[300px]">
                <canvas id="disbursementChart"></canvas>
            </div>
        </div>

        {{-- Liquidity & Risk Ratios (Spans 4 Cols) --}}
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Liquidity Ratios Summary') }}
                </h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-3 gap-2">
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-center">
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 block">75%</span>
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase block mt-0.5">{{ __('LCR Ratio') }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-center">
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 block">90%</span>
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase block mt-0.5">{{ __('NSFR') }}</span>
                        </div>
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-center">
                            <span class="text-lg font-black text-emerald-600 dark:text-emerald-400 block">40%</span>
                            <span class="text-[9px] font-bold text-slate-400 dark:text-slate-500 uppercase block mt-0.5">{{ __('Cash Reserve') }}</span>
                        </div>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/40 border border-slate-200 dark:border-slate-800 text-xs space-y-2">
                        <div class="flex justify-between font-medium">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Timeframe Filter:') }}</span>
                            <span class="font-bold text-slate-900 dark:text-slate-100">{{ $timeframeLabel }}</span>
                        </div>
                        <div class="flex justify-between font-medium">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Disbursement Volume:') }}</span>
                            <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ $totalDisbursement }}M</span>
                        </div>
                        <div class="flex justify-between font-medium">
                            <span class="text-slate-500 dark:text-slate-400">{{ __('Repayment Received:') }}</span>
                            <span class="font-bold text-blue-600 dark:text-blue-400">${{ $totalRepayment }}M</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: EXPORT ANALYTICS REPORT --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showExportModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showExportModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showExportModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Export Analytics Report') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Download platform metrics for timeframe:') }} <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ $timeframeLabel }}</span>
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                        {{ __('File Format') }}
                    </label>
                    <select x-model="exportFormat" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                        <option value="csv">{{ __('CSV Spreadsheet (.csv)') }}</option>
                        <option value="pdf">{{ __('PDF Executive Summary (.pdf)') }}</option>
                        <option value="json">{{ __('JSON Audit Stream (.json)') }}</option>
                    </select>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-[11px] space-y-1 text-slate-600 dark:text-slate-300 font-medium">
                    <div class="flex justify-between">
                        <span>{{ __('Timeframe Scope:') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ $timeframeLabel }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Disbursement Volume:') }}</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ $totalDisbursement }}M</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Repayment Volume:') }}</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">${{ $totalRepayment }}M</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showExportModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="downloadReport()" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>{{ __('Confirm & Download') }}</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const isDark = document.documentElement.classList.contains('dark');
        const gridColor = isDark ? 'rgba(51, 65, 85, 0.4)' : '#f1f5f9';
        const textColor = isDark ? '#94a3b8' : '#64748b';

        const ctx = document.getElementById('disbursementChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($disbursementData['labels']),
                datasets: [
                    {
                        label: '{{ __("Loan Disbursement") }} ($M)',
                        data: @json($disbursementData['disbursements']),
                        borderColor: '#10b981',
                        backgroundColor: 'rgba(16, 185, 129, 0.12)',
                        tension: 0.35,
                        fill: true,
                        pointBackgroundColor: '#10b981',
                        pointRadius: 4
                    },
                    {
                        label: '{{ __("Repayments Received") }} ($M)',
                        data: @json($disbursementData['repayments']),
                        borderColor: '#3b82f6',
                        backgroundColor: 'transparent',
                        tension: 0.35,
                        pointBackgroundColor: '#3b82f6',
                        pointRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { 
                    legend: { 
                        position: 'bottom',
                        labels: { color: textColor, font: { weight: 'bold', size: 11 } }
                    } 
                },
                scales: {
                    x: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { weight: 'semibold' } }
                    },
                    y: {
                        grid: { color: gridColor },
                        ticks: { color: textColor, font: { weight: 'semibold' } }
                    }
                }
            }
        });
    });
</script>
@endsection
