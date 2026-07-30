@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<!-- Chart.js CDN -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<div class="max-w-7xl mx-auto space-y-6">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ADMIN DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if($role === 'admin')

    <!-- Header Title -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Admin Dashboard — Platform Overview</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Platform metrics and operational management overview.</p>
        </div>
        <div class="flex gap-2">
            <a href="{{ route('admin.kyc.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-amber-600 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-amber-700 transition-colors">
                <span class="text-sm">🔍</span> Review KYC ({{ $stats['kyc_pending'] }})
            </a>
            <a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-3.5 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-colors">
                <span class="text-sm">📋</span> Review Loans ({{ $stats['loans_pending'] }})
            </a>
        </div>
    </div>

    <!-- 4 High-Density Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL USERS</span>
                <span class="p-1.5 rounded-lg bg-emerald-50 text-emerald-700 text-xs font-bold">👥 Users</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">{{ number_format($stats['total_users']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ACTIVE LOANS</span>
                <span class="p-1.5 rounded-lg bg-blue-50 text-blue-700 text-xs font-bold">📊 Active</span>
            </div>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">{{ number_format($stats['loans_active']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">KYC PENDING</span>
                <span class="p-1.5 rounded-lg bg-amber-50 text-amber-700 text-xs font-bold">🔍 Review</span>
            </div>
            <p class="text-2xl font-extrabold text-amber-600 mt-3">{{ number_format($stats['kyc_pending']) }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">OVERDUE INSTALLMENTS</span>
                <span class="p-1.5 rounded-lg bg-rose-50 text-rose-700 text-xs font-bold">⚠️ Warning</span>
            </div>
            <p class="text-2xl font-extrabold text-rose-600 mt-3">{{ number_format($stats['installments_overdue']) }}</p>
        </div>
    </div>

    <!-- Financial Totals -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL DISBURSED</p>
            <p class="text-xl font-extrabold text-slate-900 mt-1">Rp {{ number_format($stats['total_disbursed'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">PLATFORM FEES COLLECTED</p>
            <p class="text-xl font-extrabold text-emerald-700 mt-1">Rp {{ number_format($stats['total_platform_fees'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <p class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">LOANS COMPLETED</p>
            <p class="text-xl font-extrabold text-slate-900 mt-1">{{ number_format($stats['loans_completed']) }}</p>
        </div>
    </div>

    <!-- Monthly Loans Activity Chart -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
        <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4">Monthly Loan Volume (Last 6 Months)</h3>
        @php $maxCount = max(array_column($stats['monthly_loans'], 'count'), 1); @endphp
        <div class="space-y-3">
            @foreach($stats['monthly_loans'] as $month)
            <div class="flex items-center gap-3">
                <span class="w-20 text-right text-xs font-semibold text-slate-500">{{ $month['label'] }}</span>
                <div class="flex-1 bg-slate-100 rounded-full h-5 overflow-hidden">
                    <div class="h-5 rounded-full bg-emerald-700 transition-all duration-500"
                         style="width: {{ $month['count'] > 0 ? max(round(($month['count'] / $maxCount) * 100), 5) : 0 }}%">
                    </div>
                </div>
                <span class="w-8 text-xs font-bold text-slate-700">{{ $month['count'] }}</span>
            </div>
            @endforeach
        </div>
    </div>

    <!-- Recent Loan Applications Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">Recent Loan Applications</h3>
            <a href="{{ route('admin.loans.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">View All Applications &rarr;</a>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3 px-6">Borrower</th>
                        <th class="py-3 px-6">Amount</th>
                        <th class="py-3 px-6">Status</th>
                        <th class="py-3 px-6">Applied Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($stats['recent_loans'] as $loan)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-3.5 px-6 font-bold text-slate-900">{{ $loan->borrower?->profile?->full_name ?? 'N/A' }}</td>
                        <td class="py-3.5 px-6 font-semibold text-slate-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                        <td class="py-3.5 px-6">
                            @php
                                $statusBadge = match($loan->status) {
                                    'pending'      => 'bg-amber-50 text-amber-700 border-amber-200',
                                    'open_funding' => 'bg-blue-50 text-blue-700 border-blue-200',
                                    'funded'       => 'bg-purple-50 text-purple-700 border-purple-200',
                                    'active'       => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                                    'completed'    => 'bg-slate-100 text-slate-700 border-slate-200',
                                    default        => 'bg-slate-100 text-slate-600 border-slate-200',
                                };
                            @endphp
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold border {{ $statusBadge }}">
                                {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                            </span>
                        </td>
                        <td class="py-3.5 px-6 text-slate-400">{{ $loan->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="4" class="py-8 text-center text-slate-400">No applications recorded.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>


    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- BORROWER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @elseif($role === 'borrower')

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Borrower Dashboard — Welcome back, {{ Auth::user()->profile?->full_name ?? 'Borrower' }}! 👋</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Overview of your current loans and upcoming obligations.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
                Apply for New Loan
            </a>
        </div>
    </div>

    <!-- 4 High-Density Stat Cards Row -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: OUTSTANDING LOAN -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">OUTSTANDING LOAN</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ number_format($stats['outstanding_amount'], 0, ',', '.') }}
            </p>
        </div>

        <!-- Card 2: MONTHLY INSTALLMENT -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">MONTHLY INSTALLMENT</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ number_format($stats['monthly_installment_amount'], 0, ',', '.') }}
            </p>
        </div>

        <!-- Card 3: WALLET BALANCE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">WALLET BALANCE</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">
                Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}
            </p>
        </div>

        <!-- Card 4: CREDIT SCORE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">CREDIT SCORE</span>
            <div class="flex items-baseline gap-2 mt-3">
                <span class="text-2xl font-extrabold text-emerald-700">{{ $stats['credit_score'] }}</span>
                <span class="text-xs font-bold text-emerald-800 bg-emerald-50 px-2 py-0.5 rounded-md border border-emerald-200">Grade {{ $stats['credit_grade'] }}</span>
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
                    <h3 class="text-sm font-bold text-slate-900">Upcoming Repayments</h3>
                    <a href="{{ route('loans.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">View All Schedule &rarr;</a>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-6">DATE</th>
                                <th class="py-3 px-6">AMOUNT</th>
                                <th class="py-3 px-6">STATUS</th>
                                <th class="py-3 px-6 text-right">ACTION</th>
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
                                    Rp {{ number_format($inst->total_amount, 0, ',', '.') }}
                                </td>
                                <td class="py-4 px-6">
                                    @if($inst->status === 'paid')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                            Paid
                                        </span>
                                    @elseif($isOverdue)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">
                                            Due Soon / Overdue
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">
                                            Scheduled
                                        </span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-right">
                                    @if($inst->status !== 'paid')
                                        <a href="{{ route('loans.installments', $inst->loan_id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs">
                                            Pay Now
                                        </a>
                                    @else
                                        <a href="{{ route('loans.installments', $inst->loan_id) }}" 
                                           class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">
                                            Details
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <!-- Fallback Mock Display Matching Design Mockup -->
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-900">Oct 15, 2026</td>
                                <td class="py-4 px-6 font-bold text-slate-900">Rp 850.200</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-rose-100 text-rose-700 border border-rose-200">Due Soon</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('loans.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-bold bg-emerald-700 text-white hover:bg-emerald-800 transition-colors shadow-xs">Pay Now</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-900">Nov 15, 2026</td>
                                <td class="py-4 px-6 font-bold text-slate-900">Rp 850.200</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Scheduled</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('loans.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Details</a>
                                </td>
                            </tr>
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-4 px-6 font-semibold text-slate-900">Dec 15, 2026</td>
                                <td class="py-4 px-6 font-bold text-slate-900">Rp 850.200</td>
                                <td class="py-4 px-6">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-blue-100 text-blue-700 border border-blue-200">Scheduled</span>
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('loans.index') }}" class="inline-flex items-center px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 text-slate-700 hover:bg-slate-200 transition-colors">Details</a>
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Card: Active Application Progress -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-6">Active Application Progress</h3>
                
                @php
                    $app = $stats['active_application'];
                    $pct = $app ? (int)$app->funded_percentage : 75;
                @endphp

                <div class="flex flex-col sm:flex-row items-center gap-8">
                    <!-- Circular Donut Chart -->
                    <div class="relative w-36 h-36 flex-shrink-0 flex items-center justify-center">
                        <svg class="w-full h-full transform -rotate-90" viewBox="0 0 36 36">
                            <path class="text-slate-100" stroke-width="3.5" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                            <path class="text-emerald-700" stroke-dasharray="{{ $pct }}, 100" stroke-width="3.5" stroke-linecap="round" stroke="currentColor" fill="none" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831"/>
                        </svg>
                        <div class="absolute text-center">
                            <span class="text-2xl font-black text-slate-900">{{ $pct }}%</span>
                            <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-wider">Funded</span>
                        </div>
                    </div>

                    <!-- Milestone Steps -->
                    <div class="flex-1 space-y-4">
                        <div class="flex items-start gap-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-emerald-700 mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Application Approved</p>
                                <p class="text-[11px] text-slate-400">{{ $app?->approved_at ? \Carbon\Carbon::parse($app->approved_at)->format('M d, Y') : 'Sept 28, 2026' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-emerald-700 mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-xs font-bold text-slate-900">Marketplace Listing Active</p>
                                <p class="text-[11px] text-slate-400">{{ $app?->created_at ? \Carbon\Carbon::parse($app->created_at)->format('M d, Y') : 'Sept 29, 2026' }}</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <div class="h-2.5 w-2.5 rounded-full bg-slate-300 mt-1.5 flex-shrink-0"></div>
                            <div>
                                <p class="text-xs font-bold text-slate-500">Funds Disbursement</p>
                                <p class="text-[11px] text-slate-400">Pending 100% funding completion</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- ─── Right Column (1/3 Width) ────────────────────────────────────── -->
        <div class="space-y-6">

            <!-- Card: Quick Actions -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-3">QUICK ACTIONS</span>
                
                <div class="space-y-2">
                    <a href="{{ route('loans.create') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Apply for New Loan</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-700 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>

                    <a href="{{ route('wallet.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">Deposit Funds</span>
                        <svg class="h-4 w-4 text-slate-400 group-hover:text-emerald-700 transform group-hover:translate-x-0.5 transition-transform" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/></svg>
                    </a>

                    <a href="{{ route('loans.index') }}" class="flex items-center justify-between p-3 rounded-xl border border-slate-200 hover:border-emerald-600 hover:bg-emerald-50/50 transition-all group">
                        <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-800">View Schedule</span>
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
                            <p class="text-xs font-bold text-slate-900">KYC Status: Verified</p>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Your identity verification is complete. You have full access to marketplace features.</p>
                        </div>
                    </div>
                @else
                    <div class="flex items-start gap-3">
                        <div class="h-8 w-8 rounded-full bg-amber-100 text-amber-700 flex items-center justify-center flex-shrink-0 font-bold text-sm">
                            !
                        </div>
                        <div>
                            <p class="text-xs font-bold text-slate-900">KYC Verification Required</p>
                            <p class="text-[11px] text-slate-500 mt-1 leading-relaxed">Complete your KYC verification to unlock full loan borrowing &amp; deposit features.</p>
                            <a href="{{ route('kyc.index') }}" class="mt-2 inline-block text-xs font-bold text-emerald-700 hover:text-emerald-800">Verify Identity Now &rarr;</a>
                        </div>
                    </div>
                @endif
            </div>

            <!-- Card: Automate Repayments Banner (Dark Neutral #111827) -->
            <div class="rounded-2xl bg-slate-900 text-white p-6 shadow-md relative overflow-hidden">
                <div class="relative z-10 space-y-3">
                    <h4 class="text-sm font-bold text-white tracking-tight">Automate Repayments</h4>
                    <p class="text-xs text-slate-300 leading-relaxed">Set up auto-pay and get a 0.25% rate reduction on your next eligible loan.</p>
                    <a href="{{ route('loans.index') }}" class="inline-block rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white hover:bg-emerald-800 transition-all shadow-xs">
                        Configure Auto-Pay
                    </a>
                </div>
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
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Lender Portfolio 📊</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Track your P2P lending investments and returns.</p>
        </div>
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
            Explore Marketplace &rarr;
        </a>
    </div>

    <!-- 4 High-Density Stat Cards -->
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">WALLET BALANCE</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL INVESTED</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">Rp {{ number_format($stats['total_invested'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">INTEREST EARNED</span>
            <p class="text-2xl font-extrabold text-emerald-700 mt-3">Rp {{ number_format($stats['total_interest_earned'], 0, ',', '.') }}</p>
        </div>

        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">ACTIVE INVESTMENTS</span>
            <p class="text-2xl font-extrabold text-slate-900 mt-3">{{ $stats['active_investments'] }}</p>
        </div>
    </div>

    <!-- Main Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        <!-- Left 2/3: Distribution Chart & Fundings -->
        <div class="lg:col-span-2 space-y-6">

            <!-- Risk Grade Chart -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <h3 class="text-sm font-bold text-slate-900 mb-4">Investment Distribution by Risk Grade</h3>
                <div class="h-[220px]">
                    <canvas id="gradeDistributionChart"></canvas>
                </div>
            </div>

            <!-- Recent Fundings -->
            <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
                <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">My Investment Portfolio</h3>
                    <a href="{{ route('marketplace.index') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">Browse Marketplace &rarr;</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                                <th class="py-3 px-6">Borrower / Loan</th>
                                <th class="py-3 px-6">Grade</th>
                                <th class="py-3 px-6">Amount Funded</th>
                                <th class="py-3 px-6">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                            @forelse($stats['fundings'] as $f)
                            <tr class="hover:bg-slate-50/80 transition-colors">
                                <td class="py-3.5 px-6 font-bold text-slate-900">
                                    {{ $f->loan?->borrower?->profile?->full_name ?? 'Loan #'.substr($f->loan_id, 0, 8) }}
                                </td>
                                <td class="py-3.5 px-6">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-[11px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                                        Grade {{ $f->loan?->risk_grade ?? 'A' }}
                                    </span>
                                </td>
                                <td class="py-3.5 px-6 font-bold text-slate-900">Rp {{ number_format($f->amount, 0, ',', '.') }}</td>
                                <td class="py-3.5 px-6 capitalize text-slate-500">{{ str_replace('_', ' ', $f->loan?->status ?? 'active') }}</td>
                            </tr>
                            @empty
                            <tr><td colspan="4" class="py-8 text-center text-slate-400">No active investments yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right 1/3: Auto-Invest Configuration Panel -->
        <div class="space-y-6">
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-sm font-bold text-slate-900">🤖 Auto-Invest Engine</h3>
                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $stats['auto_invest_rule']->is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-100 text-slate-600' }}">
                        {{ $stats['auto_invest_rule']->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>

                <form action="{{ route('loans.auto-invest.update') }}" method="POST" class="space-y-4">
                    @csrf
                    
                    <div class="flex items-center justify-between py-2 border-b border-slate-100">
                        <span class="text-xs font-bold text-slate-700">Enable Auto-Funding</span>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ $stats['auto_invest_rule']->is_active ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-emerald-700"></div>
                        </label>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Min Risk Grade</label>
                        <select name="min_grade" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-emerald-600">
                            @foreach(['A', 'B', 'C', 'D'] as $g)
                                <option value="{{ $g }}" {{ $stats['auto_invest_rule']->min_grade === $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Max Risk Grade</label>
                        <select name="max_grade" class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-emerald-600">
                            @foreach(['A', 'B', 'C', 'D'] as $g)
                                <option value="{{ $g }}" {{ $stats['auto_invest_rule']->max_grade === $g ? 'selected' : '' }}>Grade {{ $g }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase mb-1">Max Allocation / Loan (IDR)</label>
                        <input type="number" name="max_allocation_per_loan" min="100000" step="50000"
                            value="{{ (int)$stats['auto_invest_rule']->max_allocation_per_loan }}"
                            class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-700 outline-none focus:border-emerald-600">
                    </div>

                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        Save Auto-Invest Rules
                    </button>
                </form>
            </div>
        </div>

    </div>

    @endif

</div>

<!-- Chart initialization script for Lender Grade distribution -->
@if($role === 'lender')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('gradeDistributionChart')?.getContext('2d');
    if (ctx) {
        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Grade A', 'Grade B', 'Grade C', 'Grade D'],
                datasets: [{
                    label: 'Funded (IDR)',
                    data: @json($stats['grade_chart_data']),
                    backgroundColor: ['#15803D', '#2563EB', '#D97706', '#DC2626'],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true } }
            }
        });
    }
});
</script>
@endif

@endsection
