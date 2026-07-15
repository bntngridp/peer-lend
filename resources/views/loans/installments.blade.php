@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-5xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Navigation Back Link -->
    <div class="mb-6">
        <a href="{{ route('loans.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to applications
        </a>
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

    <!-- Installment Schedule Table -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
        <div class="px-6 py-5 border-b border-gray-150 flex items-center justify-between">
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
                                <span class="text-gray-400 font-normal">Setted on {{ $inst->paid_at->format('M d, Y') }}</span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

</div>
@endsection
