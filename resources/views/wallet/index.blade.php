@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Title info -->
    <div class="mb-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">My Virtual Wallet</h2>
        <p class="mt-2 text-sm text-gray-500">Manage your capital, deposits, withdrawals, and trace transaction records.</p>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Balance Summary & Actions (Grid spans 1 column) -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- Balance Card -->
            @foreach($wallets as $wallet)
                <div class="overflow-hidden shadow-xl shadow-indigo-600/5 rounded-2xl border border-indigo-100 bg-gradient-to-br from-indigo-600 to-indigo-800 p-6 text-white">
                    <div class="flex items-center justify-between border-b border-indigo-500/30 pb-3 mb-4">
                        <span class="text-xs font-semibold uppercase tracking-wider opacity-85">Available Capital</span>
                        <span class="rounded-lg bg-indigo-500/30 px-2 py-0.5 text-xs font-bold">{{ $wallet->currency->code }}</span>
                    </div>
                    <div>
                        <p class="text-3xl font-black tracking-tight">
                            @if($wallet->currency->type === 'fiat')
                                Rp {{ number_format($wallet->available_balance, 0, ',', '.') }}
                            @else
                                {{ number_format($wallet->available_balance, $wallet->currency->decimal_places) }}
                            @endif
                        </p>
                        <div class="mt-4 flex justify-between text-xs opacity-80 pt-3 border-t border-indigo-500/30">
                            <span>Hold Balance:
                                @if($wallet->currency->type === 'fiat')
                                    Rp {{ number_format($wallet->hold_balance, 0, ',', '.') }}
                                @else
                                    {{ number_format($wallet->hold_balance, $wallet->currency->decimal_places) }}
                                @endif
                            </span>
                            <span>Total:
                                @if($wallet->currency->type === 'fiat')
                                    Rp {{ number_format($wallet->total_balance, 0, ',', '.') }}
                                @else
                                    {{ number_format($wallet->total_balance, $wallet->currency->decimal_places) }}
                                @endif
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach

            <!-- Action Tabs (Deposit / Withdraw Forms) -->
            <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-6" x-data="{ tab: 'deposit' }">
                <div class="flex border-b border-gray-100 pb-3 mb-6">
                    <button @click="tab = 'deposit'"
                            :class="tab === 'deposit' ? 'text-indigo-600 border-b-2 border-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
                            class="flex-1 pb-2 text-center text-sm font-semibold focus:outline-none transition-all">
                        Deposit Funds
                    </button>
                    <button @click="tab = 'withdraw'"
                            :class="tab === 'withdraw' ? 'text-indigo-600 border-b-2 border-indigo-600 font-bold' : 'text-gray-400 hover:text-gray-600'"
                            class="flex-1 pb-2 text-center text-sm font-semibold focus:outline-none transition-all">
                        Withdraw Funds
                    </button>
                </div>

                <!-- Deposit Form -->
                <div x-show="tab === 'deposit'">
                    <form action="{{ route('wallet.deposit') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="dep_currency_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Select Currency</label>
                            <select name="currency_id" id="dep_currency_id" class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}">{{ $curr->code }} - {{ $curr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="dep_amount" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Amount</label>
                            <input type="number" name="amount" id="dep_amount" required min="10000"
                                   class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. 100000">
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-all">
                            Submit Deposit
                        </button>
                    </form>
                </div>

                <!-- Withdraw Form -->
                <div x-show="tab === 'withdraw'" style="display: none;">
                    <form action="{{ route('wallet.withdraw') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="wd_currency_id" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Select Currency</label>
                            <select name="currency_id" id="wd_currency_id" class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500">
                                @foreach($currencies as $curr)
                                    <option value="{{ $curr->id }}">{{ $curr->code }} - {{ $curr->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="wd_amount" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Amount</label>
                            <input type="number" name="amount" id="wd_amount" required min="10000"
                                   class="mt-1.5 block w-full rounded-xl border-gray-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-indigo-500"
                                   placeholder="e.g. 50000">
                        </div>
                        <button type="submit"
                                class="w-full rounded-xl bg-indigo-600 px-4 py-3 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-all">
                            Submit Withdrawal
                        </button>
                    </form>
                </div>
            </div>

        </div>

        <!-- Right Side: Transaction Ledger (Grid spans 2 columns) -->
        <div class="lg:col-span-2">
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
                <div class="px-6 py-5 border-b border-gray-150 flex items-center justify-between">
                    <h3 class="text-base font-bold text-gray-900">Transaction History</h3>
                </div>
                
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50/50">
                        <tr>
                            <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Transaction</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Amount</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Balances (Before / After)</th>
                            <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Date</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-150 bg-white">
                        @forelse($transactions as $tx)
                            <tr class="hover:bg-gray-50/40 transition-colors">
                                <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                    <div class="flex items-center gap-3">
                                        <div class="flex h-8 w-8 items-center justify-center rounded-lg text-xs font-bold uppercase
                                            @if(in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund'])) bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                                            @else bg-red-50 text-red-700 ring-1 ring-red-600/10 @endif">
                                            {{ substr($tx->type, 0, 3) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-900 capitalize">{{ str_replace('_', ' ', $tx->type) }}</div>
                                            <div class="text-xs text-gray-500 truncate max-w-[200px]">{{ $tx->description }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm font-bold">
                                    <span class="{{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? 'text-emerald-600' : 'text-red-600' }}">
                                        {{ in_array($tx->type, ['deposit', 'repayment', 'interest', 'refund']) ? '+' : '-' }}
                                        {{ number_format($tx->amount, $tx->wallet->currency->decimal_places) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-xs text-gray-500">
                                    <div class="opacity-95">Before: {{ number_format($tx->balance_before, $tx->wallet->currency->decimal_places) }}</div>
                                    <div class="font-semibold text-gray-700 mt-0.5">After: {{ number_format($tx->balance_after, $tx->wallet->currency->decimal_places) }}</div>
                                </td>
                                <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                    {{ $tx->created_at->format('M d, Y H:i') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-10 text-center text-sm text-gray-500">
                                    No transactions recorded yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>

                <!-- Pagination -->
                @if($transactions->hasPages())
                    <div class="border-t border-gray-150 px-6 py-3">
                        {{ $transactions->links() }}
                    </div>
                @endif
            </div>
        </div>

    </div>

</div>
@endsection
