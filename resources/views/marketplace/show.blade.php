@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    
    <!-- Navigation Back -->
    <div>
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
            &larr; Back to Marketplace
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Detailed Statistics (Spans 2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Core Loan Details -->
            <div class="overflow-hidden shadow-xs rounded-2xl border border-slate-200 bg-white">
                <div class="px-6 py-5 border-b border-slate-100 bg-slate-50/50">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">{{ $loan->purpose }}</h2>
                            <p class="text-xs text-slate-500 mt-1 font-medium">Application ID: #LN-{{ substr($loan->id, 0, 8) }} • Category: {{ $loan->category->name }}</p>
                        </div>
                        <span class="inline-flex items-center rounded px-2.5 py-0.5 text-xs font-extrabold uppercase tracking-wider
                            @if($loan->risk_grade === 'A') bg-emerald-100 text-emerald-800 border border-emerald-200
                            @elseif($loan->risk_grade === 'B') bg-blue-100 text-blue-800 border border-blue-200
                            @elseif($loan->risk_grade === 'C') bg-amber-100 text-amber-800 border border-amber-200
                            @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                            Grade {{ $loan->risk_grade }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-6 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Target Capital</span>
                            <span class="text-xl font-extrabold text-slate-900 mt-1 block">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Annual Return (APR)</span>
                            <span class="text-xl font-extrabold text-emerald-700 mt-1 block">{{ $loan->interest_rate }}%</span>
                        </div>
                        <div>
                            <span class="block text-[11px] font-bold uppercase tracking-wider text-slate-400">Loan Duration</span>
                            <span class="text-xl font-extrabold text-slate-900 mt-1 block">{{ $loan->duration }} Months</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="border-t border-slate-100 pt-6">
                        <h4 class="text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">Loan Description</h4>
                        <p class="text-xs text-slate-600 leading-relaxed whitespace-pre-line font-medium">{{ $loan->description ?: 'No detailed description provided by the borrower.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Collateral / DeFi Security parameters (only displayed if Crypto loan) -->
            @if($loan->isCryptoLoan())
                <div class="overflow-hidden shadow-xs rounded-2xl border border-emerald-200 bg-emerald-50/30 p-6">
                    <div class="flex items-center gap-3 border-b border-emerald-200/60 pb-3 mb-4">
                        <div class="h-8 w-8 rounded-lg bg-emerald-700 text-white font-black flex items-center justify-center text-xs uppercase shadow-xs">
                            {{ $loan->collateralCurrency->code }}
                        </div>
                        <h3 class="text-sm font-bold text-emerald-950">DeFi Smart Collateral Security</h3>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center">
                        <div class="bg-white rounded-xl p-3 border border-emerald-100 shadow-xs">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Collateral Locked</span>
                            <span class="text-xs font-extrabold text-slate-900 mt-1 block">{{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }}</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-emerald-100 shadow-xs">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Initial LTV</span>
                            <span class="text-xs font-extrabold text-slate-900 mt-1 block">{{ $loan->initial_ltv }}%</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-emerald-100 shadow-xs">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Liquidation LTV</span>
                            <span class="text-xs font-extrabold text-rose-600 mt-1 block">{{ $loan->liquidation_ltv }}%</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-emerald-100 shadow-xs">
                            <span class="block text-[10px] font-bold uppercase tracking-wider text-slate-400">Liquidation Price</span>
                            <span class="text-xs font-extrabold text-rose-600 mt-1 block">Rp {{ number_format($loan->liquidation_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Side: Funding Actions & Investor form (Spans 1 column) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Investment Form Card -->
            <div class="overflow-hidden shadow-xs rounded-2xl border border-slate-200 bg-white p-6">
                <h3 class="text-sm font-bold text-slate-900 mb-4 border-b border-slate-100 pb-3">Fund This Loan</h3>

                <div class="space-y-4">
                    <!-- Progress summary -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-bold mb-1.5">
                            <span class="text-emerald-700">{{ (int)$loan->funded_percentage }}% funded</span>
                            <span class="text-slate-500">Rp {{ number_format($loan->fundings()->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                            <div class="bg-emerald-700 h-2 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                        </div>
                    </div>

                    <div class="text-xs text-slate-500 border-t border-slate-100 pt-3 flex justify-between font-medium">
                        <span>Total Target:</span>
                        <strong class="text-slate-900 font-extrabold">Rp {{ number_format($loan->amount, 0, ',', '.') }}</strong>
                    </div>

                    @if(Auth::id() === $loan->borrower_id)
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800 font-semibold">
                            You cannot invest in your own loan applications.
                        </div>
                    @else
                        <!-- Form -->
                        <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="space-y-3 pt-2">
                            @csrf
                            <div>
                                <label for="amount" class="block text-[11px] font-bold uppercase tracking-wider text-slate-500">Investment Amount (IDR)</label>
                                <input type="number" name="amount" id="amount" required min="100000" step="50000"
                                       class="mt-1.5 block w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 @error('amount') border-rose-300 text-rose-900 @enderror"
                                       placeholder="e.g. 500000">
                                @error('amount')
                                    <p class="mt-1.5 text-xs text-rose-600 font-bold">{{ $message }}</p>
                                @errorEnd
                            </div>
                            <button type="submit"
                                    class="w-full rounded-xl bg-emerald-700 px-4 py-2.5 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-colors">
                                Deploy Capital &rarr;
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Risk Disclosure -->
            <div class="rounded-2xl border border-slate-200 bg-slate-50 p-4 text-xs text-slate-500 leading-relaxed font-medium">
                <h4 class="font-bold text-slate-800 mb-1">Risk Warning</h4>
                P2P lending involves financial risks. Borrower repayments are not guaranteed unless secured by collateral assets. Past performance is not a guarantee of future outcomes.
            </div>

        </div>

    </div>

</div>
@endsection
