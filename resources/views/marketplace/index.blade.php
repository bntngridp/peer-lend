@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Top Header Bar with Filter Form -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Marketplace') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Browse institutional-grade P2P loan opportunities') }}</p>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('marketplace.index') }}" class="flex flex-wrap items-center gap-2.5">
            <!-- Search Bar -->
            <div class="relative min-w-[220px]">
                <input type="text" name="search" value="{{ request('search') }}" 
                       placeholder="{{ __('Search marketplace...') }}" 
                       class="w-full rounded-xl border border-slate-200 bg-white pl-9 pr-3 py-2 text-xs font-medium text-slate-700 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs">
                <span class="absolute left-3 top-2.5 text-slate-400 text-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z"/></svg>
                </span>
            </div>

            <!-- Filter: Risk Grade -->
            <div class="relative">
                <select name="risk_grade" onchange="this.form.submit()" 
                        class="appearance-none rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pl-3.5 pr-8 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs cursor-pointer">
                    <option value="">{{ __('Risk Grade (All)') }}</option>
                    @foreach(['A', 'B', 'C', 'D'] as $g)
                        <option value="{{ $g }}" {{ request('risk_grade') === $g ? 'selected' : '' }}>{{ __('Grade') }} {{ $g }}</option>
                    @endforeach
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>

            <!-- Filter: Term -->
            <div class="relative">
                <select name="term" onchange="this.form.submit()" 
                        class="appearance-none rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pl-3.5 pr-8 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs cursor-pointer">
                    <option value="">{{ __('Term (All)') }}</option>
                    <option value="6" {{ request('term') == '6' ? 'selected' : '' }}>{{ __n(6) }} {{ __('Months') }}</option>
                    <option value="12" {{ request('term') == '12' ? 'selected' : '' }}>{{ __n(12) }} {{ __('Months') }}</option>
                    <option value="18" {{ request('term') == '18' ? 'selected' : '' }}>{{ __n(18) }} {{ __('Months') }}</option>
                    <option value="24" {{ request('term') == '24' ? 'selected' : '' }}>{{ __n(24) }} {{ __('Months') }}</option>
                    <option value="36" {{ request('term') == '36' ? 'selected' : '' }}>{{ __n(36) }} {{ __('Months') }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>

            <!-- Sort By -->
            <div class="relative">
                <select name="sort" onchange="this.form.submit()" 
                        class="appearance-none rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 pl-3.5 pr-8 py-2 text-xs font-bold text-slate-700 dark:text-slate-200 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs cursor-pointer">
                    <option value="">{{ __('Sort: Latest') }}</option>
                    <option value="interest_desc" {{ request('sort') === 'interest_desc' ? 'selected' : '' }}>{{ __('Interest Rate (High to Low)') }}</option>
                    <option value="amount_desc" {{ request('sort') === 'amount_desc' ? 'selected' : '' }}>{{ __('Amount (High to Low)') }}</option>
                </select>
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center pr-2.5 text-slate-400">
                    <svg class="h-3.5 w-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5" /></svg>
                </div>
            </div>

            @if(request()->anyFilled(['search', 'risk_grade', 'term', 'sort']))
                <a href="{{ route('marketplace.index') }}" class="rounded-xl border border-slate-200 dark:border-slate-800 bg-slate-100 dark:bg-slate-800 px-3.5 py-2 text-xs font-bold text-slate-600 dark:text-slate-300 hover:bg-slate-200 dark:hover:bg-slate-700 transition-colors shadow-xs">
                    {{ __('Reset') }}
                </a>
            @endif
        </form>
    </div>

    <!-- Data Table Container Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        
        <!-- Table Sub-header Bar -->
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <span class="text-xs font-bold text-slate-500">
                {{ __('Showing :from-:to of :total opportunities', ['from' => __n(1), 'to' => __n($loans->count()), 'total' => __n($loans->total())]) }}
            </span>
            <div class="flex items-center gap-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('SORT BY:') }}</span>
                <span class="text-xs font-bold text-slate-800">
                    @if(request('sort') === 'interest_desc') {{ __('Interest Rate (High to Low)') }}
                    @elseif(request('sort') === 'amount_desc') {{ __('Loan Amount (High to Low)') }}
                    @else {{ __('Latest Listings') }}
                    @endif
                </span>
            </div>
        </div>

        <!-- Data Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('BORROWER') }}</th>
                        <th class="py-3.5 px-6">{{ __('LOAN AMOUNT') }}</th>
                        <th class="py-3.5 px-6">{{ __('INTEREST RATE') }}</th>
                        <th class="py-3.5 px-6">{{ __('TERM') }}</th>
                        <th class="py-3.5 px-6">{{ __('RISK GRADE') }}</th>
                        <th class="py-3.5 px-6">{{ __('FUNDING PROGRESS') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('ACTION') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Borrower -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-emerald-700 text-white font-black flex items-center justify-center text-xs shadow-xs">
                                    {{ strtoupper(substr($loan->purpose ?: ($loan->borrower?->profile?->full_name ?? 'L'), 0, 1)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs line-clamp-1">
                                        {{ $loan->purpose ?: ($loan->borrower?->profile?->full_name ?? __('Institutional Borrower')) }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium block">
                                        ID: LN-{{ substr($loan->id, 0, 6) }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Loan Amount -->
                        <td class="py-4 px-6 font-extrabold text-slate-900 text-sm">
                            Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}
                        </td>

                        <!-- Interest Rate -->
                        <td class="py-4 px-6">
                            <span class="text-emerald-700 font-extrabold text-sm">{{ __n($loan->interest_rate) }}%</span>
                        </td>

                        <!-- Term -->
                        <td class="py-4 px-6 text-slate-600 font-semibold">
                            {{ __n($loan->duration) }} {{ __('Months') }}
                        </td>

                        <!-- Risk Grade -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 text-xs font-semibold text-slate-700 dark:text-slate-300">
                                <span class="h-1.5 w-1.5 rounded-full shrink-0
                                    @if($loan->risk_grade === 'A') bg-emerald-500
                                    @elseif($loan->risk_grade === 'B') bg-blue-500
                                    @elseif($loan->risk_grade === 'C') bg-amber-500
                                    @else bg-rose-500 @endif"></span>
                                <span>Grade {{ $loan->risk_grade }}</span>
                            </span>
                        </td>

                        <!-- Funding Progress -->
                        <td class="py-4 px-6 min-w-[200px]">
                            <div class="space-y-1.5">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">{{ __n((int)$loan->funded_percentage) }}%</span>
                                    <span class="text-slate-400 font-semibold">Rp {{ __n(number_format($loan->fundings()->sum('amount'), 0, ',', '.')) }} {{ __('raised') }}</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            @if(Auth::check() && $loan->fundings()->where('lender_id', Auth::id())->exists())
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('loans.installments', $loan->id) }}" 
                                       class="inline-flex items-center gap-1.5 py-1.5 px-3 rounded-xl bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 text-xs font-bold hover:bg-emerald-200 transition-colors shadow-xs border border-emerald-300 dark:border-emerald-800">
                                        <svg class="w-3.5 h-3.5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M8.625 12a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H8.25m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0H12m4.125 0a.375.375 0 11-.75 0 .375.375 0 01.75 0zm0 0h-.375M21 12c0 4.556-4.03 8.25-9 8.25a9.764 9.764 0 01-2.555-.337A5.972 5.972 0 015.41 20.97a5.969 5.969 0 01-.474-.065 4.48 4.48 0 00.978-2.025c.09-.457-.133-.901-.467-1.226C3.93 16.178 3 14.189 3 12c0-4.556 4.03-8.25 9-8.25s9 3.694 9 8.25z"/></svg>
                                        <span>{{ __('Chat & Schedule') }}</span>
                                    </a>
                                    <a href="{{ route('marketplace.show', $loan->id) }}" 
                                       class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                        {{ __('View') }} &rarr;
                                    </a>
                                </div>
                            @else
                                <a href="{{ route('marketplace.show', $loan->id) }}" 
                                   class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                    {{ __('Invest') }} &rarr;
                                </a>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-medium">
                            {{ __('No loan funding opportunities match your search criteria.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>

    <!-- Pagination links -->
    @if($loans->hasPages())
        <div class="mt-6">
            {{ $loans->links() }}
        </div>
    @endif

</div>
@endsection
