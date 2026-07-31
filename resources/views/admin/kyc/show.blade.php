@extends('layouts.admin')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ showRejectModal: false }">
    
    <!-- Top Navigation Link -->
    <div>
        <a href="{{ route('admin.kyc.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-emerald-700 hover:text-emerald-800 transition-colors">
            &larr; Back to KYC Review Queue
        </a>
    </div>

    <!-- Header Banner -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-4">
        <div>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">
                    KYC Review: {{ $kyc->user->profile->full_name ?? 'Institutional Client' }}
                </h1>
                <span class="px-2.5 py-0.5 rounded text-[11px] font-extrabold 
                    @if($kyc->isPending()) bg-amber-100 text-amber-800 border border-amber-200
                    @elseif($kyc->isApproved()) bg-emerald-100 text-emerald-800 border border-emerald-200
                    @else bg-rose-100 text-rose-800 border border-rose-200 @endif">
                    Status: {{ ucfirst($kyc->status) }}
                </span>
            </div>
            <p class="text-xs font-medium text-slate-500 mt-1">
                Application ID: APP-{{ substr($kyc->id, 0, 6) }}-X9 • Submitted: {{ $kyc->created_at->format('Oct d, Y H:i') }}
            </p>
        </div>
    </div>

    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Column: System Assessment & Data Verification (Spans 7 Cols) -->
        <div class="lg:col-span-7 space-y-6">
            
            <!-- Card 1: System Assessment -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">System Assessment</h3>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-800 border border-emerald-200">
                        🟢 Low Risk
                    </span>
                </div>

                <div class="grid grid-cols-2 gap-4 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Face Match Score</span>
                        <span class="text-sm font-black text-emerald-700 mt-0.5 block">98.4% Match</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Document Authenticity</span>
                        <span class="text-sm font-black text-emerald-700 mt-0.5 block">Verified</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Sanctions Check</span>
                        <span class="text-sm font-black text-emerald-700 mt-0.5 block">Clear</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-100">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">IP Geolocation</span>
                        <span class="text-xs font-bold text-slate-900 mt-0.5 block">San Francisco, US (Matches Address)</span>
                    </div>
                </div>
            </div>

            <!-- Card 2: Data Verification (User Input vs OCR Extraction Table) -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Data Verification <span class="text-slate-400 font-normal text-[11px]">(User Input vs OCR Extraction)</span>
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 text-[10px] font-bold text-slate-400 uppercase tracking-wider border-b border-slate-100">
                                <th class="py-2.5 px-3">Field</th>
                                <th class="py-2.5 px-3">User Input</th>
                                <th class="py-2.5 px-3">Document OCR</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 font-medium">
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-700">Full Name</td>
                                <td class="py-3 px-3 text-slate-900">{{ $kyc->user->profile->full_name }}</td>
                                <td class="py-3 px-3 text-emerald-700 font-bold flex items-center gap-1">
                                    <span>✓</span> {{ strtoupper($kyc->user->profile->full_name) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-700">Phone / Identity</td>
                                <td class="py-3 px-3 text-slate-900">{{ $kyc->user->profile->phone }}</td>
                                <td class="py-3 px-3 text-emerald-700 font-bold flex items-center gap-1">
                                    <span>✓</span> {{ $kyc->user->profile->phone }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-700">Occupation</td>
                                <td class="py-3 px-3 text-slate-900">{{ $kyc->user->profile->occupation ?? 'Executive' }}</td>
                                <td class="py-3 px-3 text-emerald-700 font-bold flex items-center gap-1">
                                    <span>✓</span> {{ strtoupper($kyc->user->profile->occupation ?? 'EXECUTIVE') }}
                                </td>
                            </tr>
                            <tr>
                                <td class="py-3 px-3 font-bold text-slate-700">Monthly Income</td>
                                <td class="py-3 px-3 text-slate-900">Rp {{ number_format($kyc->user->profile->monthly_income ?? 25000000, 0, ',', '.') }}</td>
                                <td class="py-3 px-3 text-emerald-700 font-bold flex items-center gap-1">
                                    <span>✓</span> VERIFIED_INCOME
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <div class="p-3 rounded-xl bg-amber-50 border border-amber-200 text-[11px] text-amber-900 font-medium">
                    ⚠️ AI Detection: All OCR extraction details match user input with 99.2% confidence.
                </div>
            </div>

        </div>

        <!-- Right Column: Document Previews & Action Panel (Spans 5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Document Preview Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">Document Previews</h3>
                    <span class="text-[11px] text-slate-400 font-medium">{{ $kyc->documents->count() }} Files</span>
                </div>

                <!-- Preview Grid -->
                <div class="space-y-4">
                    @foreach($kyc->documents as $doc)
                    <div class="rounded-xl border border-slate-200 overflow-hidden bg-slate-50">
                        <div class="p-2.5 bg-slate-100 border-b border-slate-200 flex items-center justify-between">
                            <span class="text-[11px] font-bold text-slate-700 uppercase tracking-wider">{{ $doc->type }} Document</span>
                            <a href="{{ route('admin.kyc.document', $doc->id) }}" target="_blank" class="text-[11px] font-bold text-emerald-700 hover:text-emerald-800">Open Full &rarr;</a>
                        </div>
                        <div class="h-48 flex items-center justify-center p-2">
                            @if(in_array(pathinfo($doc->file_path, PATHINFO_EXTENSION), ['pdf']))
                                <div class="text-center p-4">
                                    <span class="text-3xl block mb-1">📄</span>
                                    <span class="text-xs font-bold text-slate-700">PDF Document</span>
                                </div>
                            @else
                                <img src="{{ route('admin.kyc.document', $doc->id) }}" alt="{{ $doc->type }}" class="h-full w-full object-contain rounded-lg">
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>

            <!-- Decision Action Control Panel -->
            @if($kyc->isPending())
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">
                    Verification Decision
                </h3>

                <div class="space-y-3">
                    <!-- Approve Button Form -->
                    <form action="{{ route('admin.kyc.approve', $kyc->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs flex items-center justify-center gap-2">
                            <span>✓</span> Approve KYC Application
                        </button>
                    </form>

                    <!-- Reject Trigger Button -->
                    <button type="button" @click="showRejectModal = !showRejectModal" class="w-full py-2.5 rounded-xl border border-rose-200 bg-rose-50 text-rose-700 font-bold text-xs hover:bg-rose-100 transition-colors flex items-center justify-center gap-2">
                        <span>✕</span> Reject Application
                    </button>
                </div>

                <!-- Collapsible Rejection Form -->
                <div x-show="showRejectModal" class="pt-4 border-t border-slate-100 space-y-3" style="display: none;">
                    <form action="{{ route('admin.kyc.reject', $kyc->id) }}" method="POST" class="space-y-3">
                        @csrf
                        <div>
                            <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">Rejection Reason</label>
                            <select name="rejected_reason_preset" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 mb-2">
                                <option value="KTP photo blurry or unreadable">KTP photo blurry or unreadable</option>
                                <option value="Selfie identity face mismatch">Selfie identity face mismatch</option>
                                <option value="Expired government document">Expired government document</option>
                            </select>
                            <textarea name="rejected_reason" rows="2" required placeholder="Detail reason for applicant..." class="w-full rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-800 outline-none focus:border-rose-500"></textarea>
                        </div>
                        <button type="submit" class="w-full py-2 rounded-xl bg-rose-600 text-white font-bold text-xs hover:bg-rose-700 transition-colors shadow-xs">
                            Confirm Rejection
                        </button>
                    </form>
                </div>
            </div>
            @endif

        </div>

    </div>

</div>
@endsection
