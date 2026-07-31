@extends('layouts.app')

@section('title', 'Transaction Monitoring - Admin Terminal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Transaction Monitoring</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Real-time audit log of all platform deposits, withdrawals, disbursements, and repayments.</p>
        </div>
        <button type="button" @click="alert('Exporting Transaction Audit Log CSV...')" class="py-2 px-4 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs">
            Export CSV
        </button>
    </div>

    <!-- Table Container Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">TIMESTAMP</th>
                        <th class="py-3.5 px-6">TRANSACTION ID</th>
                        <th class="py-3.5 px-6">TYPE</th>
                        <th class="py-3.5 px-6">USER / ENTITY</th>
                        <th class="py-3.5 px-6">AMOUNT (USD)</th>
                        <th class="py-3.5 px-6 text-right">STATUS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 text-slate-500 text-[11px]">{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-4 px-6 font-bold text-emerald-700">TXN-{{ strtoupper(substr($tx->reference_id ?? $tx->id, 0, 8)) }}</td>
                        <td class="py-4 px-6 font-bold uppercase tracking-wider text-[11px]">
                            <span class="px-2.5 py-0.5 rounded text-[10px]
                                @if($tx->type === 'deposit') bg-emerald-100 text-emerald-800
                                @elseif($tx->type === 'withdrawal') bg-rose-100 text-rose-800
                                @elseif($tx->type === 'disbursement') bg-purple-100 text-purple-800
                                @else bg-blue-100 text-blue-800 @endif">
                                {{ $tx->type }}
                            </span>
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900">{{ $tx->wallet?->user?->email ?? 'System Node' }}</td>
                        <td class="py-4 px-6 font-extrabold text-slate-900">
                            Rp {{ number_format($tx->amount, 0, ',', '.') }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800">
                                Completed
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 font-medium">No transaction audit logs recorded.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
