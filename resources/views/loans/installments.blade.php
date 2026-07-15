@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Navigation Back Link & Agreement -->
    <div class="mb-6 flex justify-between items-center">
        <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to applications
        </a>
        @if($loan->status !== 'pending')
            <a href="{{ route('loans.agreement', $loan->id) }}" target="_blank"
               class="inline-flex items-center gap-1.5 rounded-xl border border-indigo-200 bg-indigo-50/50 px-4 py-2 text-xs font-bold text-indigo-700 hover:bg-indigo-100/70 transition-all">
                📜 View Legal Agreement
            </a>
        @endif
    </div>

    <!-- Loan Amortization Overview Card -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white mb-8">
        <div class="px-6 py-6 sm:px-8 border-b border-gray-150 bg-gray-50/70">
            <h2 class="text-xl font-bold text-gray-900">Installment Schedule Amortization</h2>
            <p class="mt-1 text-sm text-gray-500">Loan Purpose: <strong>{{ $loan->purpose }}</strong> (Grade: {{ $loan->risk_grade }} • Tenor: {{ $loan->duration }} Months)</p>
        </div>

        <div class="px-6 py-6 sm:px-8 grid grid-cols-2 sm:grid-cols-4 gap-6">
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Total Borrowed</span>
                <span class="text-lg font-extrabold text-gray-950">Rp {{ number_format($loan->amount, 0, ',', '.') }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Annual Return (APR)</span>
                <span class="text-lg font-extrabold text-indigo-600">{{ $loan->interest_rate }}%</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Interest Currency</span>
                <span class="text-lg font-extrabold text-gray-950">{{ $loan->currency->code }}</span>
            </div>
            <div>
                <span class="block text-xs font-semibold uppercase tracking-wider text-gray-400">Loan Status</span>
                <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-0.5 text-xs font-bold text-emerald-700 ring-1 ring-inset ring-emerald-600/10 uppercase tracking-wider mt-1">
                    {{ $loan->status }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Installment Schedule Table (Col-span 2) -->
        <div class="lg:col-span-2 space-y-6">
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
                <div class="px-6 py-5 border-b border-gray-150">
                    <h3 class="text-base font-bold text-gray-900">Payment Schedule</h3>
                </div>

                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">No.</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Due Date</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Principal</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Interest</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Late Penalty</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Total Due</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                            <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">
                                <span class="sr-only">Actions</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 bg-white">
                        @foreach($installments as $inst)
                            <tr class="hover:bg-gray-50/30 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3 text-sm font-bold text-gray-900">
                                    #{{ $inst->installment_number }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $inst->due_date->format('M d, Y') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                                    Rp {{ number_format($inst->principal_amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-600 font-medium">
                                    Rp {{ number_format($inst->interest_amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-red-600 font-semibold">
                                    Rp {{ number_format($inst->penalty_amount, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-900 font-black">
                                    Rp {{ number_format($inst->total_due, 2, ',', '.') }}
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm">
                                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold uppercase tracking-wider
                                        @if($inst->isPaid()) bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                                        @elseif($inst->isOverdue()) bg-red-50 text-red-700 ring-1 ring-red-600/10
                                        @else bg-amber-50 text-amber-700 ring-1 ring-amber-600/10 @endif">
                                        {{ $inst->status }}
                                    </span>
                                </td>
                                <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-semibold">
                                    @if(!$inst->isPaid())
                                        <form action="{{ route('repayments.pay', $inst->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit"
                                                    class="inline-flex justify-center rounded-xl bg-indigo-600 px-3 py-1.5 text-xs font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors">
                                                Pay Now
                                            </button>
                                        </form>
                                    @else
                                        <span class="text-gray-400 font-normal">Settled on {{ $inst->paid_at->format('M d, Y') }}</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Right Side: Interactive Chat Box (Col-span 1) -->
        <div class="lg:col-span-1">
            <div class="flex flex-col h-[500px] shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white overflow-hidden">
                
                <!-- Chat Header -->
                <div class="px-6 py-4 border-b border-gray-150 bg-gray-50 flex items-center justify-between">
                    <div>
                        <h3 class="text-sm font-bold text-gray-900">Internal Loan Discussion</h3>
                        <p class="text-xs text-gray-500">Lender & Borrower Chat</p>
                    </div>
                    <span class="h-2 w-2 rounded-full bg-emerald-500" title="Connected"></span>
                </div>

                <!-- Messages Area -->
                <div id="chatMessages" class="flex-1 p-4 overflow-y-auto space-y-4 bg-gray-50/40">
                    <p class="text-center text-xs text-gray-400 py-10">Loading messages...</p>
                </div>

                <!-- Input Area -->
                <form id="chatForm" class="p-4 border-t border-gray-150 bg-white flex gap-2">
                    <input type="text" id="chatInput" placeholder="Ketik pesan..." 
                           class="flex-1 text-sm rounded-xl border border-gray-200 px-4 py-2.5 outline-none focus:border-indigo-500 focus:ring-2 focus:ring-indigo-500/10 transition-all">
                    <button type="submit" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-white shadow hover:bg-indigo-700 transition-colors">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 12L3.269 3.126A59.768 59.768 0 0121.485 12 59.77 59.77 0 013.27 20.876L5.999 12zm0 0h7.5" />
                        </svg>
                    </button>
                </form>
            </div>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const chatInput = document.getElementById('chatInput');
    const fetchUrl = '{{ route("loans.messages.fetch", $loan->id) }}';
    const sendUrl = '{{ route("loans.messages.send", $loan->id) }}';

    // Fetch messages function
    async function fetchMessages() {
        try {
            const response = await fetch(fetchUrl);
            const data = await response.json();

            if (!data.success) return;

            if (data.messages.length === 0) {
                chatMessages.innerHTML = `<p class="text-center text-xs text-gray-400 py-10">Belum ada diskusi. Mulai ketik pesan pertama Anda!</p>`;
                return;
            }

            const currentScroll = chatMessages.scrollTop + chatMessages.clientHeight;
            const isNearBottom = chatMessages.scrollHeight - currentScroll < 50;

            chatMessages.innerHTML = data.messages.map(msg => `
                <div class="flex flex-col ${msg.is_me ? 'items-end' : 'items-start'}">
                    <span class="text-[10px] text-gray-400 mb-0.5 px-1">${msg.sender_name}</span>
                    <div class="max-w-[85%] rounded-2xl px-4 py-2 text-sm shadow-sm
                        ${msg.is_me ? 'bg-indigo-600 text-white rounded-tr-none' : 'bg-white border border-gray-150 text-gray-800 rounded-tl-none'}">
                        <p class="break-words">${msg.message}</p>
                        <span class="block text-[9px] text-right mt-1 opacity-70">${msg.time}</span>
                    </div>
                </div>
            `).join('');

            if (isNearBottom || chatMessages.innerHTML.includes('Loading messages...')) {
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }
        } catch (err) {
            console.error('Failed to fetch messages:', err);
        }
    }

    // Send message function
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

            if (data.success) {
                fetchMessages();
            }
        } catch (err) {
            console.error('Failed to send message:', err);
        }
    });

    // Initial load and periodic polling
    fetchMessages();
    setInterval(fetchMessages, 3000);
});
</script>
@endsection
