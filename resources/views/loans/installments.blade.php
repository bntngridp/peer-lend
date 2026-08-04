@extends('layouts.app')

@section('content')
@php
    $nextInstallment = $installments->firstWhere('status', 'pending') ?? $installments->firstWhere('status', 'overdue');
    $paidSum = $installments->where('status', 'paid')->sum('principal_amount');
    $totalPrincipal = $loan->amount;
    $remainingBalance = max(0, $totalPrincipal - $paidSum);
    $percentPaid = $totalPrincipal > 0 ? round(($paidSum / $totalPrincipal) * 100) : 0;
    $totalInterestPaid = $installments->where('status', 'paid')->sum('interest_amount');
@endphp

<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Top Back Navigation & Action Bar -->
    <div class="flex items-center justify-between">
        <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
            &larr; Back to Applications
        </a>

        <div class="flex items-center gap-3">
            @if($loan->status !== 'pending')
                <a href="{{ route('loans.agreement', $loan->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                    View Legal Agreement
                </a>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all">
                Download Schedule (PDF)
            </button>
        </div>
    </div>

    <!-- Header Banner -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Repayment Schedule &amp; History</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">Loan #LN-{{ substr($loan->id, 0, 8) }} • {{ $loan->purpose }} (Grade {{ $loan->risk_grade }} • {{ $loan->duration }} Months)</p>
    </div>

    <!-- 3 Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Next Payment Due -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Next Payment Due</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900">
                    Rp {{ $nextInstallment ? number_format($nextInstallment->total_due, 0, ',', '.') : '0' }}
                </p>
                <p class="text-[11px] text-emerald-700 font-bold mt-0.5">
                    {{ $nextInstallment ? 'Due: ' . $nextInstallment->due_date->format('Oct d, Y') : 'All Payments Completed' }}
                </p>
            </div>
        </div>

        <!-- Card 2: Remaining Balance -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Remaining Balance</span>
                <span class="text-[11px] font-bold text-emerald-700">{{ $percentPaid }}% Paid</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900">
                    Rp {{ number_format($remainingBalance, 0, ',', '.') }}
                </p>
                <div class="w-full bg-slate-100 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ $percentPaid }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Interest Paid -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Total Interest Paid</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-emerald-700">
                    Rp {{ number_format($totalInterestPaid, 0, ',', '.') }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">Avg. {{ $loan->interest_rate }}% APR</p>
            </div>
        </div>
    </div>

    <!-- Main Grid: Table (Left) + Chat Box (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Table: Amortization Schedule (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">Installments Schedule</h3>
                <span class="text-xs font-medium text-slate-500">Showing 1-{{ $installments->count() }} Payments</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-6">Installment</th>
                            <th class="py-3.5 px-6">Due Date</th>
                            <th class="py-3.5 px-6">Amount</th>
                            <th class="py-3.5 px-6">Principal</th>
                            <th class="py-3.5 px-6">Interest</th>
                            <th class="py-3.5 px-6">Status</th>
                            <th class="py-3.5 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                        @foreach($installments as $inst)
                        <tr class="hover:bg-slate-50/80 transition-colors">
                            <td class="py-4 px-6 font-extrabold text-slate-900">
                                #{{ $inst->installment_number }}
                            </td>
                            <td class="py-4 px-6 font-semibold text-slate-600">
                                {{ $inst->due_date->format('M d, Y') }}
                            </td>
                            <td class="py-4 px-6 font-extrabold text-slate-900">
                                Rp {{ number_format($inst->total_due, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-slate-600">
                                Rp {{ number_format($inst->principal_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6 text-emerald-700 font-semibold">
                                Rp {{ number_format($inst->interest_amount, 0, ',', '.') }}
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider
                                    @if($inst->isPaid()) bg-emerald-100 text-emerald-800 border border-emerald-200
                                    @elseif($inst->isOverdue()) bg-rose-100 text-rose-800 border border-rose-200
                                    @else bg-amber-100 text-amber-800 border border-amber-200 @endif">
                                    {{ $inst->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 text-right">
                                @if(!$inst->isPaid())
                                    <form action="{{ route('repayments.pay', $inst->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                                            Pay Now &rarr;
                                        </button>
                                    </form>
                                @else
                                    <span class="text-slate-400 font-semibold text-[11px]">Paid</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Live Chat: Discussion Box (Spans 4 Cols) -->
        <div class="lg:col-span-4 flex flex-col h-[520px] rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 bg-slate-50 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900">Internal Loan Discussion</h3>
                    <p class="text-[10px] text-slate-400 font-medium">Lender &amp; Borrower Chat</p>
                </div>
                <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse" title="Connected"></span>
            </div>

            <!-- Messages Stream -->
            <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50 text-xs">
                <p class="text-center text-slate-400 py-10 font-medium">Loading messages...</p>
            </div>

            <!-- Chat Input Form -->
            <form id="chatForm" class="p-3 border-t border-slate-100 bg-white flex gap-2">
                <input type="text" id="chatInput" placeholder="Ketik pesan..." 
                       class="flex-1 text-xs rounded-xl border border-slate-200 px-3.5 py-2 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <button type="submit" class="py-2 px-3 rounded-xl bg-emerald-700 text-white font-bold hover:bg-emerald-800 shadow-xs">
                    Send
                </button>
            </form>
        </div>

    </div>

</div>

<!-- Live Chat Polling Script -->
<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const fetchUrl = '{{ route("loans.messages.fetch", $loan->id) }}';
    const sendUrl = '{{ route("loans.messages.send", $loan->id) }}';

    async function fetchMessages() {
        try {
            const response = await fetch(fetchUrl);
            const data = await response.json();

            if (!data.success) return;

            if (data.messages.length === 0) {
                chatMessages.innerHTML = `<p class="text-center text-slate-400 py-10 font-medium">Belum ada diskusi. Ketik pesan pertama Anda!</p>`;
                return;
            }

            chatMessages.innerHTML = data.messages.map(msg => `
                <div class="flex flex-col ${msg.is_me ? 'items-end' : 'items-start'}">
                    <span class="text-[10px] text-slate-400 mb-0.5 px-1 font-bold">${msg.sender_name}</span>
                    <div class="max-w-[85%] rounded-2xl px-3.5 py-2 text-xs shadow-xs
                        ${msg.is_me ? 'bg-emerald-700 text-white rounded-tr-none' : 'bg-white border border-slate-200 text-slate-800 rounded-tl-none'}">
                        <p class="break-words font-medium">${msg.message}</p>
                        <span class="block text-[9px] text-right mt-1 opacity-70">${msg.time}</span>
                    </div>
                </div>
            `).join('');
        } catch (err) {
            console.error('Failed to fetch messages:', err);
        }
    }

    chatForm.addEventListener('submit', async (e) => {
        e.preventDefault();
        const msgText = chatInput.value.trim();
        if (!msgText) return;

        chatInput.value = '';

        try {
            const response = await fetch(sendUrl, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json',
                },
                body: JSON.stringify({ message: msgText })
            });
            const data = await response.json();
            if (data.success) fetchMessages();
        } catch (err) {
            console.error('Failed to send message:', err);
        }
    });

    fetchMessages();
    setInterval(fetchMessages, 3000);
});
</script>
@endsection
