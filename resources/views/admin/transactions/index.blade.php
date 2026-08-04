@extends('layouts.admin')

@section('title', __('Transaction Audit Monitoring') . ' - Admin Terminal')

@section('content')
<div x-data="transactionAuditApp()" class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">

    {{-- ─── Toast Notification Popup ────────────────────────────────────────── --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-20 right-6 z-50 flex items-center gap-3 rounded-2xl bg-slate-900 dark:bg-slate-800 border border-slate-700 dark:border-slate-600 px-4 py-3 text-sm font-semibold text-white shadow-2xl"
         style="display: none;">
        <div class="h-8 w-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <span x-text="toastMessage"></span>
        <button type="button" @click="showToast = false" class="ml-2 text-slate-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>
    
    {{-- ─── Header Bar ───────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Transaction Monitoring') }}</h1>
            <p class="text-xs font-medium text-slate-500 dark:text-slate-400 mt-1">{{ __('Real-time audit log of all platform deposits, withdrawals, disbursements, and repayments.') }}</p>
        </div>
        
        <button type="button" 
                @click="showExportModal = true" 
                class="py-2.5 px-4 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-slate-800 dark:text-slate-200 font-bold text-xs hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors shadow-xs flex items-center gap-2">
            <svg class="w-4 h-4 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
            </svg>
            <span>{{ __('Export Report') }}</span>
        </button>
    </div>

    {{-- ─── Table Container Card ────────────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('TIMESTAMP') }}</th>
                        <th class="py-3.5 px-6">{{ __('TRANSACTION ID') }}</th>
                        <th class="py-3.5 px-6">{{ __('TYPE') }}</th>
                        <th class="py-3.5 px-6">{{ __('USER / ENTITY') }}</th>
                        <th class="py-3.5 px-6">{{ __('AMOUNT (IDR)') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('STATUS') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300">
                    @forelse($transactions as $tx)
                    <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/50 transition-colors">
                        <td class="py-4 px-6 text-slate-500 dark:text-slate-400 text-[11px]">{{ $tx->created_at->format('Y-m-d H:i:s') }}</td>
                        <td class="py-4 px-6 font-bold text-emerald-600 dark:text-emerald-400">TXN-{{ strtoupper(substr($tx->reference_id ?? $tx->id, 0, 8)) }}</td>
                        <td class="py-4 px-6 font-bold uppercase tracking-wider text-[11px]">
                            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold
                                @if($tx->type === 'deposit') bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20
                                @elseif($tx->type === 'withdrawal') bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/20
                                @elseif($tx->type === 'disbursement') bg-purple-500/15 text-purple-600 dark:text-purple-400 border border-purple-500/20
                                @else bg-blue-500/15 text-blue-600 dark:text-blue-400 border border-blue-500/20 @endif">
                                {{ __($tx->type) }}
                            </span>
                        </td>
                        <td class="py-4 px-6 font-bold text-slate-900 dark:text-slate-100">{{ $tx->wallet?->user?->email ?? __('System Node') }}</td>
                        <td class="py-4 px-6 font-extrabold text-slate-900 dark:text-slate-100">
                            Rp {{ __n(number_format($tx->amount, 0, ',', '.')) }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                {{ __('Completed') }}
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">{{ __('No transaction audit logs recorded.') }}</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($transactions->hasPages())
        <div class="p-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
            {{ $transactions->links() }}
        </div>
        @endif
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: EXPORT TRANSACTION AUDIT REPORT --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showExportModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showExportModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showExportModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Export Transaction Audit Report') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Download verified platform financial transaction audit logs.') }}
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                        {{ __('File Format') }}
                    </label>
                    <select x-model="exportFormat" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500 cursor-pointer">
                        <option value="csv">{{ __('CSV Spreadsheet (.csv)') }}</option>
                        <option value="pdf">{{ __('PDF Executive Audit (.pdf)') }}</option>
                        <option value="json">{{ __('JSON Audit Stream (.json)') }}</option>
                    </select>
                </div>

                <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 text-[11px] space-y-1 text-slate-600 dark:text-slate-300 font-medium">
                    <div class="flex justify-between">
                        <span>{{ __('Audit Log Records:') }}</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ count($exportTransactions) }} {{ __('Transactions') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Total Audit Volume:') }}</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">Rp {{ number_format($exportTransactions->sum('amount'), 0, ',', '.') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Security Status:') }}</span>
                        <span class="font-bold text-blue-600 dark:text-blue-400">100% Immutable</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showExportModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="downloadReport()" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs flex items-center gap-1.5">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 16.5v2.25A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75V16.5M16.5 12 12 16.5m0 0L7.5 12m4.5 4.5V3" />
                    </svg>
                    <span>{{ __('Confirm & Download') }}</span>
                </button>
            </div>
        </div>
    </div>

</div>

<script>
    function transactionAuditApp() {
        return {
            showExportModal: false,
            exportFormat: 'csv',
            toastMessage: '',
            showToast: false,
            triggerToast(msg) {
                this.toastMessage = msg;
                this.showToast = true;
                setTimeout(() => { this.showToast = false; }, 4000);
            },
            downloadReport() {
                this.showExportModal = false;
                
                const rawTxData = [
                    @foreach($exportTransactions as $tx)
                    {
                        timestamp: {!! json_encode($tx->created_at->format('Y-m-d H:i:s')) !!},
                        txn_id: {!! json_encode('TXN-' . strtoupper(substr($tx->reference_id ?? $tx->id, 0, 8))) !!},
                        type: {!! json_encode(strtoupper($tx->type)) !!},
                        user: {!! json_encode($tx->wallet?->user?->email ?? 'System Node') !!},
                        amount: {{ (float) $tx->amount }},
                        amount_formatted: {!! json_encode('Rp ' . number_format($tx->amount, 0, ',', '.')) !!},
                        status: {!! json_encode(__('Completed')) !!}
                    },
                    @endforeach
                ];

                if (this.exportFormat === 'csv') {
                    const headers = ['Timestamp', 'Transaction ID', 'Type', 'User / Entity', 'Amount (IDR)', 'Status'];
                    const csvRows = [
                        headers.join(','),
                        ...rawTxData.map(t => `"${t.timestamp}","${t.txn_id}","${t.type}","${t.user}","${t.amount_formatted}","${t.status}"`)
                    ];
                    const blob = new Blob([csvRows.join('\n')], { type: 'text/csv;charset=utf-8;' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', `lendflow_transaction_audit_${new Date().toISOString().slice(0,10)}.csv`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.triggerToast('{{ __("Transaction Audit Log exported to CSV successfully!") }}');
                } 
                else if (this.exportFormat === 'json') {
                    const jsonOutput = {
                        report_title: 'LendFlow Institutional Transaction Audit Log',
                        generated_at: new Date().toISOString(),
                        total_records: rawTxData.length,
                        total_volume_idr: rawTxData.reduce((acc, curr) => acc + curr.amount, 0),
                        transactions: rawTxData
                    };
                    const blob = new Blob([JSON.stringify(jsonOutput, null, 2)], { type: 'application/json' });
                    const url = URL.createObjectURL(blob);
                    const link = document.createElement('a');
                    link.setAttribute('href', url);
                    link.setAttribute('download', `lendflow_transaction_audit_${new Date().toISOString().slice(0,10)}.json`);
                    document.body.appendChild(link);
                    link.click();
                    document.body.removeChild(link);
                    this.triggerToast('{{ __("Transaction Audit Log exported to JSON successfully!") }}');
                } 
                else if (this.exportFormat === 'pdf') {
                    const printWindow = window.open('', '_blank');
                    const totalVol = rawTxData.reduce((acc, curr) => acc + curr.amount, 0);
                    const formattedTotalVol = new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', maximumFractionDigits: 0 }).format(totalVol);
                    
                    const rowsHtml = rawTxData.map(t => 
                        '<tr>' +
                            '<td>' + t.timestamp + '</td>' +
                            '<td><strong>' + t.txn_id + '</strong></td>' +
                            '<td><span class="badge badge-' + t.type.toLowerCase() + '">' + t.type + '</span></td>' +
                            '<td>' + t.user + '</td>' +
                            '<td><strong>' + t.amount_formatted + '</strong></td>' +
                            '<td style="color:#166534; font-weight:bold;">' + t.status + '</td>' +
                        '</tr>'
                    ).join('');

                    const printContent = 
                        '<!DOCTYPE html>' +
                        '<html>' +
                        '<head>' +
                            '<title>LendFlow Transaction Audit Log</title>' +
                            '<style>' +
                                'body { font-family: "Segoe UI", system-ui, -apple-system, sans-serif; color: #0f172a; margin: 0; padding: 40px; background: #fff; }' +
                                '.header { display: flex; justify-content: space-between; align-items: center; border-bottom: 3px solid #10b981; padding-bottom: 20px; margin-bottom: 24px; }' +
                                '.brand { font-size: 24px; font-weight: 900; color: #0f172a; letter-spacing: -0.5px; }' +
                                '.brand span { color: #10b981; }' +
                                '.title { text-align: right; }' +
                                '.title h1 { font-size: 18px; margin: 0; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }' +
                                '.title p { font-size: 12px; color: #64748b; margin: 4px 0 0 0; }' +
                                '.summary-bar { display: flex; gap: 20px; margin-bottom: 24px; background: #f8fafc; padding: 16px; border-radius: 12px; border: 1px solid #e2e8f0; }' +
                                '.summary-item { flex: 1; }' +
                                '.summary-item label { font-size: 10px; font-weight: 700; color: #64748b; text-transform: uppercase; display: block; }' +
                                '.summary-item val { font-size: 18px; font-weight: 900; color: #0f172a; display: block; margin-top: 4px; }' +
                                'table { width: 100%; border-collapse: collapse; margin-top: 10px; }' +
                                'th { background: #0f172a; color: #fff; font-size: 11px; font-weight: 700; text-transform: uppercase; padding: 10px 14px; text-align: left; }' +
                                'td { border-bottom: 1px solid #e2e8f0; font-size: 11px; padding: 10px 14px; color: #334155; font-weight: 500; }' +
                                'tr:nth-child(even) { background: #f8fafc; }' +
                                '.badge { display: inline-block; padding: 3px 8px; border-radius: 4px; font-size: 10px; font-weight: 700; }' +
                                '.badge-deposit { background: #dcfce7; color: #166534; }' +
                                '.badge-withdrawal { background: #ffe4e6; color: #9f1239; }' +
                                '.badge-disbursement { background: #f3e8ff; color: #6b21a8; }' +
                                '.badge-repayment { background: #dbeafe; color: #1e40af; }' +
                                '.footer { margin-top: 40px; border-top: 1px solid #e2e8f0; padding-top: 16px; font-size: 11px; color: #94a3b8; text-align: center; }' +
                            '</style>' +
                        '</head>' +
                        '<body>' +
                            '<div class="header">' +
                                '<div class="brand">Lend<span>Flow</span></div>' +
                                '<div class="title">' +
                                    '<h1>Transaction Audit Log</h1>' +
                                    '<p>Exported: ' + new Date().toLocaleString() + '</p>' +
                                '</div>' +
                            '</div>' +
                            '<div class="summary-bar">' +
                                '<div class="summary-item"><label>Total Audit Records</label><val>' + rawTxData.length + ' Transactions</val></div>' +
                                '<div class="summary-item"><label>Total Volume</label><val style="color: #10b981;">' + formattedTotalVol + '</val></div>' +
                                '<div class="summary-item"><label>Audit Security Status</label><val style="color: #3b82f6;">100% Immutable</val></div>' +
                            '</div>' +
                            '<table>' +
                                '<thead><tr><th>Timestamp</th><th>Txn ID</th><th>Type</th><th>User / Entity</th><th>Amount (IDR)</th><th>Status</th></tr></thead>' +
                                '<tbody>' + rowsHtml + '</tbody>' +
                            '</table>' +
                            '<div class="footer">Confidential Audit Report — LendFlow Institutional P2P Lending Platform &copy; ' + new Date().getFullYear() + '</div>' +
                            '<script>window.onload = function() { window.print(); }<' + '/script>' +
                        '</body>' +
                        '</html>';
                        
                    printWindow.document.write(printContent);
                    printWindow.document.close();
                    this.triggerToast('{{ __("PDF Transaction Audit Report generated! Printing window opened.") }}');
                }
            }
        };
    }
</script>
@endsection
