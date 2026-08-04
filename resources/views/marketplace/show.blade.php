@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{
    showConfirmModal: false,
    investAmount: {{ old('amount', 500000) }},
    get estMonthlyReturn() {
        return Math.round((this.investAmount * ({{ $loan->interest_rate }} / 100)) / 12);
    },
    get totalEstReturn() {
        return Math.round((this.investAmount * ({{ $loan->interest_rate }} / 100)) * ({{ $loan->duration }} / 12));
    },
    get totalPayout() {
        return Math.round(parseFloat(this.investAmount) + this.totalEstReturn);
    }
}">
    
    <!-- Top Back Navigation -->
    <div>
        <a href="{{ route('marketplace.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
            &larr; Back to Marketplace
        </a>
    </div>

    <!-- SUCCESS INVESTMENT BANNER (If query param status=success) -->
    @if(request('status') === 'success' || session('success'))
    <div class="rounded-2xl border border-emerald-200 bg-white p-8 text-center shadow-xs space-y-4 max-w-2xl mx-auto">
        <div class="mx-auto h-16 w-16 rounded-full bg-emerald-100 text-emerald-700 font-bold flex items-center justify-center text-3xl shadow-xs">
            
        </div>
        <h2 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Investment Confirmed</h2>
        <p class="text-xs text-slate-600 font-medium">
            You have successfully invested capital in <strong>{{ $loan->purpose }}</strong> (#LN-{{ substr($loan->id, 0, 8) }}).
        </p>

        <div class="rounded-xl bg-slate-50 border border-slate-200 p-4 text-xs space-y-2 text-left">
            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                <span class="text-slate-500">Transaction ID</span>
                <span class="font-bold text-slate-900">TRX-{{ strtoupper(substr($loan->id, 0, 8)) }}-ABC</span>
            </div>
            <div class="flex justify-between border-b border-slate-200 pb-1.5">
                <span class="text-slate-500">Date &amp; Time</span>
                <span class="font-bold text-slate-900">{{ now()->format('M d, Y H:i') }} EST</span>
            </div>
            <div class="flex justify-between">
                <span class="text-slate-500">Source Wallet</span>
                <span class="font-bold text-slate-900">Main Wallet (IDR)</span>
            </div>
        </div>

        <div class="flex justify-center gap-3 pt-2">
            <a href="{{ route('dashboard') }}" class="py-2.5 px-5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs">
                View in My Investments
            </a>
            <a href="{{ route('marketplace.index') }}" class="py-2.5 px-5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50">
                Back to Marketplace
            </a>
        </div>
        <p class="text-[10px] text-slate-400 font-medium">Your funds are held in escrow until the loan is fully funded.</p>
    </div>
    @endif

    <!-- Header Title & Loan Target Banner -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2">
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-emerald-100 text-emerald-800 border border-emerald-200">
                        {{ $loan->category->name }}
                    </span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-slate-100 text-slate-800 border border-slate-200">
                        RISK GRADE {{ $loan->risk_grade }}
                    </span>
                </div>
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight mt-1">{{ $loan->purpose }}</h1>
                <p class="text-xs text-slate-500 font-medium mt-0.5">Application ID: #LN-{{ substr($loan->id, 0, 8) }} • {{ $loan->duration }} Months Term</p>
            </div>
            <div class="text-left sm:text-right">
                <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">TARGET LOAN AMOUNT</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- Funding Progress Bar -->
        <div class="space-y-1.5 pt-2 border-t border-slate-100">
            <div class="flex items-center justify-between text-xs font-bold">
                <span class="text-emerald-700">{{ (int)$loan->funded_percentage }}% Funded (Rp {{ number_format($loan->fundings()->sum('amount'), 0, ',', '.') }})</span>
                <span class="text-slate-400">Rp {{ number_format(max(0, $loan->amount - $loan->fundings()->sum('amount')), 0, ',', '.') }} Remaining</span>
            </div>
            <div class="w-full bg-slate-100 rounded-full h-2 overflow-hidden">
                <div class="bg-emerald-700 h-2 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
            </div>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: Key Terms, Security, Use of Funds & Borrower Overview (Spans 8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Key Terms & Security Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <!-- Key Terms Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Key Terms</h3>
                    <div class="space-y-2 text-xs font-medium">
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Interest Rate (Fixed)</span>
                            <span class="font-bold text-emerald-700">{{ $loan->interest_rate }}% APR</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Term Length</span>
                            <span class="font-bold text-slate-900">{{ $loan->duration }} Months</span>
                        </div>
                        <div class="flex justify-between py-1 border-b border-slate-100">
                            <span class="text-slate-500">Payment Frequency</span>
                            <span class="font-bold text-slate-900">Monthly Interest</span>
                        </div>
                        <div class="flex justify-between py-1">
                            <span class="text-slate-500">LTV Ratio</span>
                            <span class="font-bold text-slate-900">{{ $loan->initial_ltv ?? 65 }}%</span>
                        </div>
                    </div>
                </div>

                <!-- Security Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Security</h3>
                    <div class="space-y-2 text-xs">
                        <div class="p-2.5 rounded-xl bg-slate-50 border border-slate-200">
                            <span class="font-bold text-slate-900 block text-xs">Senior Secured First Lien</span>
                            <span class="text-[10px] text-slate-500 font-medium block mt-0.5">Secured against business asset portfolio.</span>
                        </div>
                        @if($loan->isCryptoLoan())
                        <div class="p-2.5 rounded-xl bg-emerald-50 border border-emerald-200">
                            <span class="font-bold text-emerald-950 block text-xs">Crypto Collateral Locked</span>
                            <span class="text-[10px] text-emerald-800 font-medium block mt-0.5">
                                {{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }} in cold storage.
                            </span>
                        </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Use of Funds -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-3">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Use of Funds</h3>
                <p class="text-xs text-slate-600 font-medium leading-relaxed">
                    {{ $loan->description ?: 'Capital will be deployed immediately to expand business operations, fund working capital, and optimize supply chain logistics.' }}
                </p>
            </div>

            <!-- Borrower Overview -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-2">Borrower Overview</h3>
                <div class="flex items-start gap-4">
                    <div class="h-12 w-12 rounded-2xl bg-slate-900 text-white font-black flex items-center justify-center text-sm shadow-xs">
                        {{ strtoupper(substr($loan->borrower->profile->full_name ?? 'Client', 0, 2)) }}
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-slate-900">{{ $loan->borrower->profile->full_name ?? 'Institutional Borrower' }}</h4>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">
                            Verified {{ $loan->borrower->profile->occupation ?? 'Enterprise Client' }} with pristine credit history.
                        </p>
                    </div>
                </div>

                <!-- Borrower Metrics Grid -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-center pt-2">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Credit Rating</span>
                        <span class="text-xs font-black text-emerald-700 block mt-0.5">780 / Excellent</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Previous Loans</span>
                        <span class="text-xs font-black text-slate-900 block mt-0.5">4 Completed</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Total Borrowed</span>
                        <span class="text-xs font-black text-slate-900 block mt-0.5">Rp 2.4B</span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Defaults</span>
                        <span class="text-xs font-black text-emerald-700 block mt-0.5">0</span>
                    </div>
                </div>
            </div>

        </div>

        <!-- Right Side: Invest Action Card (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4 sticky top-20">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-5">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-sm font-bold text-slate-900">Invest</h3>
                    <span class="text-[11px] font-bold text-slate-400">Min. Rp 100.000</span>
                </div>

                @if(Auth::id() === $loan->borrower_id)
                    <div class="p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs text-amber-900 font-semibold">
                        You cannot invest in your own loan applications.
                    </div>
                @else
                    <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">AMOUNT TO INVEST (IDR)</label>
                            <input type="number" name="amount" x-model="investAmount" required min="100000" step="50000"
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                            
                            <!-- Presets -->
                            <div class="flex gap-2 mt-2">
                                <button type="button" @click="investAmount = 500000" class="py-1 px-2.5 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-slate-50">Rp 500K</button>
                                <button type="button" @click="investAmount = 1000000" class="py-1 px-2.5 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-slate-50">Rp 1M</button>
                                <button type="button" @click="investAmount = 5000000" class="py-1 px-2.5 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-slate-50">Rp 5M</button>
                                <button type="button" @click="investAmount = {{ max(100000, $loan->amount - $loan->fundings()->sum('amount')) }}" class="py-1 px-2.5 rounded-lg border border-slate-200 text-[10px] font-bold text-slate-600 hover:bg-slate-50">Max</button>
                            </div>
                        </div>

                        <!-- Financial Projections Card -->
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 space-y-2 text-xs font-medium">
                            <div class="flex justify-between">
                                <span class="text-slate-500">Est. Monthly Return</span>
                                <span class="font-bold text-emerald-700">+Rp <span x-text="estMonthlyReturn.toLocaleString('id-ID')"></span></span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-slate-500">Total Est. Return ({{ $loan->duration }} mo)</span>
                                <span class="font-bold text-emerald-700">+Rp <span x-text="totalEstReturn.toLocaleString('id-ID')"></span></span>
                            </div>
                            <div class="flex justify-between border-t border-slate-200 pt-2 text-xs">
                                <span class="font-bold text-slate-800">TOTAL EST. PAYOUT</span>
                                <span class="font-black text-slate-900">Rp <span x-text="totalPayout.toLocaleString('id-ID')"></span></span>
                            </div>
                        </div>

                        <button type="button" @click="showConfirmModal = true" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                            INVEST NOW &rarr;
                        </button>
                    </form>
                @endif

                <button onclick="window.print()" class="w-full py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors">
                    DOWNLOAD TERM SHEET
                </button>
            </div>
        </div>

    </div>

    <!-- ─── CONFIRM INVESTMENT MODAL ────────────────────────────────────── -->
    <div x-show="showConfirmModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" style="display: none;">
        <div class="w-full max-w-md rounded-2xl bg-white p-6 shadow-2xl space-y-5 border border-slate-200">
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-sm font-extrabold text-slate-900">Invest in {{ $loan->purpose }}</h3>
                <button @click="showConfirmModal = false" class="text-slate-400 hover:text-slate-600 text-lg">&times;</button>
            </div>

            <!-- Loan Summary Banner -->
            <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-between text-xs">
                <div>
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ID: #LN-{{ substr($loan->id, 0, 8) }}</span>
                    <span class="font-extrabold text-emerald-700 block mt-0.5">{{ $loan->interest_rate }}% APR</span>
                </div>
                <div class="text-right">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Term</span>
                    <span class="font-bold text-slate-900 block mt-0.5">{{ $loan->duration }} Months</span>
                </div>
            </div>

            <!-- Investment Amount Field -->
            <div class="space-y-2">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">Investment Amount</label>
                <div class="p-3 rounded-xl border border-slate-200 bg-slate-50 text-sm font-black text-slate-900">
                    Rp <span x-text="parseInt(investAmount).toLocaleString('id-ID')"></span>
                </div>
            </div>

            <!-- Financial Projections Box -->
            <div class="p-3.5 rounded-xl bg-emerald-50/50 border border-emerald-200 space-y-2 text-xs font-medium">
                <div class="flex justify-between">
                    <span class="text-slate-600">Estimated Monthly Interest</span>
                    <span class="font-bold text-emerald-800">+Rp <span x-text="estMonthlyReturn.toLocaleString('id-ID')"></span></span>
                </div>
                <div class="flex justify-between border-t border-emerald-200/60 pt-1.5">
                    <span class="font-bold text-emerald-950">Total Return at Maturity</span>
                    <span class="font-black text-emerald-800">Rp <span x-text="totalPayout.toLocaleString('id-ID')"></span></span>
                </div>
            </div>

            <!-- Agreement Checkbox -->
            <div class="flex items-start gap-2.5 text-xs text-slate-600 font-medium">
                <input type="checkbox" id="agree_terms" checked required class="mt-0.5 accent-emerald-700 rounded">
                <label for="agree_terms" class="text-[11px] leading-tight">
                    I agree to the <a href="#" class="font-bold text-emerald-700">Loan Participation Agreement</a> and acknowledge the risks associated with this investment.
                </label>
            </div>

            <!-- Actions -->
            <div class="flex gap-3 pt-2">
                <button type="button" @click="showConfirmModal = false" class="w-1/2 py-2.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Cancel
                </button>
                <form action="{{ route('marketplace.fund', $loan->id) }}" method="POST" class="w-1/2">
                    @csrf
                    <input type="hidden" name="amount" :value="investAmount">
                    <button type="submit" class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        Confirm Investment &rarr;
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
