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

<div class="space-y-6 max-w-7xl mx-auto" x-data="{ activeTab: 'deposit', historySubTab: 'ledger' }">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Wallet Overview') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Manage your funds, track cash flow, and review recent transactions.') }}</p>
        </div>
    </div>

    <!-- 3 Summary Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: TOTAL BALANCE -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('TOTAL BALANCE') }}</span>
                <span class="text-xs font-bold text-emerald-600 dark:text-emerald-400">{{ __('Verified Wallet') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Rp {{ __n(number_format($totalBalance, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Terakhir disinkronisasi') }} {{ now()->translatedFormat('H:i') }} WIB</p>
            </div>
        </div>

        <!-- Card 2: AVAILABLE FUNDS -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('AVAILABLE FUNDS') }}</span>
                <span class="text-xs font-bold text-emerald-700 dark:text-emerald-400">{{ __('Ready to invest') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 dark:text-slate-100 tracking-tight">
                    Rp {{ __n(number_format($availableBalance, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Active capital for allocation') }}</p>
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

    <!-- Pending Payments Alert Banner -->
    @if(isset($pendingPayments) && $pendingPayments->isNotEmpty())
        <div class="rounded-2xl border border-amber-300 dark:border-amber-700/60 bg-amber-50/70 dark:bg-amber-950/30 p-5 shadow-xs space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-amber-200/80 dark:border-amber-800/50 pb-3">
                <div class="flex items-center gap-2.5">
                    <span class="relative flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-amber-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-amber-500"></span>
                    </span>
                    <div>
                        <h3 class="text-sm font-extrabold text-amber-950 dark:text-amber-200">{{ __('Pending Deposit Payments') }}</h3>
                        <p class="text-xs font-medium text-amber-800/80 dark:text-amber-300/80">{{ __('You have deposit requests awaiting payment completion.') }}</p>
                    </div>
                </div>
                <span class="text-xs font-bold text-amber-700 dark:text-amber-300">
                    {{ $pendingPayments->count() }} {{ __('Pending') }}
                </span>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                @foreach($pendingPayments as $pendingPay)
                    <div class="rounded-xl border border-amber-200 dark:border-amber-800/60 bg-white dark:bg-slate-900 p-4 flex flex-col justify-between gap-3 shadow-2xs">
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-xs font-black text-slate-900 dark:text-slate-100">
                                        Rp {{ number_format($pendingPay->amount, 0, ',', '.') }}
                                    </span>
                                    <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                        {{ __(ucfirst($pendingPay->gateway)) }}
                                    </span>
                                </div>
                                <span class="text-[11px] font-mono text-slate-400 block mt-0.5">
                                    #{{ substr($pendingPay->id, 0, 13) }}...
                                </span>
                            </div>
                            <div class="text-right">
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0 animate-pulse"></span>
                                    <span>{{ __('Menunggu Pembayaran') }}</span>
                                </span>
                                <span class="text-[10px] text-slate-400 block mt-1 font-medium">
                                    {{ \Carbon\Carbon::parse($pendingPay->created_at)->setTimezone('Asia/Jakarta')->format('d M Y, H:i') }} WIB
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center justify-between gap-2 pt-2 border-t border-slate-100 dark:border-slate-800">
                            <span class="text-[11px] text-slate-500 dark:text-slate-400 font-medium flex items-center gap-1">
                                <svg class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                {{ __('Sisa waktu') }}: ~24 Jam
                            </span>
                            <div class="flex items-center gap-2">
                                <button type="button" onclick="checkPaymentStatus('{{ $pendingPay->id }}', this)"
                                        class="px-3 py-1.5 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 dark:hover:bg-slate-700 text-slate-700 dark:text-slate-300 text-xs font-bold transition-colors cursor-pointer">
                                    {{ __('Cek Status') }}
                                </button>
                                @if($pendingPay->gateway === 'midtrans' && $pendingPay->gateway_ref_id)
                                    <button type="button" onclick="payPendingSnap('{{ $pendingPay->gateway_ref_id }}', '{{ $pendingPay->id }}')"
                                            class="px-3 py-1.5 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold transition-colors shadow-2xs cursor-pointer">
                                        {{ __('Lanjutkan Bayar') }} &rarr;
                                    </button>
                                @elseif($pendingPay->gateway === 'nowpayments' && isset($pendingPay->payload['invoice_url']))
                                    <a href="{{ $pendingPay->payload['invoice_url'] }}" target="_blank"
                                       class="px-3 py-1.5 rounded-lg bg-indigo-700 hover:bg-indigo-800 text-white text-xs font-bold transition-colors shadow-2xs">
                                        {{ __('Buka Tagihan') }} &rarr;
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

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

    <!-- ─── Tab 3: Transaction History & Payment Invoices ──────────────── -->
    <div x-show="activeTab === 'history'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden" style="display: none;">
        
        <!-- Sub-Tabs Header -->
        <div class="flex flex-col sm:flex-row sm:items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-3 bg-slate-50/50 dark:bg-slate-800/40 gap-3">
            <div class="flex items-center gap-2">
                <button type="button" @click="historySubTab = 'ledger'"
                        :class="historySubTab === 'ledger' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-colors cursor-pointer">
                    {{ __('Buku Mutasi Saldo') }} ({{ __n($transactions->total()) }})
                </button>
                <button type="button" @click="historySubTab = 'invoices'"
                        :class="historySubTab === 'invoices' ? 'bg-emerald-700 text-white font-bold' : 'bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300 font-semibold hover:bg-slate-200 dark:hover:bg-slate-700'"
                        class="px-3 py-1.5 rounded-xl text-xs transition-colors cursor-pointer">
                    {{ __('Riwayat Tagihan & Deposit') }} ({{ isset($recentPayments) ? $recentPayments->count() : 0 }})
                </button>
            </div>
            <span class="text-xs font-medium text-slate-400">{{ __('Real-time Ledger Audit') }}</span>
        </div>

        <!-- 1. Mutasi Saldo Sub-Table -->
        <div x-show="historySubTab === 'ledger'" class="overflow-x-auto">
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
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                @if(str_contains(strtolower($tx->description), 'midtrans'))
                                    @php
                                        preg_match('/midtrans \(([^)]+)\)/i', $tx->description, $matches);
                                        $midMethod = $matches[1] ?? 'Fiat';
                                    @endphp
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M2.25 8.25h19.5M2.25 9h19.5m-16.5 5.25h6m-6 2.25h3m-6 3.75h16.5a1.5 1.5 0 001.5-1.5V6a1.5 1.5 0 00-1.5-1.5H3.75A1.5 1.5 0 002.25 6v12a1.5 1.5 0 001.5 1.5z"/></svg>
                                    <span>Midtrans ({{ $midMethod }})</span>
                                @elseif(str_contains(strtolower($tx->description), 'xendit'))
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21v-8.25M15.75 21v-8.25M8.25 21v-8.25M3 9l9-6 9 6m-1.5 12V10.5M4.5 21V10.5M3 21h18"/></svg>
                                    <span>Xendit Payout</span>
                                @elseif(str_contains(strtolower($tx->description), 'nowpayments'))
                                    <svg class="w-3.5 h-3.5 text-amber-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    <span>NOWPayments (Crypto)</span>
                                @else
                                    <svg class="w-3.5 h-3.5 text-slate-500 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M7.5 21L3 16.5m0 0L7.5 12M3 16.5h13.5m0-13.5L21 7.5m0 0L16.5 12M21 7.5H7.5"/></svg>
                                    <span>{{ __(ucfirst($tx->type)) }}</span>
                                @endif
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="font-extrabold text-sm block {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? 'text-emerald-700 dark:text-emerald-400' : 'text-rose-600 dark:text-rose-400' }}">
                                {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? '+' : '-' }}
                                Rp {{ number_format($tx->amount, 0, ',', '.')) }}
                            </span>
                            <span class="text-[11px] text-slate-400 font-medium block mt-0.5">
                                {{ __('Before') }}: Rp {{ number_format($tx->balance_before, 0, ',', '.')) }} • <strong class="text-slate-700 dark:text-slate-300">{{ __('After') }}: Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</strong>
                            </span>
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                <span>{{ __('Completed') }}</span>
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

            @if($transactions->hasPages())
                <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                    {{ $transactions->links() }}
                </div>
            @endif
        </div>

        <!-- 2. Riwayat Tagihan & Status Gateway Sub-Table -->
        <div x-show="historySubTab === 'invoices'" class="overflow-x-auto" style="display: none;">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/50 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('Order ID & Gateway') }}</th>
                        <th class="py-3.5 px-6">{{ __('Nominal Tagihan') }}</th>
                        <th class="py-3.5 px-6">{{ __('Status Tagihan') }}</th>
                        <th class="py-3.5 px-6">{{ __('Waktu Inisiasi') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('Aksi') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($recentPayments ?? [] as $pay)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-2.5">
                                <span class="text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider">
                                    {{ __(ucfirst($pay->gateway)) }}
                                </span>
                                <div>
                                    <span class="font-mono text-xs font-bold text-slate-900 dark:text-slate-100 block">#{{ substr($pay->id, 0, 14) }}...</span>
                                    <span class="text-[10px] text-slate-400">{{ $pay->gateway === 'midtrans' ? 'Snap Virtual Account / QRIS' : 'NOWPayments Invoice' }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-sm text-slate-900 dark:text-slate-100 whitespace-nowrap">
                            Rp {{ number_format($pay->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 whitespace-nowrap">
                            @if($pay->status === 'success')
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                    <span>{{ __('Berhasil (Settled)') }}</span>
                                </span>
                            @elseif($pay->status === 'pending')
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0 animate-pulse"></span>
                                    <span>{{ __('Menunggu Pembayaran') }}</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                    <span class="h-1.5 w-1.5 rounded-full bg-rose-500 shrink-0"></span>
                                    <span>{{ __('Gagal / Expired') }}</span>
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6 text-slate-500 dark:text-slate-400 font-medium whitespace-nowrap">
                            {{ \Carbon\Carbon::parse($pay->created_at)->setTimezone('Asia/Jakarta')->format('M d, Y H:i') }} WIB
                        </td>
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            @if($pay->status === 'pending')
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" onclick="checkPaymentStatus('{{ $pay->id }}', this)"
                                            class="px-2.5 py-1 rounded-lg border border-slate-200 dark:border-slate-700 bg-slate-50 dark:bg-slate-800 hover:bg-slate-100 text-slate-700 dark:text-slate-300 text-[11px] font-bold transition-colors cursor-pointer">
                                        {{ __('Cek Status') }}
                                    </button>
                                    @if($pay->gateway === 'midtrans' && $pay->gateway_ref_id)
                                        <button type="button" onclick="payPendingSnap('{{ $pay->gateway_ref_id }}', '{{ $pay->id }}')"
                                                class="px-2.5 py-1 rounded-lg bg-emerald-700 hover:bg-emerald-800 text-white text-[11px] font-bold transition-colors cursor-pointer shadow-2xs">
                                            {{ __('Bayar') }} &rarr;
                                        </button>
                                    @endif
                                </div>
                            @elseif($pay->status === 'success')
                                <span class="text-[11px] font-semibold text-emerald-700 dark:text-emerald-400">{{ __('Tervalidasi') }}</span>
                            @else
                                <span class="text-[11px] font-semibold text-slate-400">{{ __('Kadaluarsa') }}</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                            {{ __('Belum ada riwayat tagihan gateway.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>

<!-- Midtrans, Xendit & NOWPayments Integration JS -->
<script>
window.payPendingSnap = function(snapToken, paymentId) {
    if (!window.snap) {
        if (window.showPaymentFailed) {
            window.showPaymentFailed({
                title: 'Gateway Belum Siap',
                message: 'Script gateway pembayaran Midtrans sedang dimuat, silakan coba sesaat lagi.',
                reason: 'Midtrans Snap library not initialized'
            });
        } else {
            alert('Payment gateway script not loaded yet.');
        }
        return;
    }
    window.snap.pay(snapToken, {
        onSuccess: async function(result) {
            try {
                await fetch('{{ route("wallet.deposit.confirm") }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ order_id: result.order_id || paymentId })
                });
            } catch (err) {
                console.error(err);
            }
            window.location.href = '{{ route("wallet.index") }}?status=success&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent('Pembayaran tagihan deposit berhasil tervalidasi.');
        },
        onPending: function(result) {
            window.location.href = '{{ route("wallet.index") }}?status=pending&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent('Transaksi sedang menunggu transfer pembayaran Anda.');
        },
        onError: function(result) {
            window.location.href = '{{ route("wallet.index") }}?status=error&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent(result.status_message || 'Pembayaran dibatalkan atau gagal diproses.');
        },
        onClose: function() {
            console.log('Snap popup closed by user.');
        }
    });
};

window.checkPaymentStatus = async function(paymentId, btnElement) {
    if (btnElement) {
        btnElement.disabled = true;
        btnElement.innerText = 'Checking...';
    }
    try {
        const response = await fetch('{{ route("wallet.deposit.confirm") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order_id: paymentId })
        });
        const resData = await response.json();
        window.location.href = '{{ route("wallet.index") }}?status=success&order_id=' + encodeURIComponent(paymentId) + '&msg=' + encodeURIComponent('Status pembayaran telah berhasil disinkronkan.');
    } catch (err) {
        console.error(err);
        if (window.showPaymentFailed) {
            window.showPaymentFailed({
                title: 'Sinkronisasi Gagal',
                message: 'Tidak dapat menghubungi server gateway pembayaran saat ini.',
                reason: 'Network or gateway response error',
                orderId: paymentId
            });
        } else {
            alert('Gagal menyinkronkan status pembayaran.');
        }
        if (btnElement) {
            btnElement.disabled = false;
            btnElement.innerText = 'Cek Status';
        }
    }
};

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
                        const paymentId = resData.data.payment_id;
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
                                        body: JSON.stringify({ order_id: result.order_id || paymentId })
                                    });
                                } catch (err) {
                                    console.error(err);
                                }
                                window.location.href = '{{ route("wallet.index") }}?status=success&amount=' + encodeURIComponent(amount) + '&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent('Deposit saldo sebesar Rp ' + Number(amount).toLocaleString('id-ID') + ' berhasil diselesaikan!');
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
                                        body: JSON.stringify({ order_id: result.order_id || paymentId })
                                    });
                                } catch (err) {
                                    console.error(err);
                                }
                                window.location.href = '{{ route("wallet.index") }}?status=pending&amount=' + encodeURIComponent(amount) + '&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent('Tagihan Virtual Account / QRIS telah dibuat. Silakan selesaikan pembayaran sebelum batas waktu.');
                            },
                            onError: function(result) {
                                window.location.href = '{{ route("wallet.index") }}?status=error&order_id=' + encodeURIComponent(result.order_id || paymentId) + '&msg=' + encodeURIComponent(result.status_message || 'Pembayaran deposit Midtrans gagal atau dibatalkan.');
                            },
                            onClose: function() {
                                submitDepositBtn.disabled = false;
                                submitDepositBtn.innerText = 'Pay via Midtrans Snap →';
                                window.location.reload();
                            }
                        });
                    } else {
                        if (window.showPaymentFailed) {
                            window.showPaymentFailed({
                                title: 'Inisiasi Deposit Gagal',
                                message: resData.message || 'Tidak dapat membuat tagihan Midtrans.',
                                reason: resData.message
                            });
                        } else {
                            alert('Error: ' + resData.message);
                        }
                        submitDepositBtn.disabled = false;
                        submitDepositBtn.innerText = 'Pay via Midtrans Snap →';
                    }
                } catch (err) {
                    console.error(err);
                    if (window.showPaymentFailed) {
                        window.showPaymentFailed({
                            title: 'Terjadi Kesalahan',
                            message: 'Terjadi kendala jaringan saat menghubungkan ke gateway Midtrans.',
                            reason: 'Network Connection Error'
                        });
                    } else {
                        alert('An unexpected error occurred. Please try again.');
                    }
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
                    if (window.showPaymentSuccess) {
                        window.showPaymentSuccess({
                            title: 'Tagihan Kripto Berhasil Dibuat!',
                            message: 'Invoice deposit NOWPayments berhasil digenerate. Silakan kirimkan aset kripto ke alamat yang tertera.',
                            amount: amount + ' ' + payCurrency.toUpperCase(),
                            orderId: resData.data.payment_id || '#NP-' + Date.now(),
                            txType: 'Deposit Kripto (NOWPayments)',
                            actionUrl: resData.data.invoice_url,
                            actionText: 'Buka Halaman Invoice Kripto ↗'
                        });
                    } else {
                        alert('NOWPayments Invoice Created Successfully! Payment Link: ' + resData.data.invoice_url);
                        window.location.reload();
                    }
                } else {
                    if (window.showPaymentFailed) {
                        window.showPaymentFailed({
                            title: 'Pembuatan Invoice Gagal',
                            message: resData.message || 'Gagal membuat tagihan invoice kripto.',
                            reason: resData.message
                        });
                    } else {
                        alert('Error: ' + resData.message);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.showPaymentFailed) {
                    window.showPaymentFailed({
                        title: 'Koneksi Gateway Gagal',
                        message: 'Gagal terhubung ke server NOWPayments API.',
                        reason: 'Network Timeout'
                    });
                } else {
                    alert('Failed to connect to NOWPayments.');
                }
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
                    if (window.showPaymentSuccess) {
                        window.showPaymentSuccess({
                            title: 'Penarikan Bank Berhasil Diajukan!',
                            message: 'Permintaan transfer dana via Xendit sebesar Rp ' + Number(amount).toLocaleString('id-ID') + ' ke rekening ' + bankCode.toUpperCase() + ' ' + accountNumber + ' (' + accountHolderName + ') berhasil dikirim.',
                            amount: 'Rp ' + Number(amount).toLocaleString('id-ID'),
                            orderId: resData.data?.payout_id || '#WD-XEN-' + Date.now(),
                            txType: 'Penarikan Bank Instan (Xendit)',
                            actionUrl: '{{ route("wallet.index") }}',
                            actionText: 'Muat Ulang Saldo Dompet'
                        });
                    } else {
                        alert('Penarikan dana via Xendit sebesar Rp ' + Number(amount).toLocaleString('id-ID') + ' sukses diajukan!');
                        window.location.reload();
                    }
                } else {
                    if (window.showPaymentFailed) {
                        window.showPaymentFailed({
                            title: 'Penarikan Dana Gagal',
                            message: resData.message || 'Gagal memproses transfer dana ke rekening tujuan.',
                            reason: resData.message
                        });
                    } else {
                        alert('Error: ' + resData.message);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.showPaymentFailed) {
                    window.showPaymentFailed({
                        title: 'Penarikan Dana Gagal',
                        message: 'Terjadi kesalahan sistem saat memproses penarikan Xendit.',
                        reason: 'Server or Network Exception'
                    });
                } else {
                    alert('Terjadi kesalahan saat memproses penarikan Xendit.');
                }
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
                    if (window.showPaymentSuccess) {
                        window.showPaymentSuccess({
                            title: 'Penarikan Kripto Berhasil Diajukan!',
                            message: 'Permintaan payout kripto sebesar ' + amount + ' ' + currency.toUpperCase() + ' ke address ' + address + ' telah diproses.',
                            amount: amount + ' ' + currency.toUpperCase(),
                            orderId: resData.data?.payout_id || '#WD-CRYPTO-' + Date.now(),
                            txType: 'Penarikan Kripto (NOWPayments)',
                            actionUrl: '{{ route("wallet.index") }}',
                            actionText: 'Muat Ulang Saldo Dompet'
                        });
                    } else {
                        alert('Penarikan Kripto via NOWPayments sebesar ' + amount + ' ' + currency.toUpperCase() + ' berhasil diajukan!');
                        window.location.reload();
                    }
                } else {
                    if (window.showPaymentFailed) {
                        window.showPaymentFailed({
                            title: 'Penarikan Kripto Gagal',
                            message: resData.message || 'Gagal memproses payout kripto.',
                            reason: resData.message
                        });
                    } else {
                        alert('Error: ' + resData.message);
                    }
                }
            } catch (err) {
                console.error(err);
                if (window.showPaymentFailed) {
                    window.showPaymentFailed({
                        title: 'Penarikan Kripto Gagal',
                        message: 'Terjadi kendala saat memproses penarikan kripto.',
                        reason: 'NOWPayments Payout API Exception'
                    });
                } else {
                    alert('Terjadi kesalahan saat memproses penarikan kripto.');
                }
            } finally {
                submitCryptoWdBtn.disabled = false;
                submitCryptoWdBtn.innerText = 'Process NOWPayments Instant Payout →';
            }
        });
    }
});
</script>
@endsection
