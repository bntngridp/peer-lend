@extends('layouts.app')

@section('content')
<div class="mx-auto max-w-3xl px-4 py-10 sm:px-6 lg:px-8">
    
    <!-- Header -->
    <div class="text-center mb-8">
        <h2 class="text-3xl font-extrabold tracking-tight text-gray-900">KYC Identity Verification</h2>
        <p class="mt-2 text-sm text-gray-500">Fintech regulations require a one-time identity verification before you can apply for loans or fund investments.</p>
    </div>

    @if(!$kyc)
        <!-- NO KYC: Show Form -->
        <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-6 sm:p-8">
            <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @include('kyc.partials.form')
            </form>
        </div>
    @elseif($kyc->isRejected())
        <!-- REJECTED STATE: Show banner + Form to retry -->
        <div class="mb-6 rounded-2xl border border-red-200 bg-red-50/50 p-6 text-red-900">
            <div class="flex gap-3">
                <svg class="h-6 w-6 text-red-500 mt-0.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                </svg>
                <div>
                    <h4 class="text-base font-bold">Verification Request Rejected</h4>
                    <p class="text-sm mt-1 opacity-90">Reason: <strong>{{ $kyc->rejected_reason }}</strong></p>
                    <p class="text-xs mt-2 opacity-75">Please re-examine your documents and resubmit using the form below.</p>
                </div>
            </div>
        </div>

        <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-6 sm:p-8">
            <form action="{{ route('kyc.submit') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                @include('kyc.partials.form')
            </form>
        </div>
    @elseif($kyc->isPending())
        <!-- PENDING STATE -->
        <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-amber-100 text-amber-600 border border-amber-200 mb-4">
                <svg class="h-8 w-8 animate-pulse" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Verification in Progress</h3>
            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Your identity documents have been submitted successfully. Our compliance officers are reviewing your application. This normally takes less than 24 hours.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('dashboard') }}" class="rounded-xl border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50 transition-colors">
                    Go to Dashboard
                </a>
            </div>
        </div>
    @elseif($kyc->isApproved())
        <!-- APPROVED STATE -->
        <div class="bg-white shadow-xl shadow-gray-200/40 rounded-2xl border border-gray-100 p-8 text-center">
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-emerald-100 text-emerald-600 border border-emerald-200 mb-4">
                <svg class="h-8 w-8" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
            </div>
            <h3 class="text-xl font-bold text-gray-900">Account Verified</h3>
            <p class="mt-2 text-sm text-gray-500 max-w-md mx-auto">Congratulations! Your identity has been verified. You now have full access to deposit, request loans, or invest capital on the platform.</p>
            <div class="mt-6 flex justify-center gap-4">
                <a href="{{ route('dashboard') }}" class="rounded-xl bg-indigo-600 px-4 py-2.5 text-sm font-semibold text-white shadow-md shadow-indigo-600/10 hover:bg-indigo-700 transition-colors">
                    Go to Dashboard
                </a>
            </div>
        </div>
    @endif

</div>
@endsection
