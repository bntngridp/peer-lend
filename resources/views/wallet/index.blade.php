@extends('layouts.app')

@section('content')
@php
    $mainWallet = $wallets->first();
    $totalBalance = $wallets->sum('total_balance');
    $availableBalance = $wallets->sum('available_balance');
    $holdBalance = $wallets->sum('hold_balance');
@endphp

<!-- Midtrans Snap.js Sandbox -->
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>

<div class="space-y-6 max-w-7xl mx-auto" x-data="{ activeTab: 'deposit' }">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Wallet Overview') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Manage your funds, track cash flow, and review recent transactions.') }}</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="activeTab = 'deposit'" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                <span></span> {{ __('Deposit Funds') }}
            </button>
            <button @click="activeTab = 'withdraw'" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                <span></span> {{ __('Withdraw Funds') }}
            </button>
        </div>
    </div>

    <!-- 3 Summary Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: TOTAL BALANCE -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('TOTAL BALANCE') }}</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">+{{ __n('2.4%') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Rp {{ __n(number_format($totalBalance, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Updated') }} {{ __n('5') }} {{ __('mins ago') }}</p>
            </div>
        </div>

        <!-- Card 2: AVAILABLE FUNDS -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('AVAILABLE FUNDS') }}</span>
                <span class="text-xs font-bold text-emerald-700">{{ __('Ready to invest') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Rp {{ __n(number_format($availableBalance, 0, ',', '.')) }}
                </p>
                <div class="flex gap-2 mt-2">
                    <button @click="activeTab = 'deposit'" class="py-1 px-3 rounded-lg bg-emerald-700 text-white text-[11px] font-bold hover:bg-emerald-800">
                        {{ __('Deposit') }}
                    </button>
                    <button @click="activeTab = 'withdraw'" class="py-1 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-[11px] font-bold hover:bg-slate-50">
                        {{ __('Withdraw') }}
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 3: FUNDS ON HOLD -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('FUNDS ON HOLD') }}</span>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Rp {{ __n(number_format($holdBalance, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Pending escrows & reserves') }}</p>
            </div>
        </div>
    </div>

    <!-- Operation Tabs Header -->
    <div class="border-b border-slate-200 flex gap-6 text-xs font-bold text-slate-500 select-none">
        <button @click="activeTab = 'deposit'" 
                :class="activeTab === 'deposit' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            {{ __('Deposit Funds') }}
        </button>
        <button @click="activeTab = 'withdraw'" 
                :class="activeTab === 'withdraw' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            {{ __('Withdraw Funds') }}
        </button>
        <button @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            {{ __('Transaction History') }}
        </button>
    </div>

    <!-- ─── Tab 1: Deposit Funds ─────────────────────────────────────────── -->
    <div x-show="activeTab === 'deposit'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" x-data="{ depMethod: 'midtrans' }">
        
        <!-- Form Left Side (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Select Funding Source & Amount') }}</h3>

            <!-- 1. Funding Source Cards -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. {{ __('Funding Gateway Method') }}</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div @click="depMethod = 'midtrans'"
                         :class="depMethod === 'midtrans' ? 'border-emerald-700 bg-emerald-50/50 text-emerald-900' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:border-slate-300'"
                         class="p-3.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold block">Midtrans VA / QRIS</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">Fiat IDR</span>
                        </div>
                        <span class="text-[10px] font-semibold mt-1.5 opacity-80">BCA, Mandiri, BNI, BRI Virtual Account &amp; QRIS</span>
                    </div>
                    <div @click="depMethod = 'nowpayments'"
                         :class="depMethod === 'nowpayments' ? 'border-emerald-700 bg-emerald-50/50 text-emerald-900' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:border-slate-300'"
                         class="p-3.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold block">NOWPayments Crypto</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-800">300+ Crypto</span>
                        </div>
                        <span class="text-[10px] font-semibold mt-1.5 opacity-80">USDT (TRC20/ERC20), USDC, BTC, ETH, SOL</span>
                    </div>
                </div>
            </div>

            <!-- 2. Midtrans Deposit Form -->
            <form x-show="depMethod === 'midtrans'" id="deposit-form" action="{{ route('wallet.deposit') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label for="dep_currency_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Select Currency') }}</label>
                    <select name="currency_id" id="dep_currency_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}" data-code="{{ $curr->code }}">{{ $curr->code }} - {{ $curr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="dep_amount" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Deposit Amount (IDR)') }}</label>
                    <input type="number" name="amount" id="dep_amount" required min="10000" step="any"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="500000">
                </div>

                <button type="submit" id="submit-deposit-btn"
                        class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    {{ __('Pay via Midtrans Snap') }} &rarr;
                </button>
            </form>

            <!-- 3. NOWPayments Crypto Deposit Form -->
            <form x-show="depMethod === 'nowpayments'" id="crypto-deposit-form" style="display: none;" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Select Crypto Currency') }}</label>
                    <select id="crypto_dep_currency" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        <option value="usdttrc20">USDT (TRC-20)</option>
                        <option value="usdterc20">USDT (ERC-20)</option>
                        <option value="usdc">USDC (USD Coin)</option>
                        <option value="btc">Bitcoin (BTC)</option>
                        <option value="eth">Ethereum (ETH)</option>
                        <option value="sol">Solana (SOL)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Amount (USD equivalent)') }}</label>
                    <input type="number" id="crypto_dep_amount" required min="5" step="any" value="100"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="100">
                </div>

                <button type="submit" id="submit-crypto-dep-btn"
                        class="w-full py-3 rounded-xl bg-indigo-700 text-white font-bold text-xs hover:bg-indigo-800 transition-colors shadow-xs">
                    {{ __('Generate NOWPayments Invoice') }} &rarr;
                </button>
            </form>
        </div>

        <!-- Deposit Info Right Side (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Deposit Limits Box -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('GATEWAY SPECS') }}</span>
                
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100 font-medium">
                        <span class="text-slate-500">{{ __('Fiat Gateway') }}</span>
                        <span class="font-bold text-emerald-800">Midtrans Snap</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 font-medium">
                        <span class="text-slate-500">{{ __('Crypto Gateway') }}</span>
                        <span class="font-bold text-indigo-800">NOWPayments IPN</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-900 font-medium">
                    <strong>{{ __('Expected Settlement:') }}</strong> {{ __('Instant settlement via Virtual Account / Webhook IPN Callback.') }}
                </div>
            </div>
        </div>

    </div>

    <!-- ─── Tab 2: Withdraw Funds ───────────────────────────────────────── -->
    <div x-show="activeTab === 'withdraw'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" style="display: none;" x-data="{ wdMethod: 'xendit' }">
        
        <!-- Form Left Side (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Automated Withdrawal & Instant Payout') }}</h3>

            <!-- Method Selection Cards -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Withdrawal Gateway') }}</label>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div @click="wdMethod = 'xendit'"
                         :class="wdMethod === 'xendit' ? 'border-emerald-700 bg-emerald-50/50 text-emerald-900' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:border-slate-300'"
                         class="p-3.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold block">Xendit Instan Bank Payout</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-emerald-100 text-emerald-800">140+ Bank IDR</span>
                        </div>
                        <span class="text-[10px] font-semibold mt-1.5 opacity-80">BCA, Mandiri, BNI, BRI, Permata 24/7 Transfer</span>
                    </div>
                    <div @click="wdMethod = 'nowpayments'"
                         :class="wdMethod === 'nowpayments' ? 'border-emerald-700 bg-emerald-50/50 text-emerald-900' : 'border-slate-200 bg-slate-50/50 text-slate-600 hover:border-slate-300'"
                         class="p-3.5 rounded-xl border flex flex-col justify-between cursor-pointer transition-all">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold block">NOWPayments Crypto Payout</span>
                            <span class="px-2 py-0.5 text-[10px] font-bold rounded bg-indigo-100 text-indigo-800">Crypto Address</span>
                        </div>
                        <span class="text-[10px] font-semibold mt-1.5 opacity-80">Instant Payout to USDT, USDC, BTC, ETH Wallet</span>
                    </div>
                </div>
            </div>

            <!-- Xendit Bank Withdrawal Form -->
            <form x-show="wdMethod === 'xendit'" id="xendit-withdraw-form" class="space-y-4 pt-2">
                @csrf
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Destination Bank') }}</label>
                        <select id="wd_bank_code" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                            <option value="BCA">Bank BCA</option>
                            <option value="MANDIRI">Bank Mandiri</option>
                            <option value="BNI">Bank BNI</option>
                            <option value="BRI">Bank BRI</option>
                            <option value="PERMATA">Bank Permata</option>
                            <option value="CIMB">Bank CIMB Niaga</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Account Number') }}</label>
                        <input type="text" id="wd_account_number" required value="8492019482"
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none"
                               placeholder="8492019482">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Account Holder Name') }}</label>
                    <input type="text" id="wd_account_holder_name" required value="{{ Auth::user()->profile?->full_name ?? Auth::user()->email }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none"
                           placeholder="{{ __('Full Name') }}">
                </div>

                <div>
                    <div class="flex justify-between mb-1">
                        <label class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Amount (IDR)') }}</label>
                        <span class="text-xs font-bold text-emerald-700 cursor-pointer" onclick="document.getElementById('wd_xendit_amount').value = '{{ (int)$availableBalance }}'">
                            Max: Rp {{ number_format($availableBalance, 0, ',', '.') }}
                        </span>
                    </div>
                    <input type="number" id="wd_xendit_amount" required min="50000" step="any" value="100000"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none"
                           placeholder="{{ __('Min. Rp 50.000') }}">
                </div>

                <button type="submit" id="submit-xendit-wd-btn" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    {{ __('Process Xendit Instant Transfer') }} &rarr;
                </button>
            </form>

            <!-- NOWPayments Crypto Withdrawal Form -->
            <form x-show="wdMethod === 'nowpayments'" id="crypto-withdraw-form" style="display: none;" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Crypto Asset') }}</label>
                    <select id="wd_crypto_currency" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        <option value="usdttrc20">USDT (TRC-20)</option>
                        <option value="usdterc20">USDT (ERC-20)</option>
                        <option value="usdc">USDC (USD Coin)</option>
                        <option value="btc">Bitcoin (BTC)</option>
                        <option value="eth">Ethereum (ETH)</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Wallet Destination Address') }}</label>
                    <input type="text" id="wd_crypto_address" required
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none font-mono text-xs"
                           placeholder="TTYx8492019482910482910...">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Amount (USD Equivalent)') }}</label>
                    <input type="number" id="wd_crypto_amount" required min="10" value="50"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none"
                           placeholder="{{ __('Min $10') }}">
                </div>

                <button type="submit" id="submit-crypto-wd-btn" class="w-full py-3 rounded-xl bg-indigo-700 text-white font-bold text-xs hover:bg-indigo-800 transition-colors shadow-xs">
                    {{ __('Process NOWPayments Instant Payout') }} &rarr;
                </button>
            </form>
        </div>

        <!-- Withdraw Summary Right Side (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('DISBURSEMENT SUMMARY') }}</span>

                <div class="space-y-2.5 text-xs font-medium">
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">{{ __('Current Balance') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">{{ __('Fee') }}</span>
                        <span class="font-bold text-emerald-700">Rp 0 ({{ __('Covered by Platform') }})</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px]">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">{{ __('ESTIMATED TRANSFER TIME') }}</span>
                    <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ __('Instant (24/7 Real-Time Payout)') }}</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ─── Tab 3: Transaction History ──────────────────────────────────── -->
    <div x-show="activeTab === 'history'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden" style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Transaction Ledger') }}</h3>
            <span class="text-xs font-medium text-slate-500">{{ __('Total') }}: {{ __n($transactions->total()) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('Transaction') }}</th>
                        <th class="py-3.5 px-6">{{ __('TYPE & METHOD') }}</th>
                        <th class="py-3.5 px-6">{{ __('AMOUNT') }}</th>
                        <th class="py-3.5 px-6">{{ __('STATUS') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('DATE & TIME') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg font-bold flex items-center justify-center text-xs uppercase
                                    @if(in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund'])) bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                    {{ substr($tx->type, 0, 3) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs capitalize">{{ __(str_replace('_', ' ', $tx->type)) }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium block truncate max-w-[220px]">{{ $tx->description }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-700 dark:text-slate-300 border border-slate-200 dark:border-slate-700">
                                @if(str_contains(strtolower($tx->description), 'midtrans'))
                                    💳 Midtrans (Fiat)
                                @elseif(str_contains(strtolower($tx->description), 'xendit'))
                                    🏦 Xendit Payout
                                @elseif(str_contains(strtolower($tx->description), 'nowpayments'))
                                    🪙 NOWPayments (Crypto)
                                @else
                                    ⚖️ {{ __(ucfirst($tx->type)) }}
                                @endif
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="font-extrabold text-sm block {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? '+' : '-' }}
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium block mt-0.5">
                                {{ __('Before') }}: Rp {{ number_format($tx->balance_before, 0, ',', '.') }} • <strong class="text-slate-700 dark:text-slate-300">{{ __('After') }}: Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</strong>
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-[11px] font-bold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Completed') }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-right font-semibold text-slate-500 dark:text-slate-400 whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($tx->getRawOriginal('created_at') ?? $tx->created_at, 'UTC')->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                            {{ __('No transaction ledger records found.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $transactions->links() }}
            </div>
        @endif
    </div>

</div>

<!-- Midtrans, Xendit & NOWPayments Integration JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    // 1. Midtrans Deposit Form
    const depositForm = document.getElementById('deposit-form');
    const submitDepositBtn = document.getElementById('submit-deposit-btn');

    if (depositForm) {
        depositForm.addEventListener('submit', async (e) => {
            const selectEl = document.getElementById('dep_currency_id');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const currencyCode = selectedOption.getAttribute('data-code');

            if (currencyCode === 'IDR') {
                e.preventDefault();
                submitDepositBtn.disabled = true;
                submitDepositBtn.innerText = 'Initiating Midtrans payment...';

                const amount = document.getElementById('dep_amount').value;

                try {
                    const response = await fetch('{{ route("wallet.deposit.initiate") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ amount: amount })
                    });

                    const resData = await response.json();

                    if (resData.status === 'success') {
                        const snapToken = resData.data.snap_token;
                        window.snap.pay(snapToken, {
                            onSuccess: async function(result) {
                                submitDepositBtn.innerText = 'Verifying payment...';
                                try {
                                    await fetch('{{ route("wallet.deposit.confirm") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ order_id: result.order_id || resData.data.payment_id })
                                    });
                                } catch (err) {
                                    console.error(err);
                                }
                                window.location.href = '{{ route("wallet.index") }}?status=success&msg=Payment settled!';
                            },
                            onPending: async function(result) {
                                try {
                                    await fetch('{{ route("wallet.deposit.confirm") }}', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                            'Accept': 'application/json'
                                        },
                                        body: JSON.stringify({ order_id: result.order_id || resData.data.payment_id })
                                    });
                                } catch (err) {
                                    console.error(err);
                                }
                                window.location.href = '{{ route("wallet.index") }}?status=pending&msg=Payment pending.';
                            },
                            onError: function(result) {
                                window.location.href = '{{ route("wallet.index") }}?status=error&msg=Payment failed.';
                            },
                            onClose: function() {
                                submitDepositBtn.disabled = false;
                                submitDepositBtn.innerText = 'Pay via Midtrans Snap →';
                            }
                        });
                    } else {
                        alert('Error: ' + resData.message);
                        submitDepositBtn.disabled = false;
                        submitDepositBtn.innerText = 'Pay via Midtrans Snap →';
                    }
                } catch (err) {
                    console.error(err);
                    alert('An unexpected error occurred. Please try again.');
                    submitDepositBtn.disabled = false;
                    submitDepositBtn.innerText = 'Pay via Midtrans Snap →';
                }
            }
        });
    }

    // 2. NOWPayments Crypto Deposit Form
    const cryptoDepForm = document.getElementById('crypto-deposit-form');
    const submitCryptoDepBtn = document.getElementById('submit-crypto-dep-btn');

    if (cryptoDepForm) {
        cryptoDepForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitCryptoDepBtn.disabled = true;
            submitCryptoDepBtn.innerText = 'Creating NOWPayments Invoice...';

            const payCurrency = document.getElementById('crypto_dep_currency').value;
            const amount = document.getElementById('crypto_dep_amount').value;

            try {
                const response = await fetch('{{ route("wallet.crypto.deposit.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ amount: amount, pay_currency: payCurrency })
                });

                const resData = await response.json();

                if (resData.status === 'success') {
                    if (resData.data.invoice_url) {
                        window.open(resData.data.invoice_url, '_blank');
                    }
                    alert('NOWPayments Invoice Created Successfully! Payment Link: ' + resData.data.invoice_url);
                    window.location.reload();
                } else {
                    alert('Error: ' + resData.message);
                }
            } catch (err) {
                console.error(err);
                alert('Failed to connect to NOWPayments.');
            } finally {
                submitCryptoDepBtn.disabled = false;
                submitCryptoDepBtn.innerText = 'Generate NOWPayments Invoice →';
            }
        });
    }

    // 3. Xendit Bank Withdrawal Form
    const xenditWdForm = document.getElementById('xendit-withdraw-form');
    const submitXenditWdBtn = document.getElementById('submit-xendit-wd-btn');

    if (xenditWdForm) {
        xenditWdForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitXenditWdBtn.disabled = true;
            submitXenditWdBtn.innerText = 'Processing Xendit Transfer...';

            const bankCode = document.getElementById('wd_bank_code').value;
            const accountNumber = document.getElementById('wd_account_number').value;
            const accountHolderName = document.getElementById('wd_account_holder_name').value;
            const amount = document.getElementById('wd_xendit_amount').value;

            try {
                const response = await fetch('{{ route("wallet.withdraw.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        bank_code: bankCode,
                        account_number: accountNumber,
                        account_holder_name: accountHolderName,
                        amount: amount
                    })
                });

                const resData = await response.json();

                if (resData.status === 'success') {
                    alert('Penarikan dana via Xendit sebesar Rp ' + Number(amount).toLocaleString('id-ID') + ' sukses diajukan!');
                    window.location.reload();
                } else {
                    alert('Error: ' + resData.message);
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memproses penarikan Xendit.');
            } finally {
                submitXenditWdBtn.disabled = false;
                submitXenditWdBtn.innerText = 'Process Xendit Instant Transfer →';
            }
        });
    }

    // 4. NOWPayments Crypto Withdrawal Form
    const cryptoWdForm = document.getElementById('crypto-withdraw-form');
    const submitCryptoWdBtn = document.getElementById('submit-crypto-wd-btn');

    if (cryptoWdForm) {
        cryptoWdForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            submitCryptoWdBtn.disabled = true;
            submitCryptoWdBtn.innerText = 'Processing NOWPayments Payout...';

            const currency = document.getElementById('wd_crypto_currency').value;
            const address = document.getElementById('wd_crypto_address').value;
            const amount = document.getElementById('wd_crypto_amount').value;

            try {
                const response = await fetch('{{ route("wallet.crypto.withdraw.initiate") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        currency: currency,
                        address: address,
                        amount: amount
                    })
                });

                const resData = await response.json();

                if (resData.status === 'success') {
                    alert('Penarikan Kripto via NOWPayments sebesar ' + amount + ' ' + currency.toUpperCase() + ' berhasil diajukan!');
                    window.location.reload();
                } else {
                    alert('Error: ' + resData.message);
                }
            } catch (err) {
                console.error(err);
                alert('Terjadi kesalahan saat memproses penarikan kripto.');
            } finally {
                submitCryptoWdBtn.disabled = false;
                submitCryptoWdBtn.innerText = 'Process NOWPayments Instant Payout →';
            }
        });
    }
});
</script>
@endsection
