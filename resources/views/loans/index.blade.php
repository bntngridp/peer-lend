@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('My Loans') }} &amp; {{ __('New Application') }}</h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ __('Overview of your current loans and upcoming obligations.') }}</p>
        </div>
        <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/></svg>
            {{ __('Apply for New Loan') }}
        </a>
    </div>

    <!-- Loan List Card Table -->
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50">
            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('My Loans') }}</h3>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ __('Total:') }} {{ __n($loans->total()) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 dark:text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('LOAN DETAILS') }}</th>
                        <th class="py-3.5 px-6">{{ __('AMOUNT') }}</th>
                        <th class="py-3.5 px-6">{{ __('STATUS') }}</th>
                        <th class="py-3.5 px-6">{{ __('FUNDING PROGRESS') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('ACTION') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                        <!-- Details -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 flex items-center justify-center text-slate-600 dark:text-slate-400 font-bold text-xs">
                                    #LN
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs line-clamp-1">{{ $loan->purpose }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">
                                        {{ __($loan->category?->name ?? 'Loan') }} • {{ __n($loan->duration) }} {{ __('Months') }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="py-4 px-6">
                            <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm block">Rp {{ __n(number_format($loan->amount, 0, ',', '.')) }}</span>
                            <span class="text-[11px] text-emerald-700 dark:text-emerald-400 font-bold block mt-0.5">{{ __('Rate:') }} {{ __n($loan->interest_rate) }}% ({{ __('Grade') }} {{ $loan->risk_grade }})</span>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                <span class="h-1.5 w-1.5 rounded-full shrink-0
                                    @if($loan->status === 'active') bg-emerald-500
                                    @elseif($loan->status === 'pending' || $loan->status === 'open_funding') bg-amber-500
                                    @elseif($loan->status === 'completed') bg-slate-400
                                    @else bg-rose-500 @endif"></span>
                                <span>{{ __(ucwords(str_replace('_', ' ', $loan->status))) }}</span>
                            </span>
                        </td>

                        <!-- Funding Progress -->
                        <td class="py-4 px-6 min-w-[180px]">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900 dark:text-slate-100">{{ __('Funded') }} {{ __n((int)$loan->funded_percentage) }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-6 text-right whitespace-nowrap">
                            <div class="flex items-center justify-end gap-2">
                                @if($loan->status === 'open_funding')
                                    <a href="{{ route('marketplace.show', $loan->id) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                        <span>{{ __('Marketplace') }}</span>
                                    </a>
                                @endif

                                @if($loan->status === 'active' || $loan->status === 'completed' || $loan->installments()->exists() || $loan->status === 'open_funding')
                                    <a href="{{ route('loans.installments', $loan->id) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                        <span>{{ __('View Schedule') }}</span>
                                        <span>&rarr;</span>
                                    </a>
                                @else
                                    <a href="{{ route('loans.installments', $loan->id) }}" 
                                       class="inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-700 dark:text-slate-300 text-xs font-bold hover:bg-slate-50 dark:hover:bg-slate-700 transition-colors shadow-xs">
                                        <span>{{ __('View Details') }}</span>
                                        <span>&rarr;</span>
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                            {{ __('You have not submitted any loan applications yet.') }}
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="p-4 border-t border-slate-100 dark:border-slate-800">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
