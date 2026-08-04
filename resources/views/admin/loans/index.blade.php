@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">{{ __('Loan Applications Control Queue') }}</h1>
            <p class="mt-2 text-sm text-slate-600 dark:text-slate-400">{{ __("Review pending borrowers' applications to approve them into the marketplace, or trigger funding disbursements.") }}</p>
        </div>
    </div>

    <!-- Table Container (No white shadow halo in dark mode) -->
    <div class="overflow-hidden shadow-xs dark:shadow-none rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900">
        <table class="min-w-full divide-y divide-slate-200 dark:divide-slate-800">
            <thead class="bg-slate-50/70 dark:bg-slate-950/60">
                <tr>
                    <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Applicant') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Target amount') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Collateral') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Funding Progress') }}</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-500 dark:text-slate-400">{{ __('Status') }}</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">
                        <span class="sr-only">{{ __('Actions') }}</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800 bg-white dark:bg-slate-900">
                @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/50 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-slate-800 font-semibold text-slate-700 dark:text-slate-200 flex items-center justify-center border border-slate-200 dark:border-slate-700">
                                    {{ strtoupper(substr($loan->borrower->profile->full_name ?? $loan->borrower->email, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-slate-900 dark:text-slate-100">{{ $loan->borrower->profile->full_name ?? __('Applicant') }}</div>
                                    <div class="text-xs text-slate-500 dark:text-slate-400">{{ $loan->purpose }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <div class="font-bold text-slate-900 dark:text-slate-100">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</div>
                            <div class="text-xs text-slate-500 dark:text-slate-400">{{ __n($loan->interest_rate) }}% APR (Grade {{ $loan->risk_grade }})</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                            @if($loan->isCryptoLoan())
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 dark:bg-indigo-950 px-2 py-0.5 text-xs font-semibold text-indigo-700 dark:text-indigo-300 border border-indigo-200 dark:border-indigo-800">
                                    {{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }}
                                </span>
                            @else
                                <span class="text-xs text-slate-400 dark:text-slate-500">{{ __('Unsecured / Fiat') }}</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-slate-500 dark:text-slate-400">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-600 dark:bg-indigo-500 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">{{ __n($loan->funded_percentage) }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold uppercase tracking-wider
                                @if($loan->status === 'pending') bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30
                                @elseif($loan->status === 'open_funding') bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30
                                @elseif($loan->status === 'active') bg-emerald-50 dark:bg-green-500/15 text-emerald-700 dark:text-green-400 border border-emerald-200 dark:border-green-500/30
                                @elseif($loan->status === 'completed') bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700
                                @else bg-rose-50 dark:bg-rose-500/15 text-rose-700 dark:text-rose-400 border border-rose-200 dark:border-rose-500/30 @endif">
                                {{ __(str_replace('_', ' ', $loan->status)) }}
                            </span>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-semibold">
                            <a href="{{ route('admin.loans.show', $loan->id) }}" class="text-indigo-600 dark:text-indigo-400 hover:text-indigo-900 dark:hover:text-indigo-300">
                                {{ __('Review Application') }}
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500">{{ __('No loan applications found.') }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($loans->hasPages())
        <div class="mt-6">
            {{ $loans->links() }}
        </div>
    @endif
</div>
@endsection
