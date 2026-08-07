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
            &larr; {{ __('Back to Applications') }}
        </a>

        <div class="flex items-center gap-3">
            @if($loan->status !== 'pending')
                <a href="{{ route('loans.agreement', $loan->id) }}" target="_blank"
                   class="inline-flex items-center gap-1.5 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
                    {{ __('View Legal Agreement') }}
                </a>
            @endif
            <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl bg-emerald-700 px-4 py-2 text-xs font-bold text-white shadow-xs hover:bg-emerald-800 transition-all cursor-pointer">
                {{ __('Download Schedule (PDF)') }}
            </button>
        </div>
    </div>

    <!-- Header Banner -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Repayment Schedule & History') }}</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Loan') }} #LN-{{ substr($loan->id, 0, 8) }} • {{ $loan->purpose }} ({{ __('Grade') }} {{ $loan->risk_grade }} • {{ $loan->duration }} {{ __('Months') }})</p>
    </div>

    <!-- Alert Messages (Success & Error) -->
    @if(session('success'))
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/50 border border-emerald-200 dark:border-emerald-800 text-xs font-bold text-emerald-900 dark:text-emerald-300 flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <span>✅</span>
                <span>{{ session('success') }}</span>
            </div>
        </div>
    @endif

    @if($errors->any() || session('error'))
        <div class="p-4 rounded-xl bg-rose-50 dark:bg-rose-950/50 border border-rose-200 dark:border-rose-800 text-xs text-rose-900 dark:text-rose-300 space-y-2 shadow-xs">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2 font-bold">
                    <span>🚨</span>
                    <span>{{ session('error') ?? $errors->first() }}</span>
                </div>
                <a href="{{ route('wallet.index') }}" class="px-3 py-1.5 rounded-lg bg-emerald-700 text-white font-bold text-[11px] hover:bg-emerald-800 transition-colors shadow-xs">
                    {{ __('Deposit Funds') }} &rarr;
                </a>
            </div>
        </div>
    @endif

    <!-- 3 Stat Cards Row -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <!-- Card 1: Next Payment Due -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Next Payment Due') }}</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                    Rp {{ $nextInstallment ? __n(number_format($nextInstallment->total_due, 0, ',', '.')) : '0' }}
                </p>
                <p class="text-[11px] text-emerald-700 dark:text-emerald-400 font-bold mt-0.5">
                    {{ $nextInstallment ? __('Due') . ': ' . $nextInstallment->due_date->format('M d, Y') : __('All Payments Completed') }}
                </p>
            </div>
        </div>

        <!-- Card 2: Remaining Balance -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <div class="flex items-center justify-between">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Remaining Balance') }}</span>
                <span class="text-[11px] font-bold text-emerald-700 dark:text-emerald-400">{{ __n($percentPaid) }}% {{ __('Paid') }}</span>
            </div>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-slate-900 dark:text-slate-100">
                    Rp {{ __n(number_format($remainingBalance, 0, ',', '.')) }}
                </p>
                <div class="w-full bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 mt-2 overflow-hidden">
                    <div class="bg-emerald-700 h-1.5 rounded-full" style="width: {{ $percentPaid }}%"></div>
                </div>
            </div>
        </div>

        <!-- Card 3: Total Interest Paid -->
        <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('Total Interest Paid') }}</span>
            <div class="mt-3">
                <p class="text-2xl font-extrabold text-emerald-700 dark:text-emerald-400">
                    Rp {{ __n(number_format($totalInterestPaid, 0, ',', '.')) }}
                </p>
                <p class="text-[11px] text-slate-400 font-medium mt-0.5">{{ __('Avg.') }} {{ $loan->interest_rate }}% APR</p>
            </div>
        </div>
    </div>

    <!-- Main Grid: Table (Left) + Chat Box (Right) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Table: Amortization Schedule (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 px-6 py-4 bg-slate-50/50 dark:bg-slate-900/50">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('Installments Schedule') }}</h3>
                <span class="text-xs font-medium text-slate-500">{{ __('Showing 1-:count Payments', ['count' => $installments->count()]) }}</span>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 dark:bg-slate-800/60 border-b border-slate-100 dark:border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Installment') }}</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Due Date') }}</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Amount') }}</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Principal') }}</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Interest') }}</th>
                            <th class="py-3.5 px-4 whitespace-nowrap">{{ __('Status') }}</th>
                            <th class="py-3.5 px-5 whitespace-nowrap text-right min-w-[140px]">{{ __('Action') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 text-xs font-medium text-slate-700 dark:text-slate-300">
                        @foreach($installments as $inst)
                        <tr class="hover:bg-slate-50/80 dark:hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-4 font-extrabold text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                #{{ $inst->installment_number }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                {{ $inst->due_date->format('M d, Y') }}
                            </td>
                            <td class="py-3.5 px-4 font-extrabold text-slate-900 dark:text-slate-100 whitespace-nowrap">
                                Rp {{ __n(number_format($inst->total_due, 0, ',', '.')) }}
                            </td>
                            <td class="py-3.5 px-4 text-slate-600 dark:text-slate-400 whitespace-nowrap">
                                Rp {{ __n(number_format($inst->principal_amount, 0, ',', '.')) }}
                            </td>
                            <td class="py-3.5 px-4 text-emerald-700 dark:text-emerald-400 font-semibold whitespace-nowrap">
                                Rp {{ __n(number_format($inst->interest_amount, 0, ',', '.')) }}
                            </td>
                            <td class="py-3.5 px-4 whitespace-nowrap">
                                <span class="px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider
                                    @if($inst->isPaid()) bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-800
                                    @elseif($inst->isOverdue()) bg-rose-100 dark:bg-rose-950/60 text-rose-800 dark:text-rose-300 border border-rose-200 dark:border-rose-800
                                    @else bg-amber-100 dark:bg-amber-950/60 text-amber-800 dark:text-amber-300 border border-amber-200 dark:border-amber-800 @endif">
                                    {{ __(ucwords(str_replace('_', ' ', $inst->status))) }}
                                </span>
                            </td>
                            <td class="py-3.5 px-5 text-right whitespace-nowrap min-w-[140px]">
                                @if(!$inst->isPaid() && $loan->borrower_id === Auth::id())
                                    <form action="{{ route('repayments.pay', $inst->id) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="py-1.5 px-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer whitespace-nowrap">
                                            {{ __('Pay Now') }} &rarr;
                                        </button>
                                    </form>
                                @elseif($inst->isPaid())
                                    <a href="{{ route('repayments.receipt', $inst->id) }}" target="_blank" 
                                       class="inline-flex items-center justify-center gap-1.5 py-1.5 px-3 rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-emerald-800 dark:text-emerald-300 font-bold text-xs hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-xs whitespace-nowrap">
                                        🧾 {{ __('Receipt') }}
                                    </a>
                                @else
                                    <span class="text-slate-400 font-semibold text-[11px] whitespace-nowrap">{{ __('Pending') }}</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Live Chat: Discussion Box (Spans 4 Cols) -->
        <div class="lg:col-span-4 flex flex-col h-[520px] rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs overflow-hidden">
            <!-- Header -->
            <div class="px-6 py-4 border-b border-slate-100 dark:border-slate-800 bg-slate-50 dark:bg-slate-800/40 flex items-center justify-between">
                <div>
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ __('Internal Loan Discussion') }}</h3>
                    <p class="text-[10px] text-slate-400 font-medium">{{ __('Lender & Borrower Chat') }}</p>
                </div>
                <span class="h-2 w-2 rounded-full bg-emerald-600 animate-pulse" title="Connected"></span>
            </div>

            <!-- Messages Stream -->
            <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-3 bg-slate-50/50 dark:bg-slate-900/50 text-xs">
                <p class="text-center text-slate-400 py-10 font-medium">{{ __('Loading messages...') }}</p>
            </div>

            <!-- Chat Input Form -->
            <form id="chatForm" class="p-3 border-t border-slate-100 dark:border-slate-800 bg-white dark:bg-slate-900 flex gap-2">
                <input type="text" id="chatInput" placeholder="{{ __('Type a message...') }}" 
                       class="flex-1 text-xs rounded-xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 px-3.5 py-2 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                <button type="submit" class="py-2 px-3 rounded-xl bg-emerald-700 text-white font-bold hover:bg-emerald-800 shadow-xs cursor-pointer">
                    {{ __('Send') }}
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
                chatMessages.innerHTML = `<p class="text-center text-slate-400 py-10 font-medium">{{ __('No messages yet. Send your first message!') }}</p>`;
                return;
            }

            chatMessages.innerHTML = data.messages.map(msg => `
                <div class="flex flex-col ${msg.is_me ? 'items-end' : 'items-start'}">
                    <span class="text-[10px] text-slate-400 mb-0.5 px-1 font-bold">${msg.sender_name}</span>
                    <div class="max-w-[85%] rounded-2xl px-3.5 py-2 text-xs shadow-xs
                        ${msg.is_me ? 'bg-emerald-700 text-white rounded-tr-none' : 'bg-white dark:bg-slate-800 border border-slate-200 dark:border-slate-700 text-slate-800 dark:text-slate-100 rounded-tl-none'}">
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
