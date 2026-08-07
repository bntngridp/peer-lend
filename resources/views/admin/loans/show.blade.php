@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    <!-- Navigation Back Link -->
    <div class="mb-4">
        <a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:text-indigo-800 dark:hover:text-indigo-300">
            &larr; {{ __('Back to queue') }}
        </a>
    </div>

    <!-- Loan details card -->
    <div class="overflow-hidden shadow-xs dark:shadow-none rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 mb-6">
        <div class="px-6 py-6 sm:px-8 border-b border-slate-100 dark:border-slate-800 bg-slate-50/70 dark:bg-slate-950/60">
            <h2 class="text-xl font-bold text-slate-900 dark:text-slate-100">{{ __('Review Loan Application') }}</h2>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">{{ __('Applicant:') }} <strong class="text-slate-900 dark:text-slate-200">{{ $loan->borrower->profile->full_name }}</strong> ({{ $loan->borrower->email }})</p>
        </div>

        <div class="px-6 py-6 sm:px-8 space-y-6">
            <!-- Core Details Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Target Amount') }}</span>
                    <span class="text-base font-bold text-slate-950 dark:text-slate-100">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Annual Return (APR)') }}</span>
                    <span class="text-base font-bold text-emerald-600 dark:text-emerald-400">{{ __n($loan->interest_rate) }}%</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Duration Tenor') }}</span>
                    <span class="text-base font-bold text-slate-950 dark:text-slate-100">{{ __n($loan->duration) }} {{ __('Months') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Current Status') }}</span>
                    <span class="inline-flex items-center rounded-lg px-2.5 py-0.5 text-xs font-bold mt-1 uppercase tracking-wider
                        @if($loan->status === 'pending') bg-amber-50 dark:bg-amber-500/15 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/30
                        @elseif($loan->status === 'open_funding') bg-indigo-50 dark:bg-indigo-500/15 text-indigo-700 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-500/30
                        @elseif($loan->status === 'active') bg-emerald-50 dark:bg-green-500/15 text-emerald-700 dark:text-green-400 border border-emerald-200 dark:border-green-500/30
                        @else bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700 @endif">
                        {{ __(str_replace('_', ' ', $loan->status)) }}
                    </span>
                </div>
            </div>

            <!-- Borrower Info -->
            <div class="border-t border-slate-100 dark:border-slate-800 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Purpose') }}</span>
                    <span class="text-sm font-semibold text-slate-900 dark:text-slate-100">{{ $loan->purpose }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Monthly Income') }}</span>
                    <span class="text-sm font-semibold text-indigo-600 dark:text-indigo-400">Rp {{ __n(number_format($loan->borrower->profile->monthly_income ?? 0, 0, ',', '.')) }}</span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500">{{ __('Detailed Description') }}</span>
                <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed mt-1.5 whitespace-pre-line">{{ $loan->description ?: __('No detailed description.') }}</p>
            </div>

            <!-- Collateral Info if crypto -->
            @if($loan->isCryptoLoan())
                <div class="rounded-xl border border-indigo-100 dark:border-indigo-900/60 bg-indigo-50/20 dark:bg-indigo-950/40 p-4 border-t">
                    <span class="block text-xs font-bold uppercase tracking-wider text-indigo-800 dark:text-indigo-300 mb-2">{{ __('Crypto Collateral Locked') }}</span>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-white dark:bg-slate-900 rounded-lg p-2.5 border border-indigo-50 dark:border-indigo-900/50">
                            <span class="block text-[10px] text-slate-400 uppercase">{{ __('Quantity') }}</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ number_format($loan->collateral_amount, 6) }} {{ $loan->collateralCurrency->code }}</span>
                        </div>
                        <div class="bg-white dark:bg-slate-900 rounded-lg p-2.5 border border-indigo-50 dark:border-indigo-900/50">
                            <span class="block text-[10px] text-slate-400 uppercase">{{ __('Initial LTV') }}</span>
                            <span class="text-xs font-bold text-slate-800 dark:text-slate-200">{{ __n($loan->initial_ltv) }}%</span>
                        </div>
                        <div class="bg-white dark:bg-slate-900 rounded-lg p-2.5 border border-indigo-50 dark:border-indigo-900/50">
                            <span class="block text-[10px] text-slate-400 uppercase">{{ __('Liquidation Price') }}</span>
                            <span class="text-xs font-bold text-rose-600 dark:text-rose-400">Rp {{ __n(number_format($loan->liquidation_price, 0, ',', '.')) }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Decision Card -->
    @if($loan->status === 'pending')
        <div class="shadow-xs dark:shadow-none rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6" x-data="{ showRejectModal: false }">
            <h3 class="text-sm font-bold text-slate-900 dark:text-slate-100 mb-3 uppercase tracking-wider">{{ __('Review & Decision') }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">{{ __('Approve to display this loan listing on the public marketplace, or reject to decline the borrower\'s application.') }}</p>
            
            <div class="flex flex-wrap gap-3">
                <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST">
                    @csrf
                    <button type="submit"
                            class="inline-flex justify-center items-center gap-2 rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-indigo-700 transition-all">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                        {{ __('Approve Application') }}
                    </button>
                </form>

                <button type="button" @click="showRejectModal = true"
                        class="inline-flex justify-center items-center gap-2 rounded-xl bg-rose-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-rose-700 transition-all">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    {{ __('Reject Application') }}
                </button>
            </div>

            <!-- Reject Reason Modal -->
            <div x-show="showRejectModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4">
                <div class="bg-white dark:bg-slate-900 rounded-2xl p-6 max-w-md w-full border border-slate-200 dark:border-slate-800 shadow-xl space-y-4">
                    <h3 class="text-lg font-bold text-slate-900 dark:text-slate-100">{{ __('Reject Loan Request') }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400">{{ __('Please enter an optional reason for declining this loan application. The borrower will receive a notification.') }}</p>
                    <form action="{{ route('admin.loans.reject', $loan->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">{{ __('Rejection Reason (Optional)') }}</label>
                            <textarea name="rejection_reason" rows="3" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 p-3 text-xs text-slate-900 dark:text-slate-100 outline-none focus:ring-2 focus:ring-rose-500" placeholder="{{ __('e.g., Incomplete documentation or high risk profile.') }}"></textarea>
                        </div>
                        <div class="flex justify-end gap-2">
                            <button type="button" @click="showRejectModal = false" class="px-4 py-2 text-xs font-semibold text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800 rounded-xl">
                                {{ __('Cancel') }}
                            </button>
                            <button type="submit" class="px-4 py-2 text-xs font-semibold text-white bg-rose-600 hover:bg-rose-700 rounded-xl shadow-xs">
                                {{ __('Confirm Rejection') }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @elseif($loan->status === 'funded')
        <div class="shadow-xs dark:shadow-none rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6">
            <h3 class="text-sm font-bold text-emerald-800 dark:text-emerald-300 mb-3 uppercase tracking-wider">{{ __('Trigger Disbursement') }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 mb-4">{{ __('This loan is 100% funded and the contract agreement has been generated. Triggering disbursement will deposit the net funds to the borrower\'s wallet and settle all held lender allocations.') }}</p>
            <form action="{{ route('admin.loans.disburse', $loan->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-xs hover:bg-emerald-700 transition-all">
                    {{ __('Disburse Capital') }}
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
