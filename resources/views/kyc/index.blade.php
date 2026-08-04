@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto">
    
    <!-- Top Header Bar -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">Identity Verification</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">To ensure a secure trading environment, we require all institutional users to complete our Know Your Customer (KYC) process.</p>
    </div>

    @if(!$kyc)
        <!-- NO KYC SUBMITTED: Multi-step Form -->
        <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('kyc.partials.form')
        </form>
    @elseif($kyc->isRejected())
        <!-- REJECTED STATE Banner + Form -->
        <div class="rounded-2xl border border-rose-200 bg-rose-50/50 p-6 text-rose-900 shadow-xs mb-6">
            <div class="flex items-start gap-3">
                <span class="text-xl"></span>
                <div>
                    <h4 class="text-sm font-bold">Verification Request Rejected</h4>
                    <p class="text-xs mt-1 text-rose-800 font-medium">Reason: <strong>{{ $kyc->rejected_reason }}</strong></p>
                    <p class="text-[11px] mt-2 text-rose-700">Please re-examine your documents and resubmit using the form below.</p>
                </div>
            </div>
        </div>

        <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data">
            @csrf
            @include('kyc.partials.form')
        </form>
    @elseif($kyc->isPending())
        <!-- PENDING STATE matching Verification Status Stitch Mockup -->
        <div class="space-y-6">
            
            <!-- Review in Progress Card Banner -->
            <div class="rounded-2xl border border-amber-200 bg-amber-50/50 p-6 shadow-xs">
                <div class="flex items-start gap-3">
                    <span class="text-xl"></span>
                    <div>
                        <span class="text-xs font-bold text-amber-900 uppercase tracking-wider block">pending_actions Review in Progress</span>
                        <p class="text-xs text-amber-800 mt-1 leading-relaxed font-medium">
                            Your documents have been securely transmitted to our compliance team. The verification review typically takes <strong>24-48 hours</strong>. We will notify you via email once the process is complete.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Grid Layout for Timeline & Documents -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                
                <!-- Status Timeline Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">STATUS TIMELINE</h3>
                    
                    <div class="space-y-4 text-xs">
                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-emerald-100 text-emerald-800 font-bold flex items-center justify-center text-[10px]">1</span>
                            <div>
                                <span class="font-bold text-slate-900 block">Application Submitted</span>
                                <span class="text-[11px] text-slate-400 font-medium">{{ $kyc->created_at->format('M d, Y - H:i A') }}</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3">
                            <span class="h-6 w-6 rounded-full bg-amber-500 text-white font-bold flex items-center justify-center text-[10px]">2</span>
                            <div>
                                <span class="font-bold text-amber-900 block">Under Review</span>
                                <span class="text-[11px] text-slate-500 font-medium">Compliance team analyzing data</span>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 opacity-50">
                            <span class="h-6 w-6 rounded-full bg-slate-100 text-slate-500 font-bold flex items-center justify-center text-[10px]">3</span>
                            <div>
                                <span class="font-bold text-slate-700 block">Verified</span>
                                <span class="text-[11px] text-slate-400 font-medium">Pending approval</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Uploaded Documents Card -->
                <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider">UPLOADED DOCUMENTS</h3>

                    <div class="space-y-3">
                        @foreach($kyc->documents as $doc)
                        <div class="p-3.5 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <span class="text-base"></span>
                                <div>
                                    <span class="text-xs font-bold text-slate-900 block capitalize">{{ str_replace('_', ' ', $doc->type) }} Document</span>
                                    <span class="text-[10px] text-emerald-700 font-bold block mt-0.5">cloud_done Uploaded successfully</span>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <p class="text-[11px] text-slate-400 text-center pt-2">
                        Need to update your documents? <a href="#" class="font-bold text-emerald-700">Contact Support</a>
                    </p>
                </div>

            </div>

        </div>
    @elseif($kyc->isApproved())
        <!-- APPROVED STATE -->
        <div class="rounded-2xl border border-emerald-200 dark:border-emerald-800 bg-white dark:bg-slate-900 p-8 text-center shadow-xs space-y-4">
            <div class="mx-auto h-16 w-16 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-2xl shadow-xs">
                
            </div>
            <h3 class="text-xl font-extrabold text-slate-900 tracking-tight">Account Verified</h3>
            <p class="text-xs text-slate-500 max-w-md mx-auto leading-relaxed font-medium">
                Congratulations! Your institutional identity verification has been completed. You now have full access to deposit, request loans, or invest capital on the platform.
            </p>
            <div class="pt-2">
                <a href="{{ route('dashboard') }}" class="inline-flex items-center gap-1.5 py-2.5 px-6 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                    Go to Dashboard &rarr;
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
