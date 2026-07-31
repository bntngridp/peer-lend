@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">My Loans &amp; Applications</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Track verification approvals, funding percentage progress, and manage active installments.</p>
        </div>
        <a href="{{ route('loans.create') }}" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
            <span>➕</span> Apply for Loan
        </a>
    </div>

    <!-- Loan List Card Table -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Loan Applications</h3>
            <span class="text-xs font-medium text-slate-500">Total: {{ $loans->total() }} Applications</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">LOAN DETAILS</th>
                        <th class="py-3.5 px-6">AMOUNT &amp; INTEREST</th>
                        <th class="py-3.5 px-6">STATUS</th>
                        <th class="py-3.5 px-6">FUNDING PROGRESS</th>
                        <th class="py-3.5 px-6 text-right">ACTION</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($loans as $loan)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <!-- Details -->
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-slate-100 border border-slate-200 flex items-center justify-center text-slate-600 font-bold text-xs">
                                    #LN
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs line-clamp-1">{{ $loan->purpose }}</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">
                                        Category: {{ $loan->category->name }} • {{ $loan->duration }} Months
                                    </span>
                                </div>
                            </div>
                        </td>

                        <!-- Amount -->
                        <td class="py-4 px-6">
                            <span class="font-extrabold text-slate-900 text-sm block">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
                            <span class="text-[11px] text-emerald-700 font-bold block mt-0.5">Rate: {{ $loan->interest_rate }}% (Grade {{ $loan->risk_grade }})</span>
                        </td>

                        <!-- Status -->
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold uppercase tracking-wider
                                @if($loan->status === 'pending') bg-amber-100 text-amber-800 border border-amber-200
                                @elseif($loan->status === 'open_funding') bg-blue-100 text-blue-800 border border-blue-200
                                @elseif($loan->status === 'active') bg-emerald-100 text-emerald-800 border border-emerald-200
                                @elseif($loan->status === 'completed') bg-slate-100 text-slate-700 border border-slate-200
                                @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                                {{ str_replace('_', ' ', $loan->status) }}
                            </span>
                        </td>

                        <!-- Funding Progress -->
                        <td class="py-4 px-6 min-w-[180px]">
                            <div class="space-y-1">
                                <div class="flex items-center justify-between text-[11px]">
                                    <span class="font-bold text-slate-900">{{ (int)$loan->funded_percentage }}% funded</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>

                        <!-- Action -->
                        <td class="py-4 px-6 text-right">
                            @if($loan->status === 'active' || $loan->status === 'completed')
                                <a href="{{ route('loans.installments', $loan->id) }}" 
                                   class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                    View Installments &rarr;
                                </a>
                            @else
                                <span class="text-slate-400 font-medium text-xs">Processing</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-slate-400 text-xs font-medium">
                            You have not submitted any loan applications yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($loans->hasPages())
            <div class="p-4 border-t border-slate-100">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
