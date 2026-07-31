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
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Wallet Overview</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Manage your funds, track cash flow, and review recent transactions.</p>
        </div>
        <div class="flex items-center gap-3">
            <button @click="activeTab = 'deposit'" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                <span></span> Deposit Funds
            </button>
            <button @click="activeTab = 'withdraw'" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-4 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                <span></span> Withdraw Funds
            </button>
        </div>
    </div>

    <!-- 3 Summary Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: TOTAL BALANCE -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">TOTAL BALANCE</span>
                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">+2.4%</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    Rp {{ number_format($totalBalance, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Updated 5 mins ago</p>
            </div>
        </div>

        <!-- Card 2: AVAILABLE FUNDS -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">AVAILABLE FUNDS</span>
                <span class="text-xs font-bold text-emerald-700">Ready to invest</span>
            </div>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    Rp {{ number_format($availableBalance, 0, ',', '.') }}
                </p>
                <div class="flex gap-2 mt-2">
                    <button @click="activeTab = 'deposit'" class="py-1 px-3 rounded-lg bg-emerald-700 text-white text-[11px] font-bold hover:bg-emerald-800">
                        Deposit
                    </button>
                    <button @click="activeTab = 'withdraw'" class="py-1 px-3 rounded-lg border border-slate-200 bg-white text-slate-700 text-[11px] font-bold hover:bg-slate-50">
                        Withdraw
                    </button>
                </div>
            </div>
        </div>

        <!-- Card 3: FUNDS ON HOLD -->
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">FUNDS ON HOLD</span>
            <div class="mt-3">
                <p class="text-3xl font-black text-slate-900 tracking-tight">
                    Rp {{ number_format($holdBalance, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Pending escrows &amp; reserves</p>
            </div>
        </div>
    </div>

    <!-- Operation Tabs Header -->
    <div class="border-b border-slate-200 flex gap-6 text-xs font-bold text-slate-500">
        <button @click="activeTab = 'deposit'" 
                :class="activeTab === 'deposit' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Deposit Funds
        </button>
        <button @click="activeTab = 'withdraw'" 
                :class="activeTab === 'withdraw' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Withdraw Funds
        </button>
        <button @click="activeTab = 'history'" 
                :class="activeTab === 'history' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Transaction History
        </button>
    </div>

    <!-- ─── Tab 1: Deposit Funds ─────────────────────────────────────────── -->
    <div x-show="activeTab === 'deposit'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Form Left Side (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Select Funding Source &amp; Amount</h3>

            <!-- 1. Funding Source Cards -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. Funding Source</label>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3">
                    <div class="p-3.5 rounded-xl border border-emerald-700 bg-emerald-50/50 flex flex-col justify-between cursor-pointer">
                        <span class="text-xs font-bold text-slate-900 block">Bank Transfer</span>
                        <span class="text-[10px] text-emerald-800 font-semibold mt-1">Midtrans / Virtual Account</span>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 opacity-60 flex flex-col justify-between cursor-pointer">
                        <span class="text-xs font-bold text-slate-700 block">Wire Transfer</span>
                        <span class="text-[10px] text-slate-400 font-medium mt-1">Domestic / Intl</span>
                    </div>
                    <div class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 opacity-60 flex flex-col justify-between cursor-pointer">
                        <span class="text-xs font-bold text-slate-700 block">Crypto Deposit</span>
                        <span class="text-[10px] text-slate-400 font-medium mt-1">USDC / USDT</span>
                    </div>
                </div>
            </div>

            <!-- 2. Deposit Amount Form -->
            <form id="deposit-form" action="{{ route('wallet.deposit') }}" method="POST" class="space-y-4 pt-2">
                @csrf
                <div>
                    <label for="dep_currency_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Select Currency</label>
                    <select name="currency_id" id="dep_currency_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}" data-code="{{ $curr->code }}">{{ $curr->code }} - {{ $curr->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="dep_amount" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">2. Amount to Deposit (IDR)</label>
                    <input type="number" name="amount" id="dep_amount" required min="10000" step="50000"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="e.g. 500000">
                </div>

                <button type="submit" id="submit-deposit-btn"
                        class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Submit Deposit &rarr;
                </button>
            </form>
        </div>

        <!-- Deposit Info Right Side (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <!-- Deposit Limits Box -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">DEPOSIT LIMITS</span>
                
                <div class="space-y-2 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100 font-medium">
                        <span class="text-slate-500">Daily Limit</span>
                        <span class="font-bold text-slate-900">Rp 100.000.000</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100 font-medium">
                        <span class="text-slate-500">Monthly Limit</span>
                        <span class="font-bold text-slate-900">Rp 500.000.000</span>
                    </div>
                </div>

                <div class="p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-[11px] text-emerald-900 font-medium">
                    <strong>Expected Processing Time:</strong> Instant settlement for Bank Transfer via Midtrans Virtual Account.
                </div>
            </div>
        </div>

    </div>

    <!-- ─── Tab 2: Withdraw Funds ───────────────────────────────────────── -->
    <div x-show="activeTab === 'withdraw'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" style="display: none;">
        
        <!-- Form Left Side (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Transfer available balance to connected bank account</h3>

            <form action="{{ route('wallet.withdraw') }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- 1. Destination Bank Account -->
                <div class="space-y-2">
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">1. Destination Bank Account</label>
                    <select name="bank_account" class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                        <option value="bca">Bank BCA (**** 8492) - Bintang Ridwan</option>
                        <option value="mandiri">Bank Mandiri (**** 1204) - Bintang Ridwan</option>
                    </select>
                    <p class="text-[11px] text-slate-400 font-medium">Standard ACH/Bank transfers typically take 1-3 business days.</p>
                </div>

                <!-- 2. Withdrawal Details -->
                <div class="space-y-3">
                    <div class="flex items-center justify-between">
                        <label for="wd_amount" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">2. Withdrawal Details</label>
                        <span class="text-xs font-bold text-emerald-700 cursor-pointer" onclick="document.getElementById('wd_amount').value = '{{ (int)$availableBalance }}'">
                            Withdraw Max: Rp {{ number_format($availableBalance, 0, ',', '.') }}
                        </span>
                    </div>
                    
                    <select name="currency_id" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none mb-2">
                        @foreach($currencies as $curr)
                            <option value="{{ $curr->id }}">{{ $curr->code }} - {{ $curr->name }}</option>
                        @endforeach
                    </select>

                    <input type="number" name="amount" id="wd_amount" required min="10000" step="50000"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600"
                           placeholder="Enter amount to withdraw...">
                </div>

                <button type="submit" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Continue to Verification &rarr;
                </button>
            </form>
        </div>

        <!-- Withdraw Summary Right Side (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-4">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">SUMMARY</span>

                <div class="space-y-2.5 text-xs font-medium">
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Current Balance</span>
                        <span class="font-bold text-slate-900">Rp {{ number_format($availableBalance, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between py-1 border-b border-slate-100">
                        <span class="text-slate-500">Processing Fee (ACH)</span>
                        <span class="font-bold text-emerald-700">Rp 0 (Free)</span>
                    </div>
                </div>

                <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 text-[11px]">
                    <span class="text-slate-400 font-bold uppercase tracking-wider block mb-1">ESTIMATED ARRIVAL</span>
                    <span class="font-bold text-slate-900 block">1-3 Business Days</span>
                </div>
            </div>
        </div>

    </div>

    <!-- ─── Tab 3: Transaction History ──────────────────────────────────── -->
    <div x-show="activeTab === 'history'" class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden" style="display: none;">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Transaction Ledger</h3>
            <span class="text-xs font-medium text-slate-500">Total: {{ $transactions->total() }} Records</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">Transaction</th>
                        <th class="py-3.5 px-6">Amount</th>
                        <th class="py-3.5 px-6">Balances (Before / After)</th>
                        <th class="py-3.5 px-6 text-right">Date</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-8 w-8 rounded-lg font-bold flex items-center justify-center text-xs uppercase
                                    @if(in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund'])) bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                    {{ substr($tx->type, 0, 3) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs capitalize">{{ str_replace('_', ' ', $tx->type) }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium block truncate max-w-[240px]">{{ $tx->description }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-extrabold text-sm">
                            <span class="{{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? 'text-emerald-700' : 'text-rose-600' }}">
                                {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? '+' : '-' }}
                                Rp {{ number_format($tx->amount, 0, ',', '.') }}
                            </span>
                        </td>
                        <td class="py-4 px-6 text-[11px] text-slate-500">
                            <div>Before: Rp {{ number_format($tx->balance_before, 0, ',', '.') }}</div>
                            <div class="font-bold text-slate-800 mt-0.5">After: Rp {{ number_format($tx->balance_after, 0, ',', '.') }}</div>
                        </td>
                        <td class="py-4 px-6 text-right font-semibold text-slate-500">
                            {{ $tx->created_at->format('M d, Y H:i') }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="py-10 text-center text-slate-400 text-xs font-medium">
                            No transaction ledger records found.
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

<!-- Deposit Midtrans Integration JS -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('deposit-form');
    const submitBtn = document.getElementById('submit-deposit-btn');

    if (form) {
        form.addEventListener('submit', async (e) => {
            const selectEl = document.getElementById('dep_currency_id');
            const selectedOption = selectEl.options[selectEl.selectedIndex];
            const currencyCode = selectedOption.getAttribute('data-code');

            if (currencyCode === 'IDR') {
                e.preventDefault();
                submitBtn.disabled = true;
                submitBtn.innerText = 'Initiating payment...';

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
                            onSuccess: function(result) {
                                window.location.href = '{{ route("wallet.index") }}?status=success&msg=Payment settled!';
                            },
                            onPending: function(result) {
                                window.location.href = '{{ route("wallet.index") }}?status=pending&msg=Payment pending.';
                            },
                            onError: function(result) {
                                window.location.href = '{{ route("wallet.index") }}?status=error&msg=Payment failed.';
                            },
                            onClose: function() {
                                submitBtn.disabled = false;
                                submitBtn.innerText = 'Submit Deposit →';
                            }
                        });
                    } else {
                        alert('Error: ' + resData.message);
                        submitBtn.disabled = false;
                        submitBtn.innerText = 'Submit Deposit →';
                    }
                } catch (err) {
                    console.error(err);
                    alert('An unexpected error occurred. Please try again.');
                    submitBtn.disabled = false;
                    submitBtn.innerText = 'Submit Deposit →';
                }
            }
        });
    }
});
</script>
@endsection
