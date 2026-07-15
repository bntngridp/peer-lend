@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="mx-auto max-w-7xl px-4 sm:px-6 lg:px-8 py-10">

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- ADMIN DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @if($role === 'admin')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Platform Overview</h1>
        <p class="mt-1 text-sm text-gray-500">Real-time metrics for the Peer-Lend platform.</p>
    </div>

    {{-- Stat Cards Row 1 --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-6">
        @foreach([
            ['label' => 'Total Users',        'value' => number_format($stats['total_users']),        'icon' => '👥', 'color' => 'indigo'],
            ['label' => 'Active Loans',        'value' => number_format($stats['loans_active']),       'icon' => '📊', 'color' => 'blue'],
            ['label' => 'KYC Pending',         'value' => number_format($stats['kyc_pending']),        'icon' => '🔍', 'color' => 'amber'],
            ['label' => 'Overdue Installments','value' => number_format($stats['installments_overdue']),'icon' => '⚠️', 'color' => 'rose'],
        ] as $card)
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between mb-3">
                <span class="text-2xl">{{ $card['icon'] }}</span>
                <span class="text-xs font-semibold uppercase tracking-wider text-{{ $card['color'] }}-600 bg-{{ $card['color'] }}-50 px-2 py-0.5 rounded-lg">
                    {{ $card['label'] }}
                </span>
            </div>
            <p class="text-3xl font-bold text-gray-900">{{ $card['value'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- Stat Cards Row 2 --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-indigo-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Disbursed</p>
            <p class="mt-1 text-2xl font-bold text-indigo-700">Rp {{ number_format($stats['total_disbursed'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Platform Fees Collected</p>
            <p class="mt-1 text-2xl font-bold text-emerald-700">Rp {{ number_format($stats['total_platform_fees'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-purple-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Loans Completed</p>
            <p class="mt-1 text-2xl font-bold text-purple-700">{{ number_format($stats['loans_completed']) }}</p>
        </div>
    </div>

    {{-- Monthly Loan Chart (text-based for simplicity) --}}
    <div class="mb-8 rounded-2xl border border-gray-200 bg-white p-6 shadow-sm">
        <h2 class="text-lg font-semibold text-gray-900 mb-4">New Loans — Last 6 Months</h2>
        @php $maxCount = max(array_column($stats['monthly_loans'], 'count'), 1); @endphp
        <div class="space-y-3">
            @foreach($stats['monthly_loans'] as $month)
            <div class="flex items-center gap-3">
                <span class="w-20 text-right text-xs font-medium text-gray-500 flex-shrink-0">{{ $month['label'] }}</span>
                <div class="flex-1 bg-gray-100 rounded-full h-6 overflow-hidden">
                    <div class="h-6 rounded-full bg-gradient-to-r from-indigo-500 to-indigo-400 flex items-center justify-end pr-2 transition-all duration-500"
                         style="width: {{ $month['count'] > 0 ? max(round(($month['count'] / $maxCount) * 100), 4) : 0 }}%">
                        @if($month['count'] > 0)
                        <span class="text-xs font-semibold text-white">{{ $month['count'] }}</span>
                        @endif
                    </div>
                </div>
                @if($month['count'] == 0)
                    <span class="text-xs text-gray-400">0</span>
                @endif
            </div>
            @endforeach
        </div>
    </div>

    {{-- Recent Loans --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Loan Applications</h2>
            <a href="{{ route('admin.loans.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800 transition-colors">View all →</a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-100">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Borrower</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Amount</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Status</th>
                        <th class="px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-gray-500">Applied</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 bg-white">
                    @forelse($stats['recent_loans'] as $loan)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="px-6 py-4">
                            <span class="text-sm font-medium text-gray-900">{{ $loan->borrower?->profile?->full_name ?? 'N/A' }}</span>
                        </td>
                        <td class="px-6 py-4 text-sm font-semibold text-gray-900">
                            Rp {{ number_format($loan->amount, 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4">
                            @php
                                $statusColors = [
                                    'pending'      => 'bg-amber-100 text-amber-700',
                                    'open_funding' => 'bg-blue-100 text-blue-700',
                                    'funded'       => 'bg-purple-100 text-purple-700',
                                    'active'       => 'bg-emerald-100 text-emerald-700',
                                    'completed'    => 'bg-gray-100 text-gray-600',
                                    'liquidated'   => 'bg-red-100 text-red-700',
                                ];
                                $colorClass = $statusColors[$loan->status] ?? 'bg-gray-100 text-gray-600';
                            @endphp
                            <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-semibold {{ $colorClass }}">
                                {{ ucfirst(str_replace('_', ' ', $loan->status)) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 text-sm text-gray-500">{{ $loan->created_at->diffForHumans() }}</td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-6 py-10 text-center text-sm text-gray-400">No loan applications yet.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Quick Admin Actions --}}
    <div class="mt-6 grid grid-cols-2 gap-4 sm:grid-cols-4">
        <a href="{{ route('admin.kyc.index') }}"
           class="flex items-center gap-3 rounded-2xl border border-amber-200 bg-amber-50 p-4 hover:bg-amber-100 transition-colors">
            <span class="text-2xl">🔍</span>
            <div>
                <p class="text-sm font-semibold text-amber-900">Review KYC</p>
                <p class="text-xs text-amber-600">{{ $stats['kyc_pending'] }} pending</p>
            </div>
        </a>
        <a href="{{ route('admin.loans.index') }}"
           class="flex items-center gap-3 rounded-2xl border border-blue-200 bg-blue-50 p-4 hover:bg-blue-100 transition-colors">
            <span class="text-2xl">📋</span>
            <div>
                <p class="text-sm font-semibold text-blue-900">Review Loans</p>
                <p class="text-xs text-blue-600">{{ $stats['loans_pending'] }} pending</p>
            </div>
        </a>
        <a href="{{ route('2fa.setup') }}"
           class="flex items-center gap-3 rounded-2xl border border-indigo-200 bg-indigo-50 p-4 hover:bg-indigo-100 transition-colors">
            <span class="text-2xl">🔐</span>
            <div>
                <p class="text-sm font-semibold text-indigo-900">2FA Setup</p>
                <p class="text-xs text-indigo-600">Secure your account</p>
            </div>
        </a>
        <a href="{{ route('notifications.index') }}"
           class="flex items-center gap-3 rounded-2xl border border-rose-200 bg-rose-50 p-4 hover:bg-rose-100 transition-colors">
            <span class="text-2xl">🔔</span>
            <div>
                <p class="text-sm font-semibold text-rose-900">Notifications</p>
                <p class="text-xs text-rose-600">Platform alerts</p>
            </div>
        </a>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- BORROWER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @elseif($role === 'borrower')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Welcome back, {{ Auth::user()->profile?->full_name ?? 'Borrower' }}! 👋</h1>
        <p class="mt-1 text-sm text-gray-500">Here's a summary of your borrowing activity.</p>
    </div>

    {{-- KYC Banner --}}
    @if($stats['kyc_status'] !== 'approved')
    <div class="mb-6 flex items-start gap-4 rounded-2xl border border-amber-200 bg-amber-50/70 p-5">
        <span class="text-2xl">⚠️</span>
        <div class="flex-1">
            <p class="font-semibold text-amber-900">KYC Verification Required</p>
            <p class="text-sm text-amber-700 mt-0.5">Complete your identity verification to apply for loans and fund investments.</p>
        </div>
        <a href="{{ route('kyc.index') }}" class="flex-shrink-0 rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white hover:bg-amber-700 transition-colors">
            Verify Now →
        </a>
    </div>
    @endif

    {{-- Stats Cards --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-3 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Wallet Balance</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}</p>
            <a href="{{ route('wallet.index') }}" class="mt-2 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage wallet →</a>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Active Loans</p>
            <p class="mt-1 text-2xl font-bold text-blue-700">{{ $stats['active_loans_count'] }}</p>
            <a href="{{ route('loans.index') }}" class="mt-2 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800">View all loans →</a>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Borrowed</p>
            <p class="mt-1 text-2xl font-bold text-gray-900">Rp {{ number_format($stats['total_borrowed'], 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Next Installment Due --}}
    @if($stats['next_installment'])
    @php $inst = $stats['next_installment']; $overdue = now()->gt($inst->due_date); @endphp
    <div class="mb-8 rounded-2xl border {{ $overdue ? 'border-red-200 bg-red-50' : 'border-indigo-200 bg-indigo-50' }} p-5">
        <div class="flex items-center justify-between">
            <div>
                <p class="text-sm font-medium {{ $overdue ? 'text-red-700' : 'text-indigo-700' }}">
                    {{ $overdue ? '🚨 Overdue Installment' : '⏰ Next Installment Due' }}
                </p>
                <p class="mt-1 text-2xl font-bold {{ $overdue ? 'text-red-900' : 'text-indigo-900' }}">
                    Rp {{ number_format($inst->total_amount, 0, ',', '.') }}
                </p>
                <p class="text-sm {{ $overdue ? 'text-red-600' : 'text-indigo-600' }}">
                    Due {{ \Carbon\Carbon::parse($inst->due_date)->format('d M Y') }}
                    ({{ \Carbon\Carbon::parse($inst->due_date)->diffForHumans() }})
                </p>
            </div>
            <a href="{{ route('loans.installments', $inst->loan_id) }}"
               class="rounded-xl {{ $overdue ? 'bg-red-600 hover:bg-red-700' : 'bg-indigo-600 hover:bg-indigo-700' }} px-5 py-2.5 text-sm font-semibold text-white transition-colors shadow-md">
                Pay Now
            </a>
        </div>
    </div>
    @endif

    {{-- Active Loans Table --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Active Loans</h2>
            <a href="{{ route('loans.create') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-600/10">
                + Apply for Loan
            </a>
        </div>
        @forelse($stats['active_loans'] as $loan)
        <div class="flex items-center justify-between border-b border-gray-50 last:border-b-0 px-6 py-4 hover:bg-gray-50 transition-colors">
            <div>
                <p class="text-sm font-semibold text-gray-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</p>
                <p class="text-xs text-gray-500">{{ $loan->duration }} months · {{ $loan->interest_rate }}% p.a.</p>
            </div>
            @php
                $paid = $loan->installments->where('status', 'paid')->count();
                $total = $loan->installments->count();
                $pct = $total > 0 ? round(($paid / $total) * 100) : 0;
            @endphp
            <div class="flex items-center gap-4">
                <div class="text-right">
                    <p class="text-xs font-medium text-gray-500">Progress</p>
                    <p class="text-sm font-bold text-emerald-700">{{ $paid }}/{{ $total }} paid</p>
                </div>
                <div class="w-24">
                    <div class="h-2 w-full rounded-full bg-gray-100">
                        <div class="h-2 rounded-full bg-emerald-500 transition-all" style="width: {{ $pct }}%"></div>
                    </div>
                    <p class="mt-0.5 text-right text-xs text-gray-400">{{ $pct }}%</p>
                </div>
                <a href="{{ route('loans.installments', $loan) }}"
                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    Details
                </a>
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-sm text-gray-400">
            No active loans. <a href="{{ route('loans.create') }}" class="text-indigo-600 font-medium">Apply for your first loan →</a>
        </div>
        @endforelse
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    {{-- LENDER DASHBOARD --}}
    {{-- ═══════════════════════════════════════════════════════════════════ --}}
    @elseif($role === 'lender')

    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Investment Portfolio 📊</h1>
        <p class="mt-1 text-sm text-gray-500">Track your P2P lending investments and returns.</p>
    </div>

    {{-- Stats Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-4 mb-8">
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Wallet Balance</p>
            <p class="mt-1 text-xl font-bold text-gray-900">Rp {{ number_format($stats['wallet_balance'], 0, ',', '.') }}</p>
            <a href="{{ route('wallet.index') }}" class="mt-1 inline-block text-xs font-medium text-indigo-600 hover:text-indigo-800">Manage →</a>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Total Invested</p>
            <p class="mt-1 text-xl font-bold text-blue-700">Rp {{ number_format($stats['total_invested'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-gradient-to-br from-emerald-50 to-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Interest Earned</p>
            <p class="mt-1 text-xl font-bold text-emerald-700">Rp {{ number_format($stats['total_interest_earned'], 0, ',', '.') }}</p>
        </div>
        <div class="rounded-2xl border border-gray-200 bg-white p-5 shadow-sm">
            <p class="text-sm font-medium text-gray-500">Active Investments</p>
            <p class="mt-1 text-xl font-bold text-purple-700">{{ $stats['active_investments'] }}</p>
            <p class="text-xs text-gray-400">{{ $stats['completed_investments'] }} completed</p>
        </div>
    </div>

    {{-- Investment List --}}
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden mb-6">
        <div class="flex items-center justify-between border-b border-gray-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Your Investments</h2>
            <a href="{{ route('marketplace.index') }}" class="rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-600/10">
                Browse Marketplace
            </a>
        </div>
        @forelse($stats['fundings'] as $funding)
        @php
            $statusColors = [
                'open_funding' => 'bg-blue-100 text-blue-700',
                'funded'       => 'bg-purple-100 text-purple-700',
                'active'       => 'bg-emerald-100 text-emerald-700',
                'completed'    => 'bg-gray-100 text-gray-600',
                'liquidated'   => 'bg-red-100 text-red-700',
            ];
            $status = $funding->loan?->status ?? 'unknown';
            $colorClass = $statusColors[$status] ?? 'bg-gray-100 text-gray-600';
        @endphp
        <div class="flex items-center justify-between border-b border-gray-50 last:border-b-0 px-6 py-4 hover:bg-gray-50 transition-colors">
            <div>
                <p class="text-sm font-semibold text-gray-900">
                    {{ $funding->loan?->borrower?->profile?->full_name ?? 'Unknown' }}
                </p>
                <p class="text-xs text-gray-500">
                    Rp {{ number_format($funding->amount, 0, ',', '.') }} funded
                    · {{ $funding->loan?->interest_rate }}% p.a.
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="inline-flex rounded-lg px-2.5 py-0.5 text-xs font-semibold {{ $colorClass }}">
                    {{ ucfirst(str_replace('_', ' ', $status)) }}
                </span>
                @if($funding->loan)
                <a href="{{ route('marketplace.show', $funding->loan) }}"
                   class="rounded-lg border border-gray-200 px-3 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-100 transition-colors">
                    View
                </a>
                @endif
            </div>
        </div>
        @empty
        <div class="px-6 py-10 text-center text-sm text-gray-400">
            No investments yet. <a href="{{ route('marketplace.index') }}" class="text-indigo-600 font-medium">Browse the marketplace →</a>
        </div>
        @endforelse
    </div>

    @endif

    {{-- Recent Transactions (shown to both borrower and lender) --}}
    @if(in_array($role, ['borrower', 'lender']) && $stats['recent_transactions']->isNotEmpty())
    <div class="rounded-2xl border border-gray-200 bg-white shadow-sm overflow-hidden">
        <div class="border-b border-gray-100 px-6 py-4">
            <h2 class="text-lg font-semibold text-gray-900">Recent Wallet Transactions</h2>
        </div>
        @foreach($stats['recent_transactions'] as $tx)
        <div class="flex items-center justify-between border-b border-gray-50 last:border-b-0 px-6 py-3">
            <div>
                <p class="text-sm font-medium text-gray-900 capitalize">{{ str_replace('_', ' ', $tx->type) }}</p>
                <p class="text-xs text-gray-400">{{ $tx->created_at->format('d M Y, H:i') }}</p>
            </div>
            <p class="text-sm font-semibold {{ in_array($tx->type, ['deposit', 'repayment_principal', 'repayment_interest', 'liquidation_recovery']) ? 'text-emerald-600' : 'text-rose-600' }}">
                {{ in_array($tx->type, ['deposit', 'repayment_principal', 'repayment_interest', 'liquidation_recovery']) ? '+' : '-' }}
                Rp {{ number_format($tx->amount, 0, ',', '.') }}
            </p>
        </div>
        @endforeach
        <div class="px-6 py-3">
            <a href="{{ route('wallet.index') }}" class="text-sm font-medium text-indigo-600 hover:text-indigo-800">View full history →</a>
        </div>
    </div>
    @endif

</div>
@endsection
