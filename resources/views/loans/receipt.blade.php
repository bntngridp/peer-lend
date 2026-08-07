@extends('layouts.app')

@section('title', __('Official Payment Receipt'))

@section('content')
<div class="max-w-3xl mx-auto space-y-6">

    <!-- Top Action Header (No-print) -->
    <div class="flex items-center justify-between print:hidden">
        <a href="{{ route('loans.installments', $loan->id) }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
            &larr; {{ __('Back to Installments Schedule') }}
        </a>

        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all cursor-pointer">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24.03-.48.062-.72.096m.72-.096a42.415 42.415 0 0110.56 0m-10.56 0L6.34 18m10.94-4.171c.24.03.48.062.72.096m-.72-.096L17.66 18m0 0l.229 2.523a1.125 1.125 0 01-1.12 1.227H7.231a1.125 1.125 0 01-1.12-1.227L6.34 18m11.32 0h-11.32M9 6h6m-6 3h6m-6 3h6M3.75 6A2.25 2.25 0 016 3.75h12A2.25 2.25 0 0120.25 6v12A2.25 2.25 0 0118 20.25H6A2.25 2.25 0 013.75 18V6z"/></svg>
            {{ __('Print / Download Official Receipt (PDF)') }}
        </button>
    </div>

    <!-- Official Receipt Printable Document Card -->
    <div class="rounded-3xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-8 sm:p-10 shadow-lg relative overflow-hidden print:border-none print:shadow-none print:p-0">
        
        <!-- Watermark / Stamp -->
        <div class="absolute right-6 top-6 opacity-10 dark:opacity-20 pointer-events-none select-none text-right">
            <div class="text-6xl font-black uppercase text-emerald-600 tracking-tighter">PAID</div>
            <div class="text-[10px] font-bold text-slate-500 uppercase tracking-widest mt-1">VERIFIED BY LENDFLOW ESCROW</div>
        </div>

        <!-- Header Brand & Receipt Title -->
        <div class="border-b border-slate-100 dark:border-slate-800 pb-6 mb-6 flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <div class="h-9 w-9 rounded-xl bg-emerald-700 text-white flex items-center justify-center font-black text-lg shadow-xs">
                        L
                    </div>
                    <div>
                        <span class="text-lg font-black text-slate-900 dark:text-slate-100 tracking-tight">LendFlow P2P</span>
                        <span class="block text-[10px] font-bold text-slate-400 uppercase tracking-widest">Institutional Credit Protocol</span>
                    </div>
                </div>
            </div>

            <div class="sm:text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800">
                    ✓ Official Payment Receipt
                </span>
                <p class="text-xs font-mono font-bold text-slate-500 mt-2">Ref: RCP-{{ date('Ymd', strtotime($installment->paid_at ?? now())) }}-{{ strtoupper(substr($installment->id, 0, 8)) }}</p>
            </div>
        </div>

        <!-- Metadata Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 bg-slate-50/70 dark:bg-slate-800/40 rounded-2xl p-5 border border-slate-100 dark:border-slate-800/60 text-xs mb-8">
            <div class="space-y-2">
                <div>
                    <span class="text-slate-400 font-medium block text-[11px] uppercase tracking-wider">{{ __('Borrower Details') }}</span>
                    <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm">{{ $loan->borrower->name ?? 'Borrower' }}</span>
                    <span class="block text-slate-500 font-mono">{{ $loan->borrower->email ?? '-' }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-medium block text-[11px] uppercase tracking-wider mt-3">{{ __('Loan Application ID') }}</span>
                    <span class="font-bold font-mono text-slate-800 dark:text-slate-200">LN-{{ strtoupper(substr($loan->id, 0, 8)) }} ({{ $loan->purpose }})</span>
                </div>
            </div>

            <div class="space-y-2 sm:text-right">
                <div>
                    <span class="text-slate-400 font-medium block text-[11px] uppercase tracking-wider">{{ __('Payment Timestamp') }}</span>
                    <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm">{{ \Carbon\Carbon::parse($installment->paid_at ?? now())->format('d M Y, H:i:s T') }}</span>
                </div>
                <div>
                    <span class="text-slate-400 font-medium block text-[11px] uppercase tracking-wider mt-3">{{ __('Payment Method') }}</span>
                    <span class="font-bold text-emerald-700 dark:text-emerald-400">LendFlow Main Wallet (IDR)</span>
                </div>
            </div>
        </div>

        <!-- Payment Rincian Table -->
        <div class="space-y-3 mb-8">
            <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">{{ __('Installment Breakdown') }}</h3>
            
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 overflow-hidden">
                <table class="w-full text-left text-xs">
                    <thead class="bg-slate-50 dark:bg-slate-800/80 border-b border-slate-200 dark:border-slate-800 text-slate-500 uppercase tracking-wider font-bold">
                        <tr>
                            <th class="py-3.5 px-5">{{ __('Description') }}</th>
                            <th class="py-3.5 px-5 text-center">{{ __('Installment No.') }}</th>
                            <th class="py-3.5 px-5 text-right">{{ __('Amount Paid') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800/60 font-semibold text-slate-800 dark:text-slate-200">
                        <tr>
                            <td class="py-3.5 px-5">
                                <span class="font-bold block">{{ __('Loan Principal Repayment') }}</span>
                                <span class="text-[11px] text-slate-400 font-normal">{{ __('Direct principal reduction') }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold">#{{ $installment->installment_number }}</td>
                            <td class="py-3.5 px-5 text-right font-mono">Rp {{ number_format($installment->principal_amount, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td class="py-3.5 px-5">
                                <span class="font-bold block">{{ __('Interest Share Payout') }}</span>
                                <span class="text-[11px] text-slate-400 font-normal">{{ __('Proportional yield distributed to lenders') }}</span>
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold">#{{ $installment->installment_number }}</td>
                            <td class="py-3.5 px-5 text-right font-mono">Rp {{ number_format($installment->interest_amount, 0, ',', '.') }}</td>
                        </tr>
                        @if($installment->penalty_amount > 0)
                        <tr class="bg-rose-50/50 dark:bg-rose-950/20">
                            <td class="py-3.5 px-5 text-rose-700 dark:text-rose-400 font-bold">
                                {{ __('Late Penalty Fee') }}
                            </td>
                            <td class="py-3.5 px-5 text-center font-bold text-rose-700">#{{ $installment->installment_number }}</td>
                            <td class="py-3.5 px-5 text-right font-mono font-bold text-rose-700 dark:text-rose-400">Rp {{ number_format($installment->penalty_amount, 0, ',', '.') }}</td>
                        </tr>
                        @endif
                    </tbody>
                    <tfoot class="bg-slate-50/80 dark:bg-slate-800/50 border-t border-slate-200 dark:border-slate-800 font-black text-slate-900 dark:text-slate-100">
                        <tr>
                            <td colspan="2" class="py-4 px-5 text-sm uppercase tracking-wider text-right">{{ __('TOTAL PAID') }}</td>
                            <td class="py-4 px-5 text-right text-base font-mono text-emerald-700 dark:text-emerald-400">
                                Rp {{ number_format($installment->total_amount + $installment->penalty_amount, 0, ',', '.') }}
                            </td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Footer Audit & Guarantee Notes -->
        <div class="pt-6 border-t border-slate-100 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 text-[11px] text-slate-400">
            <div>
                <p class="font-semibold text-slate-600 dark:text-slate-300">LendFlow Financial System Protocol</p>
                <p class="mt-0.5">This official electronic receipt is digitally generated &amp; verified by LendFlow P2P Engine.</p>
            </div>
            <div class="sm:text-right font-mono">
                <span>Security Token: {{ md5($installment->id . $installment->paid_at) }}</span>
            </div>
        </div>

    </div>

</div>
@endsection
