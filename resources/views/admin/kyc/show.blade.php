@extends('layouts.admin')

@section('content')
<div class="mx-auto max-w-4xl px-4 py-8 sm:px-6 lg:px-8">
    
    <!-- Navigation Back Link -->
    <div class="mb-4">
        <a href="{{ route('admin.kyc.index') }}" class="inline-flex items-center gap-2 text-sm font-semibold text-indigo-600 hover:text-indigo-800">
            &larr; Back to queue
        </a>
    </div>

    <!-- Applicant Overview -->
    <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white mb-6">
        <div class="px-6 py-6 sm:px-8 border-b border-gray-150 bg-gray-50/70">
            <h2 class="text-xl font-bold text-gray-900">Review Identity Verification</h2>
            <p class="mt-1 text-sm text-gray-500">Applicant: <strong>{{ $kyc->user->profile->full_name }}</strong> ({{ $kyc->user->email }})</p>
        </div>

        <div class="px-6 py-6 sm:px-8 space-y-4">
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Phone Number</span>
                    <span class="text-sm font-medium text-gray-900">{{ $kyc->user->profile->phone }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Application Status</span>
                    <span class="inline-flex items-center rounded-lg px-2 py-0.5 text-xs font-semibold mt-1
                        @if($kyc->status === 'pending') bg-amber-50 text-amber-700 ring-1 ring-amber-600/10
                        @elseif($kyc->status === 'approved') bg-emerald-50 text-emerald-700 ring-1 ring-emerald-600/10
                        @else bg-red-50 text-red-700 ring-1 ring-red-600/10 @endif">
                        {{ ucfirst($kyc->status) }}
                    </span>
                </div>
            </div>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Occupation</span>
                    <span class="text-sm font-medium text-gray-900">{{ $kyc->user->profile->occupation ?? '-' }}</span>
                </div>
                <div>
                    <span class="block text-xs font-semibold uppercase tracking-wider text-gray-500">Monthly Income</span>
                    <span class="text-sm font-bold text-indigo-600">
                        {{ $kyc->user->profile->monthly_income ? 'Rp ' . number_format($kyc->user->profile->monthly_income, 0, ',', '.') : '-' }}
                    </span>
                </div>
            </div>
        </div>
    </div>

    <!-- Document Images Review Section -->
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
        @foreach($kyc->documents as $doc)
            <div class="overflow-hidden shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-4">
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 mb-4">
                    <h3 class="text-sm font-bold uppercase tracking-wider text-gray-600">{{ strtoupper($doc->type) }} Document</h3>
                    <a href="{{ route('admin.kyc.document', $doc->id) }}" target="_blank"
                       class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">
                        Open in new tab
                    </a>
                </div>

                <!-- Safe Private Image Preview -->
                <div class="flex items-center justify-center rounded-xl bg-gray-50 border border-gray-200 overflow-hidden h-64">
                    @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['pdf']))
                        <div class="text-center p-4">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                            </svg>
                            <span class="mt-2 block text-xs font-medium text-gray-600">PDF Document</span>
                        </div>
                    @else
                        <img src="{{ route('admin.kyc.document', $doc->id) }}"
                             alt="{{ $doc->type }}"
                             class="h-full w-full object-contain">
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    @if($kyc->isPending())
        <!-- Review Action Panel -->
        <div class="shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-150 bg-white p-6 sm:p-8" x-data="{ showReject: false }">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Decide Verification Application</h3>
            
            <div class="flex gap-4">
                <!-- Approve action -->
                <form action="{{ route('admin.kyc.approve', $kyc->id) }}" method="POST" class="inline">
                    @csrf
                    <button type="submit"
                            class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-emerald-600/10 hover:bg-emerald-700 hover:scale-[1.02] transition-all">
                        Approve Application
                    </button>
                </form>

                <!-- Reject trigger button -->
                <button type="button" @click="showReject = !showReject"
                        class="inline-flex justify-center rounded-xl border border-red-200 bg-red-50 px-4 py-2.5 text-sm font-semibold text-red-600 hover:bg-red-100 transition-colors">
                    Reject Application
                </button>
            </div>

            <!-- Rejection Reason Form -->
            <div x-show="showReject" class="mt-6 pt-6 border-t border-gray-150" style="display: none;">
                <form action="{{ route('admin.kyc.reject', $kyc->id) }}" method="POST">
                    @csrf
                    <div>
                        <label for="rejected_reason" class="block text-xs font-semibold uppercase tracking-wider text-gray-600">Reason for Rejection</label>
                        <textarea name="rejected_reason" id="rejected_reason" rows="3" required
                                  class="mt-1.5 block w-full rounded-xl border-gray-300 px-4 py-2.5 text-sm focus:border-red-500 focus:ring-red-500"
                                  placeholder="e.g. KTP photo blurry or name mismatch."></textarea>
                    </div>
                    <div class="mt-4 flex justify-end">
                        <button type="submit"
                                class="inline-flex justify-center rounded-xl bg-red-600 px-4 py-2 text-sm font-semibold text-white hover:bg-red-700 shadow-md shadow-red-600/10 transition-all">
                            Submit Rejection
                        </button>
                    </div>
                </form>
            </div>
        </div>
    @endif

</div>
@endsection
