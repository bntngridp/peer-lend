@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    <!-- Navigation Back Link -->
    <div class="mb-4">
        <a href="{{ route('admin.loans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to queue
        </a>
    </div>

    <!-- Loan details card -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white mb-6">
        <div class="px-6 py-6 sm:px-8 border-b border-gray-150 bg-gray-50/70">
            <h2 class="text-xl font-bold text-gray-900">Review Loan Application</h2>
            <p class="mt-1 text-sm text-gray-500">Applicant: <strong>{{ $loan->borrower->profile->full_name }}</strong> ({{ $loan->borrower->email }})</p>
        </div>

        <div class="px-6 py-6 sm:px-8 space-y-6">
            <!-- Core Details Grid -->
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-6">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Target Amount</span>
                    <span class="text-base font-bold text-gray-950">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Annual Return (APR)</span>
                    <span class="text-base font-bold text-emerald-600">{{ $loan->interest_rate }}%</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Duration Tenor</span>
                    <span class="text-base font-bold text-gray-950">{{ $loan->duration }} Months</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Current Status</span>
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold mt-1 uppercase tracking-wider
                        @if($loan->status === 'pending') bg-amber-50 text-amber-700 ring-1 ring-amber-600/10
                        @elseif($loan->status === 'open_funding') bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10
                        @elseif($loan->status === 'active') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                        @else bg-gray-50 text-gray-700 ring-1 ring-gray-600/10 @endif">
                        {{ $loan->status }}
                    </span>
                </div>
            </div>

            <!-- Borrower Info -->
            <div class="border-t border-gray-150 pt-6 grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Purpose</span>
                    <span class="text-sm font-semibold text-gray-900">{{ $loan->purpose }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Monthly Income</span>
                    <span class="text-sm font-semibold text-indigo-600">Rp {{ number_format($loan->borrower->profile->monthly_income ?? 0, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- Description -->
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Detailed Description</span>
                <p class="text-sm text-gray-600 leading-relaxed mt-1.5 whitespace-pre-line">{{ $loan->description ?: 'No detailed description.' }}</p>
            </div>

            <!-- Collateral Info if crypto -->
            @if($loan->isCryptoLoan())
                <div class="rounded-xl border border-indigo-100 bg-indigo-50/20 p-4 border-t">
                    <span class="block text-xs font-bold uppercase tracking-wider text-indigo-800 mb-2">Crypto Collateral Locked</span>
                    <div class="grid grid-cols-3 gap-4 text-center">
                        <div class="bg-white rounded-lg p-2.5 border border-indigo-50">
                            <span class="block text-[10px] text-gray-400 uppercase">Quantity</span>
                            <span class="text-xs font-bold text-gray-800">{{ number_format($loan->collateral_amount, 6) }} {{ $loan->collateralCurrency->code }}</span>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-indigo-50">
                            <span class="block text-[10px] text-gray-400 uppercase">Initial LTV</span>
                            <span class="text-xs font-bold text-gray-800">{{ $loan->initial_ltv }}%</span>
                        </div>
                        <div class="bg-white rounded-lg p-2.5 border border-indigo-50">
                            <span class="block text-[10px] text-gray-400 uppercase">Liquidation Price</span>
                            <span class="text-xs font-bold text-red-600">Rp {{ number_format($loan->liquidation_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <!-- Action Decision Card -->
    @if($loan->status === 'pending')
        <div class="shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider">Approve to marketplace</h3>
            <p class="text-xs text-gray-500 mb-4">By approving, this loan listing will immediately display on the public marketplace and accept funding from lenders.</p>
            <form action="{{ route('admin.loans.approve', $loan->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-all">
                    Approve Application
                </button>
            </form>
        </div>
    @elseif($loan->status === 'funded')
        <div class="shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-6">
            <h3 class="text-sm font-bold text-gray-900 mb-3 uppercase tracking-wider text-emerald-800">Trigger Disbursement</h3>
            <p class="text-xs text-gray-500 mb-4">This loan is 100% funded and the contract agreement has been generated. Triggering disbursement will deposit the net funds to the borrower's wallet and settle all held lender allocations.</p>
            <form action="{{ route('admin.loans.disburse', $loan->id) }}" method="POST">
                @csrf
                <button type="submit"
                        class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/10 hover:bg-emerald-700 transition-all">
                    Disburse Capital
                </button>
            </form>
        </div>
    @endif

</div>
@endsection
