@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Welcome Header -->
    <div class="md:flex md:items-center md:justify-between mb-8 pb-6 border-b border-gray-200">
        <div class="min-w-0 flex-1">
            <h2 class="text-2xl font-bold leading-7 text-gray-900 sm:truncate sm:text-3xl tracking-tight">
                Welcome back, {{ Auth::user()->profile->full_name ?? Auth::user()->email }}!
            </h2>
            <div class="mt-1 flex flex-col sm:mt-0 sm:flex-row sm:flex-wrap sm:space-x-6">
                <div class="mt-2 flex items-center text-sm text-gray-500">
                    <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-1 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-700/10 uppercase tracking-wider">
                        {{ Auth::user()->roles->first()->name ?? 'Member' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2 lg:grid-cols-3">
        
        <!-- Wallet Card -->
        <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 bg-white p-6">
            <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">My Wallet</h3>
                <span class="text-xs font-medium text-gray-400">IDR Account</span>
            </div>
            <div>
                <p class="text-3xl font-extrabold text-indigo-600 tracking-tight">
                    Rp {{ number_format(Auth::user()->walletFor(\App\Models\Currency::where('code', 'IDR')->first()?->id ?? 1)?->available_balance ?? 0, 0, ',', '.') }}
                </p>
                <div class="mt-2 flex gap-4 text-xs text-gray-500">
                    <span>Hold: Rp {{ number_format(Auth::user()->walletFor(\App\Models\Currency::where('code', 'IDR')->first()?->id ?? 1)?->hold_balance ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>
            <div class="mt-6 flex gap-3">
                <a href="#" class="flex-1 text-center rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                    Deposit
                </a>
                <a href="#" class="flex-1 text-center rounded-xl border border-gray-300 bg-white px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Withdraw
                </a>
            </div>
        </div>

        <!-- KYC Card -->
        <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 bg-white p-6">
            <div class="flex items-center justify-between border-b border-gray-50 pb-4 mb-4">
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500">Identity Verification</h3>
                <span class="text-xs font-medium text-gray-400">KYC Compliance</span>
            </div>
            <div class="flex flex-col h-28 justify-between">
                @if(Auth::user()->kyc)
                    @if(Auth::user()->kyc->isApproved())
                        <div class="flex items-center gap-2.5 text-emerald-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold">Verification Approved</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">You are verified to borrow or invest on the platform.</p>
                    @elseif(Auth::user()->kyc->isPending())
                        <div class="flex items-center gap-2.5 text-amber-600">
                            <svg class="h-6 w-6 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                            <span class="text-sm font-semibold">Verification Pending Review</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Our compliance officers are currently reviewing your documents.</p>
                    @else
                        <div class="flex items-center gap-2.5 text-red-600">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                            </svg>
                            <span class="text-sm font-semibold">Verification Rejected</span>
                        </div>
                        <p class="text-xs text-gray-500 mt-2">Reason: <strong>{{ Auth::user()->kyc->rejected_reason }}</strong></p>
                    @endif
                @else
                    <div class="flex items-center gap-2.5 text-gray-500">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M9.75 9.75l4.5 4.5m0-4.5l-4.5 4.5M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <span class="text-sm font-semibold">Not Verified</span>
                    </div>
                    <p class="text-xs text-gray-500 mt-2">Please complete identity verification to unlock full finance features.</p>
                @endif
                
                @if(!Auth::user()->kyc || Auth::user()->kyc->isRejected())
                    <div class="mt-4">
                        <a href="{{ route('kyc.index') }}" class="inline-flex w-full justify-center rounded-xl bg-indigo-600 px-3 py-2 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                            Verify Identity
                        </a>
                    </div>
                @endif
            </div>
        </div>

        <!-- Quick actions -->
        <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 bg-white p-6 flex flex-col justify-between">
            <div>
                <h3 class="text-sm font-bold uppercase tracking-wider text-gray-500 border-b border-gray-50 pb-4 mb-4">Quick Actions</h3>
                <div class="space-y-3">
                    @if(Auth::user()->hasRole('borrower'))
                        <a href="#" class="flex items-center justify-between rounded-xl border border-gray-200 p-3 hover:bg-indigo-50/20 hover:border-indigo-200 transition-all group">
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600">Apply for a Loan</span>
                            <span class="text-gray-400">&rarr;</span>
                        </a>
                    @else
                        <a href="#" class="flex items-center justify-between rounded-xl border border-gray-200 p-3 hover:bg-indigo-50/20 hover:border-indigo-200 transition-all group">
                            <span class="text-sm font-semibold text-gray-800 group-hover:text-indigo-600">Browse Loans Marketplace</span>
                            <span class="text-gray-400">&rarr;</span>
                        </a>
                    @endif
                </div>
            </div>
        </div>

    </div>

</div>
@endsection
