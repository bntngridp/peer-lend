@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="mb-10 text-center">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900 sm:text-4xl">P2P Lending Marketplace</h2>
        <p class="mt-3 text-sm text-gray-500 max-w-2xl mx-auto">Deploy your virtual capital to earn monthly interest payments. Diversify investments across various risk grades and collateral parameters.</p>
    </div>

    <!-- Marketplace grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($loans as $loan)
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white flex flex-col justify-between hover:translate-y-[-2px] transition-all duration-200">
                <div class="p-6">
                    <!-- Top header row -->
                    <div class="flex items-start justify-between border-b border-gray-50 pb-4 mb-4">
                        <div>
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-bold uppercase tracking-wider
                                @if($loan->risk_grade === 'A') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                                @elseif($loan->risk_grade === 'B') bg-blue-50 text-blue-700 ring-1 ring-blue-600/10
                                @elseif($loan->risk_grade === 'C') bg-amber-50 text-amber-700 ring-1 ring-amber-600/10
                                @else bg-red-50 text-red-700 ring-1 ring-red-600/10 @endif">
                                Grade {{ $loan->risk_grade }}
                            </span>
                            <span class="text-xs text-gray-400 ml-2">Tenor: {{ $loan->duration }} M</span>
                        </div>
                        <span class="text-sm font-bold text-indigo-600">{{ $loan->interest_rate }}% APR</span>
                    </div>

                    <!-- Borrower & Title info -->
                    <div class="mb-4">
                        <h3 class="text-base font-bold text-gray-900 line-clamp-1">{{ $loan->purpose }}</h3>
                        <p class="text-xs text-gray-400 mt-0.5">Category: {{ $loan->category->name }}</p>
                    </div>

                    <!-- Collateral alert/badge -->
                    @if($loan->isCryptoLoan())
                        <div class="rounded-xl bg-indigo-50/50 border border-indigo-100 p-2.5 flex items-center justify-between mb-4">
                            <span class="text-xs text-indigo-700 font-semibold">DeFi Secured</span>
                            <span class="text-[10px] bg-indigo-600 text-white rounded-lg px-1.5 py-0.5 font-bold uppercase">{{ $loan->collateralCurrency->code }}</span>
                        </div>
                    @else
                        <div class="rounded-xl bg-gray-50 border border-gray-100 p-2.5 flex items-center justify-between mb-4">
                            <span class="text-xs text-gray-500">Unsecured Loan</span>
                            <span class="text-[10px] text-gray-400 font-bold uppercase">Fiat</span>
                        </div>
                    @endif

                    <!-- Amount -->
                    <div class="mb-6">
                        <span class="block text-[10px] font-bold uppercase tracking-wider text-gray-400">Target Funding</span>
                        <span class="text-2xl font-black text-gray-900 tracking-tight">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                    </div>

                    <!-- Progress Bar -->
                    <div class="space-y-1">
                        <div class="flex items-center justify-between text-xs font-semibold">
                            <span class="text-indigo-600">{{ $loan->funded_percentage }}% funded</span>
                            <span class="text-gray-400">Rp {{ number_format($loan->fundings()->sum('amount'), 0, ',', '.') }}</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                            <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                        </div>
                    </div>
                </div>

                <!-- Footer button action -->
                <div class="bg-gray-50/70 border-t border-gray-100 px-6 py-4">
                    <a href="{{ route('marketplace.show', $loan->id) }}"
                       class="block w-full text-center rounded-xl bg-white border border-gray-300 px-4 py-2.5 text-xs font-semibold text-gray-700 shadow-sm hover:bg-gray-50 transition-colors">
                        Invest Capital &rarr;
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full py-16 text-center">
                <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-4">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0a2 2 0 01-2 2H6a2 2 0 01-2-2m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5" />
                    </svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900">No Loans Active</h3>
                <p class="mt-1 text-xs text-gray-500">There are no open loan requests available for investment at the moment.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    @if($loans->hasPages())
        <div class="mt-10">
            {{ $loans->links() }}
        </div>
    @endif

</div>
@endsection
