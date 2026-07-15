@extends('layouts.admin')

@section('content')
<div class="px-4 sm:px-6 lg:px-8">
    
    <!-- Title info -->
    <div class="sm:flex sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-gray-900">KYC Verifications Queue</h1>
            <p class="mt-2 text-sm text-gray-700">Review, approve, or reject user identity document submissions for regulatory compliance.</p>
        </div>
    </div>

    <!-- Table container -->
    <div class="mt-8 flex flex-col">
        <div class="-mx-4 -my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle md:px-6 lg:px-8">
                <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50/70">
                            <tr>
                                <th scope="col" class="py-3.5 pl-4 pr-3 text-left text-xs font-bold uppercase tracking-wider text-gray-500 sm:pl-6">Borrower / Lender</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Phone</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Status</th>
                                <th scope="col" class="px-3 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-gray-500">Submitted At</th>
                                <th scope="col" class="relative py-3.5 pl-3 pr-4 sm:pr-6">
                                    <span class="sr-only">Actions</span>
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-150 bg-white">
                            @forelse($kycs as $kyc)
                                <tr class="hover:bg-gray-50/50 transition-colors">
                                    <td class="whitespace-nowrap py-4 pl-4 pr-3 sm:pl-6">
                                        <div class="flex items-center gap-3">
                                            <div class="h-9 w-9 rounded-xl bg-gray-100 font-semibold text-gray-700 flex items-center justify-center border border-gray-200">
                                                {{ strtoupper(substr($kyc->user->profile->full_name ?? $kyc->user->email, 0, 2)) }}
                                            </div>
                                            <div>
                                                <div class="text-sm font-bold text-gray-900">{{ $kyc->user->profile->full_name ?? 'Name Unspecified' }}</div>
                                                <div class="text-xs text-gray-500">{{ $kyc->user->email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $kyc->user->profile->phone ?? '-' }}
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm">
                                        @if($kyc->status === 'pending')
                                            <span class="inline-flex items-center rounded-lg bg-amber-50 px-2 py-1 text-xs font-semibold text-amber-700 ring-1 ring-inset ring-amber-600/10">Pending review</span>
                                        @elseif($kyc->status === 'approved')
                                            <span class="inline-flex items-center rounded-lg bg-emerald-50 px-2 py-1 text-xs font-semibold text-emerald-700 ring-1 ring-inset ring-emerald-600/10">Approved</span>
                                        @else
                                            <span class="inline-flex items-center rounded-lg bg-red-50 px-2 py-1 text-xs font-semibold text-red-700 ring-1 ring-inset ring-red-600/10">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="whitespace-nowrap px-3 py-4 text-sm text-gray-500">
                                        {{ $kyc->created_at->format('M d, Y H:i') }}
                                    </td>
                                    <td class="relative whitespace-nowrap py-4 pl-3 pr-4 text-right text-sm font-medium sm:pr-6">
                                        <a href="{{ route('admin.kyc.show', $kyc->id) }}" class="text-indigo-600 hover:text-indigo-900 font-semibold">Review Application</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-10 text-center text-sm text-gray-500">
                                        No KYC applications in the queue.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    @if($kycs->hasPages())
                        <div class="border-t border-gray-150 px-4 py-3 sm:px-6">
                            {{ $kycs->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</div>
@endsection
