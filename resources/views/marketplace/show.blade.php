@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Navigation Back -->
    <div class="mb-6">
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to marketplace
        </a>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Detailed Statistics (Spans 2 columns) -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Core Loan Details -->
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
                <div class="px-6 py-6 sm:px-8 border-b border-gray-150 bg-gray-50/70">
                    <div class="flex items-center justify-between flex-wrap gap-2">
                        <div>
                            <h2 class="text-xl font-bold text-gray-900">{{ $loan->purpose }}</h2>
                            <p class="text-xs text-gray-500 mt-1">Application ID: {{ $loan->id }} • Category: {{ $loan->category->name }}</p>
                        </div>
                        <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold uppercase tracking-wider
                            @if($loan->risk_grade === 'A') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                            @elseif($loan->risk_grade === 'B') bg-blue-50 text-blue-700 ring-1 ring-blue-600/10
                            @elseif($loan->risk_grade === 'C') bg-amber-50 text-amber-700 ring-1 ring-amber-600/10
                            @else bg-red-50 text-red-700 ring-1 ring-red-600/10 @endif">
                            Grade {{ $loan->risk_grade }}
                        </span>
                    </div>
                </div>

                <div class="px-6 py-6 sm:px-8 space-y-6">
                    <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Target Capital</span>
                            <span class="text-xl font-extrabold text-gray-950">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Annual Return (APR)</span>
                            <span class="text-xl font-extrabold text-emerald-600">{{ $loan->interest_rate }}%</span>
                        </div>
                        <div>
                            <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Loan Duration</span>
                            <span class="text-xl font-extrabold text-gray-950">{{ $loan->duration }} Months</span>
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="border-t border-gray-100 pt-6">
                        <h4 class="text-xs font-semibold uppercase tracking-wider text-gray-600 mb-2">Loan Description</h4>
                        <p class="text-sm text-gray-600 leading-relaxed whitespace-pre-line">{{ $loan->description ?: 'No detailed description provided by the borrower.' }}</p>
                    </div>
                </div>
            </div>

            <!-- Collateral / DeFi Security parameters (only displayed if Crypto loan) -->
            @if($loan->isCryptoLoan())
                <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-indigo-100 bg-indigo-50/20 p-6 sm:p-8">
                    <div class="flex items-center gap-3 border-b border-indigo-100 pb-3 mb-6">
                        <div class="h-8 w-8 rounded-lg bg-indigo-600 text-white font-bold flex items-center justify-center text-sm uppercase">
                            {{ $loan->collateralCurrency->code }}
                        </div>
                        <h3 class="text-base font-bold text-indigo-900">DeFi Smart Collateral Security</h3>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 text-center">
                        <div class="bg-white rounded-xl p-3 border border-indigo-100/50">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Collateral Locked</span>
                            <span class="text-sm font-bold text-gray-900 mt-1 block">{{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }}</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-indigo-100/50">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Initial LTV</span>
                            <span class="text-sm font-bold text-gray-900 mt-1 block">{{ $loan->initial_ltv }}%</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-indigo-100/50">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Liquidation LTV</span>
                            <span class="text-sm font-bold text-red-600 mt-1 block">{{ $loan->liquidation_ltv }}%</span>
                        </div>
                        <div class="bg-white rounded-xl p-3 border border-indigo-100/50">
                            <span class="block text-[10px] font-semibold uppercase tracking-wider text-gray-400">Liquidation Price</span>
                            <span class="text-sm font-bold text-red-600 mt-1 block">Rp {{ number_format($loan->liquidation_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif

        </div>

        <!-- Right Side: Funding Actions & Investor form (Spans 1 column) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Investment Form Card -->
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4 border-b border-gray-50 pb-3">Fund this loan</h3>

                <div class="space-y-4">
                    <!-- Progress summary -->
                    <div>
                        <div class="flex items-center justify-between text-xs font-semibold mb-1">
                            <span class="text-indigo-600">{{ $loan->funded_percentage }}% funded</span>
                            <span class="text-gray-400">Rp {{ number_format($loan->fundings()->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                        </div>
                    </div>

                    <div class="text-xs text-gray-500 border-t border-gray-50 pt-4 flex justify-between">
                        <span>Total Target:</span>
                        <strong class="text-gray-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</strong>
                    </div>

                    @if(Auth::id() === $loan->borrower_id)
                        <div class="rounded-xl bg-amber-50 border border-amber-200 p-3 text-xs text-amber-800">
                            You cannot invest in your own loan applications.
                        </div>
                    @else
                        <!-- Form -->
                        <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="space-y-3 pt-2">
                            @csrf
                            <div>
                                <label for="amount" class="block text-[10px] font-semibold uppercase tracking-wider text-gray-500">Investment Amount (IDR)</label>
                                <input type="number" name="amount" id="amount" required min="100000"
                                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                                       placeholder="e.g. 500000">
                                @error('amount')
                                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                                @enderror
                            </div>
                            <button type="submit"
                                    class="w-full rounded-xl bg-indigo-600 px-4 py-2.5 text-xs font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-colors">
                                Deploy Capital
                            </button>
                        </form>
                    @endif
                </div>
            </div>

            <!-- Risk Disclosure -->
            <div class="rounded-2xl border border-gray-200 bg-gray-50 p-4 text-xs text-gray-500 leading-relaxed">
                <h4 class="font-bold text-gray-700 mb-1">Risk Warning</h4>
                P2P lending involves high financial risks. Diversify your investments. Borrower repayments are not guaranteed unless secured by collateral assets. Past performance is not a guarantee of future outcomes.
            </div>

        </div>

    </div>

</div>
@endsection
