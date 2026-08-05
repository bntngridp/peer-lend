@extends('layouts.admin')

@section('title', __('Platform Analytics') . ' - Admin Terminal')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div x-data="platformAnalyticsApp()" class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">
    
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
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('Liquidity Pool') }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                    {{ __n('+14.2%') }}
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                    ${{ __n($totalLiquidityFormatted) }}
                </span>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ __('IDR Equiv.') }}
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                {{ __('Available capital for institutional disbursement') }}
            </p>
        </div>

        {{-- Stat 2: Platform Default Rate --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('Default Rate (NPL)') }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400">
                    {{ __n('-0.18%') }}
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-emerald-600 dark:text-emerald-400">
                    {{ __n(number_format($nplRate, 2)) }}%
                </span>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ __('Target') }} &lt; {{ __n('2.5%') }}
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                {{ __('Maintained well below risk limit thresholds') }}
            </p>
        </div>

        {{-- Stat 3: Liquidity Coverage Ratio (LCR) --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('LCR Ratio') }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-indigo-100 dark:bg-indigo-950/60 text-indigo-700 dark:text-indigo-400">
                    {{ __('Healthy') }}
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                    {{ __n(number_format($lcrRatio, 1)) }}%
                </span>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ __('Min') }} {{ __n('100%') }}
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                {{ __('High-quality liquid assets vs 30-day net cash outflow') }}
            </p>
        </div>

        {{-- Stat 4: Net Stable Funding (NSFR) --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <div class="flex items-center justify-between">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">
                    {{ __('NSFR Ratio') }}
                </span>
                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold bg-blue-100 dark:bg-blue-950/60 text-blue-700 dark:text-blue-400">
                    {{ __('Optimal') }}
                </span>
            </div>
            <div class="mt-3 flex items-baseline justify-between">
                <span class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                    {{ __n(number_format($nsfrRatio, 1)) }}%
                </span>
                <span class="text-xs font-semibold text-slate-400 dark:text-slate-500">
                    {{ __('Min') }} {{ __n('100%') }}
                </span>
            </div>
            <p class="mt-2 text-[11px] text-slate-500 dark:text-slate-400">
                {{ __('Available stable funding vs required stable funding') }}
            </p>
        </div>

    </div>

    {{-- ─── Main Content Grid: Chart & Risk Breakdown ────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        {{-- Chart Section (2 Columns) --}}
        <div class="lg:col-span-2 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Disbursement vs Repayment Trend') }}
                    </h2>
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        {{ __('Historical performance metrics for scope:') }} <span class="font-bold text-slate-700 dark:text-slate-300">{{ $timeframeLabel }}</span>
                    </p>
                </div>
                <div class="flex items-center gap-4 text-xs font-semibold">
                    <span class="flex items-center gap-1.5 text-emerald-600 dark:text-emerald-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span>
                        {{ __('Disbursement') }} (${{ __n($totalDisbursement) }}M)
                    </span>
                    <span class="flex items-center gap-1.5 text-blue-600 dark:text-blue-400">
                        <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span>
                        {{ __('Repayment') }} (${{ __n($totalRepayment) }}M)
                    </span>
                </div>
            </div>

            <div class="h-72 w-full relative">
                <canvas id="disbursementChart"></canvas>
            </div>
        </div>

        {{-- Risk Tier & Asset Quality (1 Column) --}}
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-5">
            <div>
                <h2 class="text-base font-bold text-slate-900 dark:text-slate-100">
                    {{ __('Credit Risk Tier Distribution') }}
                </h2>
                <p class="text-xs text-slate-500 dark:text-slate-400">
                    {{ __('Portfolio composition across institutional risk classes') }}
                </p>
            </div>

            <div class="space-y-4 text-xs">
                <div>
                    <div class="flex justify-between font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <span>{{ __('Tier AAA (Low Risk)') }}</span>
                        <span>{{ __n(number_format($riskDistribution['AAA'], 1)) }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-emerald-500 rounded-full" style="width: {{ $riskDistribution['AAA'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <span>{{ __('Tier AA (Moderate Risk)') }}</span>
                        <span>{{ __n(number_format($riskDistribution['AA'], 1)) }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-indigo-500 rounded-full" style="width: {{ $riskDistribution['AA'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <span>{{ __('Tier A (Balanced Risk)') }}</span>
                        <span>{{ __n(number_format($riskDistribution['A'], 1)) }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-amber-500 rounded-full" style="width: {{ $riskDistribution['A'] }}%"></div>
                    </div>
                </div>

                <div>
                    <div class="flex justify-between font-bold text-slate-700 dark:text-slate-300 mb-1">
                        <span>{{ __('Tier B (High Yield / Subprime)') }}</span>
                        <span>{{ __n(number_format($riskDistribution['B'], 1)) }}%</span>
                    </div>
                    <div class="w-full h-2 rounded-full bg-slate-100 dark:bg-slate-800 overflow-hidden">
                        <div class="h-full bg-rose-500 rounded-full" style="width: {{ $riskDistribution['B'] }}%"></div>
                    </div>
                </div>
            </div>

            <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-800/50 border border-slate-200 dark:border-slate-700 space-y-2">
                <span class="text-xs font-bold text-slate-900 dark:text-slate-100">
                    {{ __('Asset Quality Summary') }}
                </span>
                <p class="text-[11px] text-slate-500 dark:text-slate-400">
                    {{ __('Over :percent% of portfolio assets are rated AA or higher. Collateral coverage ratio stands firm at :lcr%.', ['percent' => __n(number_format($topTierPercent, 1)), 'lcr' => __n(number_format($lcrRatio, 1))]) }}
                </p>
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
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">${{ __n($totalDisbursement) }}M</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Repayment Volume:') }}</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">${{ __n($totalRepayment) }}M</span>
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
    function platformAnalyticsApp() {
        return {
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
                const timeframe = {!! json_encode($timeframeLabel) !!};
                const days = {!! json_encode((string)$days) !!};
                const totalDisbursement = {!! json_encode((string)$totalDisbursement) !!};
                const totalRepayment = {!! json_encode((string)$totalRepayment) !!};
                
                const periodData = [
                    @foreach($disbursementData['labels'] as $idx => $label)
                    {
                        period: {!! json_encode($label) !!},
                        disbursement: {{ (float) $disbursementData['disbursements'][$idx] }},
                        repayment: {{ (float) $disbursementData['repayments'][$idx] }},
                        defaultRate: '1.24%',
                        lcr: '145%'
                    },
                    @endforeach
                ];

                if (this.exportFormat === 'csv') {
                    const headers = ['Period / Metric', 'Disbursement ($M)', 'Repayment ($M)', 'Default Rate (%)', 'LCR (%)'];
                    const rows = [
                        headers.join(','),
                        `"Summary (${timeframe})","${totalDisbursement}","${totalRepayment}","1.24%","145%"`,
                        ...periodData.map(r => `"${r.period}","${r.disbursement}","${r.repayment}","${r.defaultRate}","${r.lcr}"`)
                    ];
                    const blob = new Blob([rows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', `lendflow_analytics_${days}_days.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.triggerToast({!! json_encode(__('Platform Analytics CSV Report exported successfully!')) !!});
                } 
                else if (this.exportFormat === 'json') {
                    const jsonOutput = {
                        report_title: 'LendFlow Institutional Platform Analytics Report',
                        generated_at: new Date().toISOString(),
                        timeframe_scope: timeframe,
                        timeframe_days: parseInt(days),
                        summary_metrics: {
                            total_liquidity_pool: '$' + (days == '365' ? '2.84B' : (days == '90' ? '1.42B' : '842.5M')),
                            platform_default_rate: '1.24%',
                            liquidity_coverage_ratio_lcr: '145%',
                            net_stable_funding_ratio_nsfr: '118%',
                            total_disbursement_millions: parseFloat(totalDisbursement),
                            total_repayment_millions: parseFloat(totalRepayment)
                        },
                        period_breakdown: periodData
                    };
                    const blob = new Blob([JSON.stringify(jsonOutput, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', `lendflow_analytics_${days}_days.json`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.triggerToast({!! json_encode(__('Platform Analytics JSON Stream exported successfully!')) !!});
                } 
                else if (this.exportFormat === 'pdf') {
                    const printWindow = window.open('', '_blank');
                    const printContent = `
                        <!DOCTYPE html>
                        <html>
                        <head>
                            <title>LendFlow Platform Analytics Report (${timeframe})</title>
                            <style>
                                body { font-family: 'Segoe UI', system-ui, -apple-system, sans-serif; color: #0f172a; margin: 0; padding: 40px; background: #fff; }
                                .header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #10b981; padding-bottom: 20px; margin-bottom: 30px; }
                                .brand { font-size: 24px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px; }
                                .brand span { color: #10b981; }
                                .title { text-align: right; }
                                .title h1 { font-size: 18px; margin: 0; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
                                .title p { font-size: 12px; color: #64748b; margin: 4px 0 0 0; }
                                .grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 16px; margin-bottom: 30px; }
                                .card { border: 1px solid #e2e8f0; border-radius: 12px; padding: 16px; background: #f8fafc; }
                                .card-label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; }
                                .card-val { font-size: 20px; font-weight: 900; color: #0f172a; margin-top: 6px; }
                                table { width: 100%; border-collapse: collapse; margin-top: 20px; }
                                th { background: #0f172a; color: #fff; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 10px 14px; text-align: left; }
                                td { border-bottom: 1px solid #e2e8f0; font-size: 12px; padding: 12px 14px; color: #334155; }
                                tr:nth-child(even) { background: #f8fafc; }
                                .footer { margin-top: 50px; border-top: 1px solid #e2e8f0; padding-top: 16px; font-size: 11px; color: #94a3b8; text-align: center; }
                            </style>
                        </head>
                        <body>
                            <div class="header">
                                <div class="brand">Lend<span>Flow</span></div>
                                <div class="title">
                                    <h1>Institutional Platform Analytics</h1>
                                    <p>Timeframe: ${timeframe} | Exported: ${new Date().toLocaleString()}</p>
                                </div>
                            </div>

                            <div class="grid">
                                <div class="card">
                                    <div class="card-label">Liquidity Pool</div>
                                    <div class="card-val">${days == '365' ? '$2.84B' : (days == '90' ? '$1.42B' : '$842.5M')}</div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Default Rate</div>
                                    <div class="card-val" style="color:#10b981">1.24%</div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Total Disbursement</div>
                                    <div class="card-val">$${totalDisbursement}M</div>
                                </div>
                                <div class="card">
                                    <div class="card-label">Total Repayments</div>
                                    <div class="card-val" style="color:#3b82f6">$${totalRepayment}M</div>
                                </div>
                            </div>

                            <h3>Period-by-Period Breakdown</h3>
                            <table>
                                <thead>
                                    <tr>
                                        <th>Period</th>
                                        <th>Disbursement ($M)</th>
                                        <th>Repayment ($M)</th>
                                        <th>Default Rate</th>
                                        <th>LCR</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    ${periodData.map(r => `
                                        <tr>
                                            <td><strong>${r.period}</strong></td>
                                            <td>$${r.disbursement}M</td>
                                            <td>$${r.repayment}M</td>
                                            <td>${r.defaultRate}</td>
                                            <td>${r.lcr}</td>
                                        </tr>
                                    `).join('')}
                                </tbody>
                            </table>

                            <div class="footer">
                                Confidential Report — LendFlow Institutional P2P Lending Platform &copy; ${new Date().getFullYear()}
                            </div>
                        </body>
                        </html>
                    `;
                    printWindow.document.write(printContent);
                    printWindow.document.close();
                    printWindow.print();
                    this.triggerToast({!! json_encode(__('PDF Executive Analytics Report generated!')) !!});
                }
            }
        };
    }

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
