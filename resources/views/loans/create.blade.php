@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight">Apply for a New Loan</h2>
        <p class="mt-2 text-sm text-gray-500">Fill in the fields below. If you want to use crypto as collateral, choose your preferred token.</p>
    </div>

    <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-6 sm:p-8">
        <form action="{{ route('loans.store') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Category & Risk Grade -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="category_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Loan Category</label>
                    <select name="category_id" id="category_id" required
                            class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="risk_grade" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Risk Grade</label>
                    <select name="risk_grade" id="risk_grade" required
                            class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="A">Grade A (Risiko Rendah: 8% - 10%)</option>
                        <option value="B">Grade B (Risiko Sedang: 11% - 14%)</option>
                        <option value="C">Grade C (Risiko Tinggi: 15% - 18%)</option>
                        <option value="D">Grade D (Risiko Sangat Tinggi: 19% - 24%)</option>
                    </select>
                </div>
            </div>

            <!-- Amount & Duration -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="amount" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Loan Amount (IDR)</label>
                    <input type="number" name="amount" id="amount" required min="1000000" max="500000000" value="{{ old('amount') }}"
                           class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('amount') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                           placeholder="e.g. 10000000">
                    @error('amount')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="duration" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Tenor Duration</label>
                    <select name="duration" id="duration" required
                            class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="3">3 Months</option>
                        <option value="6">6 Months</option>
                        <option value="12">12 Months</option>
                        <option value="24">24 Months</option>
                    </select>
                </div>
            </div>

            <!-- Interest Rate & Collateral Currency -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="interest_rate" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Proposed Interest Rate (% Annual)</label>
                    <input type="number" step="0.01" name="interest_rate" id="interest_rate" required value="{{ old('interest_rate') }}"
                           class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('interest_rate') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                           placeholder="e.g. 12.00">
                    @error('interest_rate')
                        <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="collateral_currency_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Crypto Collateral (Optional)</label>
                    <select name="collateral_currency_id" id="collateral_currency_id"
                            class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">None (Unsecured / Fiat Loan)</option>
                        @foreach($cryptoCurrencies as $crypto)
                            <option value="{{ $crypto->id }}">{{ $crypto->code }} - DeFi Collateral (LTV 50%)</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- Purpose -->
            <div>
                <label for="purpose" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Purpose of Loan</label>
                <input type="text" name="purpose" id="purpose" required value="{{ old('purpose') }}"
                       class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500 @error('purpose') border-red-300 text-red-900 focus:border-red-500 focus:ring-red-500 @enderror"
                       placeholder="e.g. Purchase business equipment">
                @error('purpose')
                    <p class="mt-1.5 text-xs text-red-600 font-medium">{{ $message }}</p>
                @enderror
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Detailed Description</label>
                <textarea name="description" id="description" rows="5"
                          class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-3 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                          placeholder="Provide details about your project or funding requirements..."></textarea>
            </div>

            <!-- Submit -->
            <div class="pt-4">
                <button type="submit"
                        class="flex w-full justify-center rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-all">
                    Submit Loan Request
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
