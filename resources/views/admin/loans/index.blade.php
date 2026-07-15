@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">Loan Applications Control Queue</h1>
            <p class="mt-2 text-sm text-gray-700">Review pending borrowers' applications to approve them into the marketplace, or trigger funding disbursements.</p>
        </div>
    </div>

    <!-- Table -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/70">
                <tr>
                    <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Applicant</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Target amount</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Collateral</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Funding Progress</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-150 bg-white">
                @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50/40 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-xl bg-gray-100 font-semibold text-gray-700 flex items-center justify-center border border-gray-200">
                                    {{ strtoupper(substr($loan->borrower->profile->full_name ?? $loan->borrower->email, 0, 2)) }}
                                </div>
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $loan->borrower->profile->full_name ?? 'Applicant' }}</div>
                                    <div class="text-xs text-gray-500">{{ $loan->purpose }}</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <div class="font-bold text-gray-900">Rp {{ number_format($loan->amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">{{ $loan->interest_rate }}% APR (Grade {{ $loan->risk_grade }})</div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            @if($loan->isCryptoLoan())
                                <span class="inline-flex items-center rounded-lg bg-indigo-50 px-2 py-0.5 text-xs font-semibold text-indigo-700 ring-1 ring-inset ring-indigo-600/10">
                                    {{ number_format($loan->collateral_amount, $loan->collateralCurrency->decimal_places) }} {{ $loan->collateralCurrency->code }}
                                </span>
                            @else
                                <span class="text-xs text-gray-400">Unsecured / Fiat</span>
                            @endif
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <div class="flex items-center gap-2">
                                <div class="w-24 bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                                <span class="text-xs font-semibold text-gray-700">{{ $loan->funded_percentage }}%</span>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold uppercase tracking-wider
                                @if($loan->status === 'pending') bg-amber-50 text-amber-700 ring-1 ring-amber-600/10
                                @elseif($loan->status === 'open_funding') bg-indigo-50 text-indigo-700 ring-1 ring-indigo-600/10
                                @elseif($loan->status === 'active') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                                @elseif($loan->status === 'completed') bg-gray-50 text-gray-700 ring-1 ring-gray-600/10
                                @else bg-red-50 text-red-700 ring-1 ring-red-600/10 @endif">
                                {{ str_replace('_', ' ', $loan->status) }}
                            </span>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-semibold">
                            <a href="{{ route('admin.loans.show', $loan->id) }}" class="text-indigo-600 hover:text-indigo-900">
                                Review Application
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="py-10 text-center text-sm text-gray-500">
                            No loan applications in the queue.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($loans->hasPages())
            <div class="border-t border-gray-150 px-6 py-3">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
