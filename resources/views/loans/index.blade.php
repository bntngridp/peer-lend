@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-7xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="sm:flex sm:items-center sm:justify-between mb-8 pb-6 border-b border-gray-200">
        <div>
            <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">My Loan Applications</h2>
            <p class="mt-2 text-sm text-gray-500">Track verification approvals, funding percentage progress, and manage active installments.</p>
        </div>
        <div class="mt-4 sm:ml-16 sm:mt-0">
            <a href="{{ route('loans.create') }}" class="inline-flex items-center justify-center rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-all">
                Apply for Loan
            </a>
        </div>
    </div>

    <!-- Loan List Card -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50/50">
                <tr>
                    <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Loan Details</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Amount & Interest</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                    <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Funding Progress</th>
                    <th scope="col" class="relative py-3.5 pl-3 pr-6 text-right">
                        <span class="sr-only">Actions</span>
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-150 bg-white">
                @forelse($loans as $loan)
                    <tr class="hover:bg-gray-50/30 transition-colors">
                        <td class="whitespace-nowrap py-4 pl-6 pr-3">
                            <div class="flex items-center gap-3">
                                <div>
                                    <div class="text-sm font-bold text-gray-900">{{ $loan->purpose }}</div>
                                    <div class="text-xs text-gray-500">Category: {{ $loan->category->name }} • {{ $loan->duration }} Months</div>
                                </div>
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-3 py-4 text-sm">
                            <div class="font-bold text-indigo-600">Rp {{ number_format($loan->amount, 0, ',', '.') }}</div>
                            <div class="text-xs text-gray-500">Rate: {{ $loan->interest_rate }}% (Grade {{ $loan->risk_grade }})</div>
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
                        <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                            <div class="w-48">
                                <div class="flex items-center justify-between text-xs font-semibold mb-1">
                                    <span>{{ $loan->funded_percentage }}% funded</span>
                                </div>
                                <div class="w-full bg-gray-100 rounded-full h-1.5 overflow-hidden">
                                    <div class="bg-indigo-600 h-1.5 rounded-full" style="width: {{ min(100, $loan->funded_percentage) }}%"></div>
                                </div>
                            </div>
                        </td>
                        <td class="relative whitespace-nowrap py-4 pl-3 pr-6 text-right text-sm font-semibold">
                            @if($loan->status === 'active')
                                <a href="{{ route('loans.installments', $loan->id) }}" class="text-indigo-600 hover:text-indigo-900">View Installments</a>
                            @else
                                <span class="text-gray-400 font-normal">No action required</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="py-10 text-center text-sm text-gray-500">
                            You have not submitted any loan applications yet.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        <!-- Pagination -->
        @if($loans->hasPages())
            <div class="border-t border-gray-150 px-6 py-3">
                {{ $loans->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
