@extends('layouts.app')

@section('title', __('Dashboard'))

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ADMIN DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if($role === 'admin')

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('System Administration') }}</h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ __('Global platform oversight and operational health. — Platform Overview') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                <span class="h-2 w-2 rounded-full bg-emerald-600 dark:bg-emerald-400 animate-pulse"></span>
                {{ __('System Status: Operational') }}
            </span>
        </div>
    </div>

    <!-- 4 High-Density Stat Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Users -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Total Users') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">{{ __n('+2.4%') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 mt-3">{{ __n(number_format($stats['total_users'])) }}</p>
        </div>

        <!-- Card 2: Pending KYC -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Pending KYC') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">{{ __('Action Req.') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 mt-3">{{ __n(number_format($stats['kyc_pending'])) }}</p>
        </div>

        <!-- Card 3: Total Volume -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('Total Volume') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ __('30 Days') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 mt-3">Rp {{ __n(number_format($stats['total_disbursed'], 0, ',', '.')) }}</p>
        </div>

        <!-- Card 4: System Health -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">{{ __('System Health') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">{{ __('Stable') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400 mt-3">{{ __n('99.9%') }}</p>
        </div>
    </div>

    <!-- Main Grid: 2/3 Left Column, 1/3 Right Column -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ─── Left Column (2/3 Width) ─────────────────────────────────────── -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Platform Statistics (Line Chart) -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ __('Platform Statistics') }}</h3>
                    <span class="px-3 py-1 rounded-lg text-xs font-semibold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 border border-slate-200 dark:border-slate-700">{{ __('Last 30 Days') }}</span>
                </div>
                <div class="h-[250px]">
                    <canvas id="adminPlatformChart"></canvas>
                </div>
            </div>

            <!-- Recent Loan Applications Table -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Recent Loan Applications') }}</h3>
                    <a href="{{ route('admin.loans.index') }}" class="text-xs font-bold text-emerald-700 dark:text-emerald-400 hover:text-emerald-800 dark:hover:text-emerald-300">{{ __('View All Applications') }} &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 dark:bg-slate-950/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-6">{{ __('Borrower') }}</th>
                                <th class="py-3 px-6">{{ __('Amount') }}</th>
                                <th class="py-3 px-6">{{ __('Status') }}</th>
                                <th class="py-3 px-6">{{ __('Applied Date') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                            @forelse($stats['recent_loans'] as $loan)
                            <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-900 dark:text-slate-100">{{ $loan->borrower?->profile?->full_name ?? 'N/A' }}</td>
                                <td class="py-3.5 px-6 font-semibold text-slate-900 dark:text-slate-100">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</td>
                                <td class="py-3.5 px-6">
                                    @php
                                        $statusBadge = match($loan->status) {
                                            'pending'      => 'bg-amber-500/15 text-amber-400 border border-amber-500/30',
                                            'open_funding' => 'bg-indigo-500/15 text-indigo-400 border border-indigo-500/30',
                                            'funded'       => 'bg-purple-500/15 text-purple-400 border border-purple-500/30',
                                            'active'       => 'bg-green-500/450 text-green-400 border border-green-500/30',
                                            'completed'    => 'bg-slate-500/15 text-slate-300 border border-slate-500/30',
                                            'default'      => 'bg-slate-500/15 text-slate-400 border border-slate-500/30',
                                        };
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $statusBadge }}">
                                        {{ __(ucfirst(str_replace('_', ' ', $loan->status))) }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 text-slate-400 dark:text-slate-400">{{ $loan->created_at->diffForHumans() }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-400">{{ __('No applications recorded.') }}</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ─── Right Column (1/3 Width) ────────────────────────────────────── -->
        <div class="space-y-6">

            <!-- Card: System Alerts -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 flex items-center gap-2">
                        <svg class="h-4 w-4 text-rose-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                        </svg>
                        <span>{{ __('System Alerts') }}</span>
                    </h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 dark:bg-rose-950/80 text-rose-700 dark:text-rose-300 border border-rose-200 dark:border-rose-800">{{ __('3 New') }}</span>
                </div>

                <div class="space-y-3">
                    <!-- Alert 1: Large Withdrawal -->
                    <div class="p-3.5 rounded-xl bg-rose-50 dark:bg-rose-950/40 border border-rose-200 dark:border-rose-900/70 space-y-1">
                        <div class="flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 text-rose-600 dark:text-rose-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <p class="text-xs font-bold text-rose-900 dark:text-rose-200">{{ __('Large Withdrawal Detected') }}</p>
                        </div>
                        <p class="text-[11px] text-rose-700 dark:text-rose-300/90 leading-relaxed font-medium">{{ __('Unusual outbound transfer of Rp 50.000.000 initiated by Account #8492.') }}</p>
                        <p class="text-[10px] font-semibold text-rose-600 dark:text-rose-400">{{ __('2 mins ago') }}</p>
                    </div>

                    <!-- Alert 2: API Latency Spike -->
                    <div class="p-3.5 rounded-xl bg-sky-50 dark:bg-sky-950/40 border border-sky-200 dark:border-sky-900/70 space-y-1">
                        <div class="flex items-center gap-2">
                            <svg class="h-3.5 w-3.5 text-sky-600 dark:text-sky-400 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <p class="text-xs font-bold text-sky-900 dark:text-sky-200">{{ __('API Latency Spike') }}</p>
                        </div>
                        <p class="text-[11px] text-sky-700 dark:text-sky-300/90 leading-relaxed font-medium">{{ __('Payment gateway response times exceeded 2000ms for 30 seconds.') }}</p>
                        <p class="text-[10px] font-semibold text-sky-600 dark:text-sky-400">{{ __('15 mins ago') }}</p>
                    </div>
                </div>
            </div>

            <!-- Card: Quick Management -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-1">{{ __('QUICK MANAGEMENT') }}</span>
                <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mb-4">{{ __('Global platform parameters and approvals.') }}</p>

                <div class="space-y-2">
                    <a href="{{ route('admin.kyc.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-600 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"></span>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-800 dark:group-hover:text-emerald-400">{{ __('Review Pending KYC') }}</span>
                        </div>
                        <span class="text-xs font-bold text-amber-700 dark:text-amber-300 bg-amber-50 dark:bg-amber-950/60 px-2 py-0.5 rounded-md border border-amber-200 dark:border-amber-800/80">{{ $stats['kyc_pending'] }}</span>
                    </a>

                    <a href="{{ route('admin.loans.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 dark:border-slate-800 hover:border-emerald-600 dark:hover:border-emerald-500 hover:bg-emerald-50/50 dark:hover:bg-emerald-950/30 transition-all group">
                        <div class="flex items-center gap-2.5">
                            <span class="text-sm"></span>
                            <span class="text-xs font-bold text-slate-900 dark:text-slate-100 group-hover:text-emerald-800 dark:group-hover:text-emerald-400">{{ __('Review Loan Applications') }}</span>
                        </div>
                        <span class="text-xs font-bold text-blue-700 dark:text-blue-300 bg-blue-50 dark:bg-blue-950/60 px-2 py-0.5 rounded-md border border-blue-200 dark:border-blue-800/80">{{ $stats['loans_pending'] }}</span>
                    </a>
                </div>
            </div>

        </div>

    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- BORROWER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @elseif($role === 'borrower')
    <div x-data="{
        showRepayModal: false,
        repayItem: null,
        walletBalance: {{ (float)($stats['wallet_balance'] ?? 0) }},
        openRepaymentModal(item) {
            this.repayItem = item;
            this.showRepayModal = true;
        },
        formatRupiah(num) {
            return new Intl.NumberFormat('id-ID').format(num);
        }
    }" class="space-y-6">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Welcome back, :name!', ['name' => Auth::user()->profile?->full_name ?? __('Borrower')]) }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Overview of your current loans and upcoming obligations.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                {{ __('Apply for New Loan') }}
            </a>
        </div>
    </div>

    <!-- 4 High-Density Stat Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: OUTSTANDING LOAN -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('OUTSTANDING LOAN') }}</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ __n(number_format($stats['outstanding_amount'], 0, ',', '.')) }}
            </p>
        </div>

        <!-- Card 2: MONTHLY INSTALLMENT -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('MONTHLY INSTALLMENT') }}</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ __n(number_format($stats['monthly_installment_amount'], 0, ',', '.')) }}
            </p>
        </div>

        <!-- Card 3: WALLET BALANCE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('WALLET BALANCE') }}</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ __n(number_format($stats['wallet_balance'], 0, ',', '.')) }}
            </p>
        </div>

        <!-- Card 4: CREDIT SCORE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('CREDIT SCORE') }}</span>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="text-2xl font-extrabold text-emerald-700">{{ __n($stats['credit_score']) }}</span>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">{{ __('Grade') }} {{ $stats['credit_grade'] }}</span>
            </div>
        </div>
    </div>

    <!-- Main Grid: 2/3 Left Column, 1/3 Right Column -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ─── Left Column (2/3 Width) ─────────────────────────────────────── -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Upcoming Repayments Table -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Upcoming Repayments') }}</h3>
                    <a href="{{ route('loans.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ __('View All Schedule') }} &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-6">{{ __('DATE') }}</th>
                                <th class="py-3 px-6">{{ __('AMOUNT') }}</th>
                                <th class="py-3 px-6">{{ __('STATUS') }}</th>
                                <th class="py-3 px-6 text-right">{{ __('ACTION') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($stats['upcoming_installments'] as $inst)
                            @php
                                $isOverdue = now()->gt($inst->due_date) && $inst->status !== 'paid';
                            @endphp
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-900">
                                    {{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}
                                </td>
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    Rp {{ __n(number_format($inst->total_amount, 0, ',', '.')) }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($inst->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            {{ __('Paid') }}
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            {{ __('Due Soon / Overdue') }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            {{ __('Scheduled') }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($inst->status !== 'paid')
                                        <button type="button" 
                                                @click="openRepaymentModal({
                                                    id: '{{ $inst->id }}',
                                                    number: '{{ $inst->installment_number }}',
                                                    due_date: '{{ \Carbon\Carbon::parse($inst->due_date)->format('M d, Y') }}',
                                                    principal: {{ (float)$inst->principal_amount }},
                                                    interest: {{ (float)$inst->interest_amount }},
                                                    penalty: {{ (float)($inst->penalty_amount ?? 0) }},
                                                    total: {{ (float)($inst->total_due ?? $inst->total_amount) }},
                                                    pay_url: '{{ route('repayments.pay', $inst->id) }}'
                                                })"
                                                class="inline-flex items-center px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition-all shadow-xs cursor-pointer">
                                            {{ __('Pay Now') }}
                                        </button>
                                    @else
                                        <a href="{{ route('loans.installments', $inst->loan_id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                            {{ __('Details') }}
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="py-8 text-center text-slate-500">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-9 w-9 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs font-bold text-slate-700">{{ __('No Upcoming Repayments') }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ __('You have no active loan installments due at this time.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card: Active Application Progress -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4">{{ __('Active Application Progress') }}</h3>
                
                @php
                    $app = $stats['active_application'];
                    $pct = $app ? (int)min(100, max(0, $app->funded_percentage)) : 0;
                @endphp

                @if($app)
                    <div class="flex flex-col sm:flex-row items-center gap-8">
                        <!-- Circular Donut Chart -->
                        <div class="relative w-36 h-36 flex-shrink-0 flex items-center justify-center">
                            <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                                <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                                <path class="text-emerald-700" stroke-dasharray="{{ $pct }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            </svg>
                            <div class="absolute text-center">
                                <span class="text-2xl font-black text-slate-900">{{ __n($pct) }}%</span>
                                <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Funded') }}</span>
                            </div>
                        </div>

                        <!-- Milestone Steps -->
                        <div class="flex-1 space-y-4">
                            <div class="flex items-start gap-3">
                                <div class="h-2.5 w-2.5 rounded-full {{ $app->status !== 'pending' ? 'bg-emerald-700' : 'bg-slate-300' }} mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ __('Application Status') }}: <span class="capitalize text-emerald-700">{{ __(ucwords(str_replace('_', ' ', $app->status))) }}</span></p>
                                    <p class="text-[11px] text-slate-400">{{ __('Submitted') }}: {{ \Carbon\Carbon::parse($app->created_at)->format('M d, Y') }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="h-2.5 w-2.5 rounded-full {{ $app->status === 'open_funding' ? 'bg-emerald-700 animate-pulse' : 'bg-slate-300' }} mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-xs font-bold text-slate-900">{{ __('Marketplace Listing') }}</p>
                                    <p class="text-[11px] text-slate-400">{{ __('Raised') }}: Rp {{ __n(number_format($app->funded_amount, 0, ',', '.')) }} / Rp {{ __n(number_format($app->amount, 0, ',', '.')) }}</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-3">
                                <div class="h-2.5 w-2.5 rounded-full {{ $app->status === 'active' || $app->status === 'funded' ? 'bg-emerald-700' : 'bg-slate-300' }} mt-1.5 flex-shrink-0"></div>
                                <div>
                                    <p class="text-xs font-bold {{ $app->status === 'active' ? 'text-emerald-700' : 'text-slate-500' }}">{{ __('Funds Disbursement') }}</p>
                                    <p class="text-[11px] text-slate-400">{{ $app->status === 'active' ? __('Funds Disbursed Successfully') : __('Waiting for 100% funding from lenders.') }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                @else
                    <div class="py-6 px-4 rounded-xl border border-dashed border-slate-200 bg-slate-50/60 text-center">
                        <svg class="h-8 w-8 text-slate-400 mx-auto mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z"/></svg>
                        <p class="text-xs font-bold text-slate-800">{{ __('No Active Loan Application') }}</p>
                        <p class="text-[11px] text-slate-500 mt-1 max-w-md mx-auto">{{ __('You currently have no active loan application running in the marketplace.') }}</p>
                        <a href="{{ route('loans.create') }}" class="mt-3 inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-all shadow-xs">
                            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                            {{ __('Apply for New Loan') }}
                        </a>
                    </div>
                @endif
            </div>

        </div>

        <!-- ─── Right Column (1/3 Width) ────────────────────────────────────── -->
        <div class="space-y-6">

            <!-- Card: Quick Actions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-3">{{ __('QUICK ACTIONS') }}</span>
                
                <div class="space-y-2">
                    <a href="{{ route('loans.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">{{ __('Apply for New Loan') }}</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-700 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>

                    <a href="{{ route('wallet.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">{{ __('Deposit Funds') }}</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-700 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>

                    <a href="{{ route('loans.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">{{ __('View Schedule') }}</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-700 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>
                </div>
            </div>

            <!-- Card: KYC Status -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                @if($stats['kyc_status'] === 'approved')
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-800 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                            ✓
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('KYC Status: Verified') }}</p>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ __('Your identity verification is complete. You have full access to marketplace features.') }}</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                            !
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">{{ __('KYC Verification Required') }}</p>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">{{ __('Complete your KYC verification to unlock full loan borrowing &amp; deposit features.') }}</p>
                            <a href="{{ route('kyc.index') }}" class="mt-2 inline-block text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ __('Verify Identity Now') }} &rarr;</a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card: Automate Repayments Banner -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs relative overflow-hidden">
                <div class="relative z-10 space-y-3">
                    <h4 class="text-sm font-bold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Automate Repayments') }}</h4>
                    <p class="text-xs text-slate-500 dark:text-slate-400 leading-relaxed">{{ __('Set up auto-pay and get a 0.25% rate reduction on your next eligible loan.') }}</p>
                    <a href="{{ route('loans.index') }}" class="inline-block rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-800 transition-all shadow-xs">
                        {{ __('Configure Auto-Pay') }}
                    </a>
                </div>
            </div>

        </div>

    </div>

    <!-- Repayment Confirmation Modal -->
    <div x-show="showRepayModal" 
         x-cloak 
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200">
        <div @click.away="showRepayModal = false" 
             class="w-full max-w-md bg-white dark:bg-slate-900 rounded-3xl p-6 shadow-2xl border border-slate-200 dark:border-slate-800 space-y-5 animate-in fade-in zoom-in duration-150">
            
            <!-- Header -->
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-700 dark:text-emerald-400 flex items-center justify-center font-bold text-base border border-emerald-200 dark:border-emerald-800">
                        💳
                    </div>
                    <div>
                        <h3 class="text-sm font-extrabold text-slate-900 dark:text-slate-100">{{ __('Payment Confirmation') }}</h3>
                        <p class="text-[11px] text-slate-400 font-medium" x-text="repayItem ? '{{ __('Installment') }} #' + repayItem.number + ' &bull; {{ __('Due') }}: ' + repayItem.due_date : ''"></p>
                    </div>
                </div>
                <button type="button" @click="showRepayModal = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 font-bold text-xl leading-none cursor-pointer">&times;</button>
            </div>

            <!-- Breakdown Card -->
            <div class="rounded-2xl bg-slate-50 dark:bg-slate-800/50 p-4 border border-slate-200/60 dark:border-slate-800 space-y-2.5 text-xs">
                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                    <span>{{ __('Principal Amount') }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="repayItem ? 'Rp ' + formatRupiah(repayItem.principal) : ''"></span>
                </div>
                <div class="flex justify-between text-slate-500 dark:text-slate-400">
                    <span>{{ __('Interest Amount') }}</span>
                    <span class="font-bold text-slate-800 dark:text-slate-200" x-text="repayItem ? 'Rp ' + formatRupiah(repayItem.interest) : ''"></span>
                </div>
                <template x-if="repayItem && repayItem.penalty > 0">
                    <div class="flex justify-between text-rose-600 dark:text-rose-400">
                        <span>{{ __('Late Penalty Fee') }}</span>
                        <span class="font-bold" x-text="'Rp ' + formatRupiah(repayItem.penalty)"></span>
                    </div>
                </template>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-700 flex justify-between items-baseline">
                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs">{{ __('Total Payment') }}</span>
                    <span class="text-lg font-black text-emerald-700 dark:text-emerald-400" x-text="repayItem ? 'Rp ' + formatRupiah(repayItem.total) : ''"></span>
                </div>
            </div>

            <!-- Wallet Balance Assessment -->
            <div class="rounded-2xl p-4 border text-xs space-y-2"
                 :class="repayItem && walletBalance >= repayItem.total ? 'bg-emerald-50/70 dark:bg-emerald-950/30 border-emerald-200 dark:border-emerald-800 text-emerald-900 dark:text-emerald-300' : 'bg-rose-50/70 dark:bg-rose-950/30 border-rose-200 dark:border-rose-800 text-rose-900 dark:text-rose-300'">
                <div class="flex justify-between items-center">
                    <span class="font-medium">{{ __('Current Wallet Balance') }}:</span>
                    <span class="font-extrabold" x-text="'Rp ' + formatRupiah(walletBalance)"></span>
                </div>
                <div class="flex justify-between items-center pt-1 border-t border-current/10">
                    <span class="font-medium">{{ __('Balance After Payment') }}:</span>
                    <span class="font-extrabold" x-text="repayItem ? 'Rp ' + formatRupiah(Math.max(0, walletBalance - repayItem.total)) : ''"></span>
                </div>
                <template x-if="repayItem && walletBalance < repayItem.total">
                    <div class="mt-2 pt-2 border-t border-rose-200 dark:border-rose-800/60 flex items-center justify-between gap-2">
                        <span class="text-[11px] font-bold text-rose-700 dark:text-rose-400">{{ __('Insufficient balance for this payment.') }}</span>
                        <a href="{{ route('wallet.index') }}" class="px-2.5 py-1 rounded-lg bg-rose-600 text-white font-bold text-[11px] hover:bg-rose-700 transition-all shrink-0">
                            {{ __('Deposit Funds') }} &rarr;
                        </a>
                    </div>
                </template>
            </div>

            <!-- Form Submit Actions -->
            <form :action="repayItem ? repayItem.pay_url : ''" method="POST" class="pt-2 flex items-center gap-3">
                @csrf
                <button type="button" 
                        @click="showRepayModal = false" 
                        class="flex-1 py-2.5 px-4 rounded-xl border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-300 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-800 transition-all cursor-pointer">
                    {{ __('Cancel') }}
                </button>
                <button type="submit" 
                        :disabled="!repayItem || walletBalance < repayItem.total"
                        :class="repayItem && walletBalance >= repayItem.total ? 'bg-emerald-700 hover:bg-emerald-800 text-white shadow-xs cursor-pointer' : 'bg-slate-300 dark:bg-slate-700 text-slate-500 dark:text-slate-400 cursor-not-allowed'"
                        class="flex-1 py-2.5 px-4 rounded-xl font-bold text-xs transition-all flex items-center justify-center gap-1.5">
                    <span>{{ __('Confirm & Pay Now') }}</span>
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5L21 12m0 0l-7.5 7.5M21 12H3"/></svg>
                </button>
            </form>

        </div>
    </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- LENDER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @elseif($role === 'lender')

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Lender Dashboard') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Welcome back, investor. Here\'s your high-level portfolio overview.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                <span></span> {{ __('Export Report') }}
            </button>
            <a href="{{ route('wallet.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                <span></span> {{ __('Deposit Funds') }}
            </a>
        </div>
    </div>

    <!-- 4 High-Density Stat Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: PORTFOLIO VALUE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('PORTFOLIO VALUE') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">{{ __('+4.2% vs last mo') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ number_format($stats['portfolio_value'], 0, ',', '.') }}
            </p>
        </div>

        <!-- Card 2: EXPECTED RETURN -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('EXPECTED RETURN') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">{{ __('On Track Ann. Yield') }}</span>
            </div>
            <p class="text-2xl font-extrabold text-emerald-700 mt-3">
                {{ $stats['expected_return_pct'] }}%
            </p>
        </div>

        <!-- Card 3: WALLET BALANCE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('WALLET BALANCE') }}</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900">Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}</p>
                <p class="text-[11px] text-slate-400 font-medium">{{ __('Available to invest') }}</p>
            </div>
        </div>

        <!-- Card 4: ACTIVE INVESTMENTS -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('ACTIVE INVESTMENTS') }}</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900">{{ $stats['active_investments'] }}</p>
                <p class="text-[11px] text-slate-400 font-medium">{{ __('Across 4 risk grades') }}</p>
            </div>
        </div>
    </div>

    <!-- Main Grid: 2/3 Left Column, 1/3 Right Column -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- ─── Left Column (2/3 Width) ─────────────────────────────────────── -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Card: Portfolio Growth (6 Months) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs relative">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center gap-3">
                        <h3 class="text-sm font-bold text-slate-900">{{ __('Portfolio Growth (6 Months)') }}</h3>
                        <span class="px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                            {{ __('Current Value:') }} Rp {{ number_format($stats['portfolio_value'], 0, ',', '.') }}
                        </span>
                    </div>
                    <div class="flex items-center gap-1">
                        <button class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-900 text-white">6M</button>
                        <button class="px-2.5 py-1 rounded-md text-xs font-semibold text-slate-500 hover:bg-slate-100">1Y</button>
                        <button class="px-2.5 py-1 rounded-md text-xs font-semibold text-slate-500 hover:bg-slate-100">ALL</button>
                    </div>
                </div>
                <div class="h-[240px]">
                    <canvas id="lenderGrowthChart"></canvas>
                </div>
            </div>

            <!-- Card: Recent Repayments Table -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Recent Repayments') }}</h3>
                    <a href="{{ route('marketplace.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">{{ __('View All') }} &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-6">{{ __('DATE') }}</th>
                                <th class="py-3 px-6">{{ __('LOAN ID') }}</th>
                                <th class="py-3 px-6">{{ __('AMOUNT') }}</th>
                                <th class="py-3 px-6">{{ __('PRINCIPAL') }}</th>
                                <th class="py-3 px-6">{{ __('INTEREST') }}</th>
                                <th class="py-3 px-6 text-right">{{ __('STATUS') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($stats['recent_repayments'] as $tx)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-6 font-semibold text-slate-900">{{ $tx->created_at->format('M d, Y') }}</td>
                                <td class="py-3.5 px-6 font-bold text-emerald-700">#LN-{{ substr($tx->reference_id ?? $tx->id, 0, 6) }}</td>
                                <td class="py-3.5 px-6 font-bold text-slate-900">Rp {{ number_format($tx->amount, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-slate-600">Rp {{ number_format($tx->amount * 0.8, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-emerald-700 font-semibold">+Rp {{ number_format($tx->amount * 0.2, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 text-right">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                        {{ __('COMPLETED') }}
                                    </span>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    <div class="flex flex-col items-center justify-center">
                                        <svg class="h-8 w-8 text-slate-300 mb-2" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.5"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <p class="text-xs font-bold text-slate-600 dark:text-slate-400">{{ __('No Recent Repayments') }}</p>
                                        <p class="text-[11px] text-slate-400 mt-0.5">{{ __('Repayments from borrowers will appear here once distributed.') }}</p>
                                    </div>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- ─── Right Column (1/3 Width) ────────────────────────────────────── -->
        <div class="space-y-6">

            <!-- Card: Risk Allocation Donut Chart -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">{{ __('Risk Allocation') }}</h3>
                </div>

                <!-- Donut Chart & Legend -->
                <div class="flex flex-col items-center">
                    <div class="relative w-44 h-44 flex items-center justify-center my-2">
                        <canvas id="lenderRiskAllocationDonut"></canvas>
                        <div class="absolute text-center">
                            <span class="text-2xl font-black text-slate-900 block leading-tight">{{ $stats['active_investments'] > 0 ? $stats['active_investments'] : 34 }}</span>
                            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Active Loans') }}</span>
                        </div>
                    </div>

                    <!-- 4 Risk Grade Percentages Legend Grid -->
                    <div class="w-full grid grid-cols-2 gap-3 mt-4">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-emerald-700"></span>
                                <span class="text-xs font-bold text-slate-700">{{ __('Grade A') }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-900">45%</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-blue-600"></span>
                                <span class="text-xs font-bold text-slate-700">{{ __('Grade B') }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-900">30%</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-amber-500"></span>
                                <span class="text-xs font-bold text-slate-700">{{ __('Grade C') }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-900">15%</span>
                        </div>
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <span class="h-2.5 w-2.5 rounded-full bg-rose-600"></span>
                                <span class="text-xs font-bold text-slate-700">{{ __('Grade D') }}</span>
                            </div>
                            <span class="text-xs font-black text-slate-900">10%</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Card: Auto-Invest Engine Config Panel -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs" x-data="{ editing: false }">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2">
                        <h3 class="text-sm font-bold text-slate-900">{{ __('Auto-Invest') }}</h3>
                    </div>
                    <!-- Active Toggle Switch -->
                    <form action="{{ route('loans.auto-invest.update') }}" method="POST" id="autoInvestToggleForm">
                        @csrf
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" 
                                   onchange="document.getElementById('autoInvestToggleForm').submit()"
                                   {{ $stats['auto_invest_rule']->is_active ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-700"></div>
                        </label>
                    </form>
                </div>

                <!-- Status Banner -->
                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 mb-4">
                    <p class="text-xs font-bold text-emerald-900">
                        {{ $stats['auto_invest_rule']->is_active ? __('Status: Active') : __('Status: Inactive') }}
                    </p>
                    <p class="text-[11px] text-emerald-700 mt-0.5 leading-relaxed">
                        {{ __('Your funds are automatically being deployed based on the rules below.') }}
                    </p>
                </div>

                <!-- Summary parameters -->
                <div class="space-y-2 text-xs font-medium text-slate-600 mb-4">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-400 font-semibold">{{ __('Max LTV') }}</span>
                        <span class="font-bold text-slate-900">{{ (int)$stats['auto_invest_rule']->max_ltv }}%</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-400 font-semibold">{{ __('Min Expected Return') }}</span>
                        <span class="font-bold text-slate-900">10.0%</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-400 font-semibold">{{ __('Target Grades') }}</span>
                        <span class="font-bold text-slate-900">{{ __('Grade') }} {{ $stats['auto_invest_rule']->min_grade }} - {{ $stats['auto_invest_rule']->max_grade }}</span>
                    </div>
                </div>

                <!-- Edit Rules Toggle Button -->
                <button @click="editing = !editing" class="w-full py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors">
                    <span x-text="editing ? '{{ __('Close Form') }}' : '{{ __('Edit Rules') }}'">{{ __('Edit Rules') }}</span>
                </button>

                <!-- Collapsible Form -->
                <form x-show="editing" action="{{ route('loans.auto-invest.update') }}" method="POST" class="mt-4 space-y-3 pt-3 border-t border-slate-100" style="display: none;">
                    @csrf
                    <input type="hidden" name="is_active" value="1">
                    
                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Min Risk Grade') }}</label>
                        <select name="min_grade" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
                            @foreach(['A', 'B', 'C', 'D'] as $g)
                                <option value="{{ $g }}" {{ $stats['auto_invest_rule']->min_grade === $g ? 'selected' : '' }}>{{ __('Grade') }} {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Max Risk Grade') }}</label>
                        <select name="max_grade" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
                            @foreach(['A', 'B', 'C', 'D'] as $g)
                                <option value="{{ $g }}" {{ $stats['auto_invest_rule']->max_grade === $g ? 'selected' : '' }}>{{ __('Grade') }} {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Max Allocation / Loan (IDR)') }}</label>
                        <input type="number" name="max_allocation_per_loan" min="100000" step="50000"
                            value="{{ (int)$stats['auto_invest_rule']->max_allocation_per_loan }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700">
                    </div>

                    <button type="submit" class="w-full py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        {{ __('Save Rules') }}
                    </button>
                </form>
            </div>

        </div>

    </div>

    @endif

</div>

<!-- Chart initialization script for Admin & Lender -->
@if($role === 'admin')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('adminPlatformChart')?.getContext('2d');
    if (ctx) {
        const gradient = ctx.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(21, 128, 61, 0.35)');
        gradient.addColorStop(1, 'rgba(21, 128, 61, 0.0)');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: @json($stats['monthly_volume_labels']),
                datasets: [{
                    label: 'Funding Volume (IDR)',
                    data: @json($stats['monthly_volume_data']),
                    borderColor: '#15803D',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#15803D',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: true,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                let formatted = new Intl.NumberFormat('en-US').format(value);
                                if ('{{ app()->getLocale() }}' === 'ar') {
                                    const arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                                    formatted = formatted.replace(/[0-9]/g, d => arabicDigits[d]);
                                }
                                return formatted;
                            }
                        }
                    }
                }
            }
        });
    }
});
</script>
@elseif($role === 'lender')
<script>
document.addEventListener('DOMContentLoaded', function () {
    // 1. Portfolio Growth Area Chart
    const ctxGrowth = document.getElementById('lenderGrowthChart')?.getContext('2d');
    if (ctxGrowth) {
        const gradient = ctxGrowth.createLinearGradient(0, 0, 0, 200);
        gradient.addColorStop(0, 'rgba(21, 128, 61, 0.35)');
        gradient.addColorStop(1, 'rgba(21, 128, 61, 0.0)');

        new Chart(ctxGrowth, {
            type: 'line',
            data: {
                labels: @json($stats['growth_chart_labels']),
                datasets: [{
                    label: 'Portfolio Value (IDR)',
                    data: @json($stats['growth_chart_data']),
                    borderColor: '#15803D',
                    borderWidth: 3,
                    backgroundColor: gradient,
                    fill: true,
                    tension: 0.4,
                    pointBackgroundColor: '#15803D',
                    pointRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        beginAtZero: false,
                        grid: { color: '#f1f5f9' },
                        ticks: {
                            callback: function(value) {
                                let formatted = new Intl.NumberFormat('en-US').format(value);
                                if ('{{ app()->getLocale() }}' === 'ar') {
                                    const arabicDigits = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
                                    formatted = formatted.replace(/[0-9]/g, d => arabicDigits[d]);
                                }
                                return formatted;
                            }
                        }
                    }
                }
            }
        });
    }

    // 2. Risk Allocation Donut Chart
    const ctxDonut = document.getElementById('lenderRiskAllocationDonut')?.getContext('2d');
    if (ctxDonut) {
        new Chart(ctxDonut, {
            type: 'doughnut',
            data: {
                labels: ['Grade A', 'Grade B', 'Grade C', 'Grade D'],
                datasets: [{
                    data: [45, 30, 15, 10],
                    backgroundColor: ['#15803D', '#2563EB', '#D97706', '#DC2626'],
                    borderWidth: 0,
                    cutout: '75%'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
@endif

@endsection

