@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- ─── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ __('Loan Applications Control Queue') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __("Review pending borrowers' applications to approve them into the marketplace, or trigger funding disbursements.") }}
            </p>
        </div>
        {{-- Stats Summary Pills --}}
        <div class="flex items-center gap-2 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 dark:bg-slate-500"></span>
                {{ $loans->total() }} {{ __('Total Applications') }}
            </span>
        </div>
    </div>

    {{-- ─── Table Card ───────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs dark:shadow-none">

        {{-- Table Scroll Wrapper --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">

                {{-- ─── Table Header ─────────────────────────────────────────── --}}
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60">
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Applicant') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Target Amount') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Collateral') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Funding Progress') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Status') }}
                        </th>
                        <th scope="col" class="py-3.5 pl-4 pr-6 text-right text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Action') }}
                        </th>
                    </tr>
                </thead>

                {{-- ─── Table Body ───────────────────────────────────────────── --}}
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($loans as $loan)
                        @php
                            $statusClass = match($loan->status) {
                                'pending'      => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                                'open_funding' => 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30',
                                'funded'       => 'bg-purple-500/15 text-purple-400 border-purple-500/30',
                                'active'       => 'bg-green-500/15 text-green-400 border-green-500/30',
                                'completed'    => 'bg-slate-500/15 text-slate-400 border-slate-500/30',
                                'rejected'     => 'bg-rose-500/15 text-rose-400 border-rose-500/30',
                                default        => 'bg-slate-500/15 text-slate-400 border-slate-500/30',
                            };
                            $gradeClass = match($loan->risk_grade ?? '') {
                                'A' => 'bg-emerald-500/15 text-emerald-400 border-emerald-500/30',
                                'B' => 'bg-blue-500/15 text-blue-400 border-blue-500/30',
                                'C' => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                                'D' => 'bg-rose-500/15 text-rose-400 border-rose-500/30',
                                default => 'bg-slate-500/15 text-slate-400 border-slate-500/30',
                            };
                            $currencyCode = $loan->collateralCurrency->code ?? '';
                            $collateralClass = match(strtoupper($currencyCode)) {
                                'ETH'  => 'bg-violet-500/15 text-violet-400 border-violet-500/30',
                                'BTC'  => 'bg-amber-500/15 text-amber-400 border-amber-500/30',
                                'USDT', 'USDC' => 'bg-teal-500/15 text-teal-400 border-teal-500/30',
                                'BNB'  => 'bg-yellow-500/15 text-yellow-400 border-yellow-500/30',
                                'SOL'  => 'bg-purple-500/15 text-purple-400 border-purple-500/30',
                                default => 'bg-indigo-500/15 text-indigo-400 border-indigo-500/30',
                            };
                            $fundedPct = min(100, $loan->funded_percentage);
                            $progressColor = $fundedPct >= 100 ? 'bg-emerald-500' : ($fundedPct >= 50 ? 'bg-indigo-500' : 'bg-indigo-400');
                            $initials = strtoupper(substr($loan->borrower->profile->full_name ?? $loan->borrower->email, 0, 2));
                        @endphp
                        <tr class="group hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors duration-150">

                            {{-- Applicant --}}
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 flex-shrink-0 rounded-xl bg-gradient-to-br from-slate-100 to-slate-200 dark:from-slate-700 dark:to-slate-800 flex items-center justify-center border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-600 dark:text-slate-300">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate max-w-[160px]">
                                            {{ $loan->borrower->profile->full_name ?? __('Applicant') }}
                                        </div>
                                        <div class="text-xs text-slate-400 dark:text-slate-500 truncate max-w-[160px]">
                                            {{ $loan->purpose }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Target Amount --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="text-sm font-bold text-slate-900 dark:text-slate-100">
                                    Rp {{ number_format($loan->amount, 0, ',', '.') }}
                                </div>
                                <div class="flex items-center gap-1.5 mt-0.5">
                                    <span class="text-xs text-slate-400 dark:text-slate-500">{{ $loan->interest_rate }}% APR</span>
                                    <span class="inline-flex items-center rounded-full border px-1.5 py-0 text-[10px] font-bold {{ $gradeClass }}">
                                        {{ $loan->risk_grade }}
                                    </span>
                                </div>
                            </td>

                            {{-- Collateral --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                @if($loan->isCryptoLoan())
                                    <span class="inline-flex items-center gap-1.5 rounded-lg border px-2.5 py-0.5 text-xs font-bold {{ $collateralClass }}">
                                        @if(strtoupper($currencyCode) === 'ETH')
                                            {{-- ETH Icon --}}
                                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 17.97L4.58 13.62 11.943 24l7.37-10.38-7.372 4.35h.003zM12.056 0L4.69 12.223l7.365 4.354 7.365-4.35L12.056 0z"/></svg>
                                        @elseif(strtoupper($currencyCode) === 'BTC')
                                            {{-- BTC Icon --}}
                                            <svg class="w-3 h-3 shrink-0" viewBox="0 0 24 24" fill="currentColor"><path d="M23.638 14.904c-1.602 6.43-8.113 10.34-14.542 8.736C2.67 22.05-1.244 15.525.362 9.105 1.962 2.67 8.475-1.243 14.9.358c6.43 1.605 10.342 8.115 8.738 14.548v-.002zm-6.35-4.613c.24-1.59-.974-2.45-2.64-3.03l.54-2.153-1.315-.33-.525 2.107c-.345-.087-.705-.167-1.064-.25l.526-2.127-1.32-.33-.54 2.165c-.285-.067-.565-.132-.84-.2l-1.815-.45-.35 1.407s.974.225.955.236c.535.136.63.486.615.766l-1.477 5.92c-.075.166-.24.406-.614.314.015.02-.96-.24-.96-.24l-.66 1.51 1.71.426.93.242-.54 2.19 1.32.327.54-2.17c.36.1.705.19 1.05.273l-.51 2.154 1.32.33.545-2.19c2.24.427 3.93.257 4.64-1.774.57-1.637-.03-2.58-1.217-3.196.854-.193 1.5-.76 1.68-1.93h.01zm-3.01 4.22c-.404 1.64-3.157.75-4.05.53l.72-2.9c.896.23 3.757.67 3.33 2.37zm.41-4.24c-.37 1.49-2.662.735-3.405.55l.654-2.64c.744.18 3.137.524 2.75 2.084v.006z"/></svg>
                                        @else
                                            {{-- Generic Crypto Icon --}}
                                            <svg class="w-3 h-3 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M20.25 6.375c0 8.485-7.5 11.625-7.5 11.625S5.25 14.86 5.25 6.375a7.5 7.5 0 0 1 15 0Z"/></svg>
                                        @endif
                                        {{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 rounded-lg bg-slate-500/10 border border-slate-500/20 px-2 py-0.5 text-xs font-medium text-slate-400 dark:text-slate-500">
                                        <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75m-3-7.036A11.959 11.959 0 0 1 3.598 6 11.99 11.99 0 0 0 3 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285Z"/></svg>
                                        {{ __('Unsecured') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Funding Progress --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-28 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $progressColor }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $fundedPct }}%"></div>
                                    </div>
                                    <span class="text-xs font-semibold {{ $fundedPct >= 100 ? 'text-emerald-500' : 'text-slate-600 dark:text-slate-300' }} tabular-nums">
                                        {{ number_format($fundedPct, 2) }}%
                                    </span>
                                </div>
                            </td>

                            {{-- Status Badge --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-[11px] font-bold uppercase tracking-wider {{ $statusClass }}">
                                    {{ __(str_replace('_', ' ', $loan->status)) }}
                                </span>
                            </td>

                            {{-- Action --}}
                            <td class="whitespace-nowrap py-4 pl-4 pr-6 text-right">
                                <a href="{{ route('admin.loans.show', $loan->id) }}"
                                   class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300 shadow-xs hover:bg-slate-50 dark:hover:bg-slate-700 hover:border-slate-300 dark:hover:border-slate-600 transition-all duration-150 group-hover:border-indigo-300 dark:group-hover:border-indigo-700 group-hover:text-indigo-600 dark:group-hover:text-indigo-400">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z"/>
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/>
                                    </svg>
                                    {{ __('Review') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-16 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-12 h-12 rounded-2xl bg-slate-100 dark:bg-slate-800 flex items-center justify-center">
                                        <svg class="w-6 h-6 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M9 12h3.75M9 15h3.75M9 18h3.75m3 .75H18a2.25 2.25 0 0 0 2.25-2.25V6.108c0-1.135-.845-2.098-1.976-2.192a48.424 48.424 0 0 0-1.123-.08m-5.801 0c-.065.21-.1.433-.1.664 0 .414.336.75.75.75h4.5a.75.75 0 0 0 .75-.75 2.25 2.25 0 0 0-.1-.664m-5.8 0A2.251 2.251 0 0 1 13.5 2.25H15c1.012 0 1.867.668 2.15 1.586m-5.8 0c-.376.023-.75.05-1.124.08C9.095 4.01 8.25 4.973 8.25 6.108V8.25m0 0H4.875c-.621 0-1.125.504-1.125 1.125v11.25c0 .621.504 1.125 1.125 1.125h9.75c.621 0 1.125-.504 1.125-1.125V9.375c0-.621-.504-1.125-1.125-1.125H8.25ZM6.75 12h.008v.008H6.75V12Zm0 3h.008v.008H6.75V15Zm0 3h.008v.008H6.75V18Z"/>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-medium text-slate-500 dark:text-slate-400">{{ __('No loan applications found.') }}</p>
                                    <p class="text-xs text-slate-400 dark:text-slate-500">{{ __('New applications will appear here when submitted.') }}</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- ─── Pagination Footer ──────────────────────────────────────────── --}}
        @if($loans->hasPages())
            <div class="border-t border-slate-100 dark:border-slate-800 px-6 py-4">
                {{ $loans->links() }}
            </div>
        @endif

    </div>

</div>
@endsection
