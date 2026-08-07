@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{
    amount: {{ old('amount', 250000000) }},
    duration: {{ old('duration', 24) }},
    rate: {{ old('interest_rate', 12.0) }},
    get monthlyPayment() {
        let totalInterest = (this.amount * (this.rate / 100) * (this.duration / 12));
        let totalRepay = parseFloat(this.amount) + parseFloat(totalInterest);
        return Math.round(totalRepay / this.duration);
    },
    get totalInterest() {
        return Math.round(this.amount * (this.rate / 100) * (this.duration / 12));
    },
    get totalRepayment() {
        return Math.round(parseFloat(this.amount) + this.totalInterest);
    }
}">
    
    <!-- Top Header Bar -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Calculate &amp; Apply</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">Configure your loan parameters to see estimated terms before applying.</p>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Side: Loan Details Parameters (Spans 7 Cols) -->
        <div class="lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
            <h3 class="text-sm font-bold text-slate-900 border-b border-slate-100 pb-3">Loan Details</h3>

            <form id="loanForm" action="{{ route('loans.store') }}" method="POST" class="space-y-6">
                @csrf

                <!-- LOAN PURPOSE -->
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">LOAN PURPOSE</label>
                    <div class="grid grid-cols-3 gap-3">
                        <label class="p-3.5 rounded-xl border border-emerald-700 bg-emerald-50/50 flex flex-col items-center justify-center text-center cursor-pointer">
                            <input type="radio" name="purpose_type" value="Business Exp" checked class="sr-only">
                            <span class="text-lg mb-1"></span>
                            <span class="text-xs font-bold text-slate-900">Business Exp</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center text-center cursor-pointer hover:border-slate-300">
                            <input type="radio" name="purpose_type" value="Inventory" class="sr-only">
                            <span class="text-lg mb-1"></span>
                            <span class="text-xs font-bold text-slate-700">Inventory</span>
                        </label>
                        <label class="p-3.5 rounded-xl border border-slate-200 bg-slate-50/50 flex flex-col items-center justify-center text-center cursor-pointer hover:border-slate-300">
                            <input type="radio" name="purpose_type" value="Equipment" class="sr-only">
                            <span class="text-lg mb-1"></span>
                            <span class="text-xs font-bold text-slate-700">Equipment</span>
                        </label>
                    </div>
                </div>

                <!-- Loan Purpose Text -->
                <div>
                    <label for="purpose" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Loan Title / Specific Purpose</label>
                    <input type="text" name="purpose" id="purpose" required value="{{ old('purpose', 'Business Expansion & Operating Capital') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <!-- Category & Risk Grade -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="category_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Loan Category</label>
                        <select name="category_id" id="category_id" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800 outline-none">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="risk_grade" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Risk Grade</label>
                        <select name="risk_grade" id="risk_grade" required class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-800 outline-none">
                            <option value="A">Grade A (Low Risk: 8% - 10%)</option>
                            <option value="B" selected>Grade B (Medium Risk: 11% - 14%)</option>
                            <option value="C">Grade C (High Risk: 15% - 18%)</option>
                            <option value="D">Grade D (Very High Risk: 19% - 24%)</option>
                        </select>
                    </div>
                </div>

                <!-- LOAN AMOUNT Range Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">LOAN AMOUNT (IDR)</label>
                        <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1">
                            <span class="text-xs font-bold text-slate-500">Rp</span>
                            <input type="number" name="amount" x-model="amount" required min="1000000" max="1000000000" step="any"
                                   class="text-xs font-extrabold text-slate-900 bg-transparent outline-none text-right w-28">
                        </div>
                    </div>
                    <input type="range" min="10000000" max="1000000000" step="10000000" x-model="amount" class="w-full accent-emerald-700">
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-1">
                        <span>Rp 10M</span>
                        <span>Rp 1B</span>
                    </div>
                </div>

                <!-- REPAYMENT TERM Slider -->
                <div>
                    <div class="flex items-center justify-between mb-1.5">
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider">REPAYMENT TERM (MONTHS)</label>
                        <div class="flex items-center gap-1 bg-slate-50 border border-slate-200 rounded-xl px-3 py-1">
                            <input type="number" name="duration" x-model="duration" required min="3" max="60"
                                   class="text-xs font-extrabold text-slate-900 bg-transparent outline-none text-right w-12">
                            <span class="text-xs font-bold text-slate-500">mo</span>
                        </div>
                    </div>
                    <input type="range" min="6" max="60" step="6" x-model="duration" class="w-full accent-emerald-700">
                    <div class="flex justify-between text-[10px] font-bold text-slate-400 mt-1">
                        <span>6 mo</span>
                        <span>60 mo</span>
                    </div>
                </div>

                <!-- Proposed Interest Rate & Crypto Collateral -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label for="interest_rate" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Proposed Interest Rate (% APR)</label>
                        <input type="number" step="0.1" name="interest_rate" id="interest_rate" x-model="rate" required
                               class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                    </div>

                    <div>
                        <label for="collateral_currency_id" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Crypto Collateral (Optional)</label>
                        <select name="collateral_currency_id" id="collateral_currency_id" class="w-full rounded-xl border border-slate-200 px-3 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                            <option value="">None (Unsecured / Fiat Loan)</option>
                            @foreach($cryptoCurrencies as $crypto)
                                <option value="{{ $crypto->id }}">{{ $crypto->code }} - DeFi Collateral (LTV 50%)</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Detailed Description -->
                <div>
                    <label for="description" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Detailed Description</label>
                    <textarea name="description" id="description" rows="3" placeholder="Provide details about your project or funding requirements..." class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-emerald-600"></textarea>
                </div>
            </form>
        </div>

        <!-- Right Side: Estimated Summary Box (Spans 5 Cols) -->
        <div class="lg:col-span-5 space-y-4 sticky top-20">
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-6">
                
                <!-- Monthly Payment Banner -->
                <div class="text-center p-4 rounded-xl bg-slate-50 border border-slate-100">
                    <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">ESTIMATED MONTHLY PAYMENT</span>
                    <span class="text-3xl font-black text-emerald-700 block mt-1 tracking-tight">
                        Rp <span x-text="monthlyPayment.toLocaleString('id-ID')">11.350.000</span>
                    </span>
                </div>

                <!-- Financial Breakdown -->
                <div class="space-y-3 text-xs font-medium">
                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">Principal Amount</span>
                        <span class="font-bold text-slate-900">Rp <span x-text="parseInt(amount).toLocaleString('id-ID')">250.000.000</span></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">Estimated APR Info</span>
                        <span class="font-bold text-emerald-700"><span x-text="rate">8.25</span>%</span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-500">Total Interest</span>
                        <span class="font-bold text-slate-900">Rp <span x-text="totalInterest.toLocaleString('id-ID')">22.400.000</span></span>
                    </div>
                    <div class="flex justify-between py-1.5 border-b border-slate-100 text-sm">
                        <span class="font-bold text-slate-800">Total Repayment</span>
                        <span class="font-black text-slate-900">Rp <span x-text="totalRepayment.toLocaleString('id-ID')">272.400.000</span></span>
                    </div>
                </div>

                <!-- Submit Button -->
                <button type="submit" form="loanForm" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs flex items-center justify-center gap-1.5">
                    Apply Now &rarr;
                </button>

                <!-- Footnote -->
                <p class="text-[11px] text-slate-400 text-center font-medium">
                    No impact to your credit score to check rates.
                </p>

            </div>
        </div>

    </div>

</div>
@endsection
