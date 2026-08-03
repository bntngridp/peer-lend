@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto space-y-6 py-4" x-data="{ setupMethod: 'qr', copied: false }">
    
    <!-- Top Navigation Back Link -->
    <div>
        <a href="{{ route('profile.edit', ['tab' => 'security']) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-slate-500 hover:text-emerald-700 transition-colors">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5L3 12m0 0l7.5-7.5M3 12h18" />
            </svg>
            {{ __('Back to Security Settings') }}
        </a>
    </div>

    <!-- Header Title -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Two-Factor Authentication (2FA)') }}</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Enhance your LendFlow account security by enabling Authenticator App (Google Authenticator, Authy, or Microsoft Authenticator).') }}</p>
    </div>

    <!-- Main Card Container -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-8">
        
        <!-- Step 1: Choose Setup Method & Get Credentials -->
        <div class="space-y-4">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                <div class="flex items-center gap-2">
                    <span class="h-6 w-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0">{{ __n(1) }}</span>
                    <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Step 1: Connect Authenticator App') }}</h3>
                </div>
                
                <!-- Toggle Setup Method Tabs -->
                <div class="flex bg-slate-100 p-1 rounded-xl text-xs font-bold text-slate-600 self-start sm:self-auto">
                    <button type="button" @click="setupMethod = 'qr'" 
                            :class="setupMethod === 'qr' ? 'bg-white text-emerald-800 shadow-xs rounded-lg px-3.5 py-1.5' : 'px-3.5 py-1.5 hover:text-slate-900 transition-colors'">
                        {{ __('Scan QR Code') }}
                    </button>
                    <button type="button" @click="setupMethod = 'key'" 
                            :class="setupMethod === 'key' ? 'bg-white text-emerald-800 shadow-xs rounded-lg px-3.5 py-1.5' : 'px-3.5 py-1.5 hover:text-slate-900 transition-colors'">
                        {{ __('Manual Key Code') }}
                    </button>
                </div>
            </div>

            <!-- Method 1: QR Code Scan -->
            <div x-show="setupMethod === 'qr'" class="flex flex-col sm:flex-row items-center gap-6 p-6 rounded-2xl bg-slate-50 border border-slate-200">
                <div class="bg-white p-3 rounded-2xl border border-slate-200 shadow-xs shrink-0 flex items-center justify-center">
                    <img src="{{ $qrUrl }}" alt="2FA QR Code" class="h-44 w-44 rounded-xl object-contain bg-white">
                </div>
                <div class="space-y-3 text-center sm:text-left">
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-emerald-100 text-emerald-800">
                        {{ __('Primary Method (Camera Scan)') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900">{{ __('Scan QR Code Using Your Phone') }}</h4>
                    <p class="text-xs text-slate-500 leading-relaxed font-medium">
                        {{ __('Open your :app app, :authy, or :ms on your phone, then select Add Account / Scan QR Code.', ['app' => 'Google Authenticator', 'authy' => 'Authy', 'ms' => 'Microsoft Authenticator']) }}
                    </p>
                </div>
            </div>

            <!-- Method 2: Manual Secret Key Code -->
            <div x-show="setupMethod === 'key'" class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4" style="display: none;">
                <div>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-md text-[11px] font-bold bg-amber-100 text-amber-800">
                        {{ __('Alternative Method (Manual Key)') }}
                    </span>
                    <h4 class="text-xs font-bold text-slate-900 mt-1">{{ __('Enter Secret Key Manually') }}</h4>
                </div>

                <p class="text-xs text-slate-500 leading-relaxed font-medium">
                    {{ __('If your phone camera cannot scan the QR Code, open the Authenticator app, select Enter Setup Key Manually, then enter the secret key below:') }}
                </p>

                <!-- Copyable Secret Key Box -->
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3">
                    <div class="flex-1 font-mono text-sm font-black bg-white border border-slate-200 px-4 py-3 rounded-xl text-emerald-800 tracking-wider shadow-xs select-all text-center sm:text-left">
                        {{ $secret }}
                    </div>
                    <button type="button" 
                            @click="
                                navigator.clipboard.writeText('{{ $secret }}');
                                copied = true;
                                setTimeout(() => copied = false, 2500);
                            "
                            class="px-4 py-3 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition-colors shadow-xs shrink-0 flex items-center justify-center gap-1.5 cursor-pointer">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 17.25v3.375c0 .621-.504 1.125-1.125 1.125h-9.75a1.125 1.125 0 01-1.125-1.125V7.875c0-.621.504-1.125 1.125-1.125H6.75a9.06 9.06 0 011.5.124m7.5 10.376h3.375c.621 0 1.125-.504 1.125-1.125V11.25c0-4.46-3.243-8.161-7.5-8.876a9.06 9.06 0 00-1.5-.124H9.375c-.621 0-1.125.504-1.125 1.125v3.5m7.5 10.375H9.375a1.125 1.125 0 01-1.125-1.125v-9.25m12 6.375v-6.375a1.125 1.125 0 00-1.125-1.125H15.75"/></svg>
                        <span x-text="copied ? '{{ __('Copied!') }}' : '{{ __('Copy Key') }}'">{{ __('Copy Key') }}</span>
                    </button>
                </div>
            </div>
        </div>

        <!-- Step 2: Verification Code Input -->
        <div class="pt-4 border-t border-slate-100">
            <div class="flex items-center gap-2 mb-4">
                <span class="h-6 w-6 rounded-full bg-emerald-700 text-white font-black text-xs flex items-center justify-center shrink-0">{{ __n(2) }}</span>
                <h3 class="text-sm font-bold text-slate-900 uppercase tracking-wider">{{ __('Step 2: Verify 6-Digit Code') }}</h3>
            </div>

            <form action="{{ route('2fa.enable') }}" method="POST" class="space-y-5 max-w-md mx-auto text-center">
                @csrf

                <div>
                    <label for="code" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('OTP Code from Authenticator App') }}</label>
                    <input type="text" name="code" id="code" required maxlength="6" autofocus
                           class="w-full text-center tracking-[0.6em] font-mono text-2xl font-bold rounded-2xl border {{ $errors->has('code') ? 'border-rose-500 bg-rose-50/50 text-rose-900' : 'border-slate-200 text-slate-900' }} px-4 py-3.5 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs"
                           placeholder="000000">
                    
                    @error('code')
                        <p class="mt-2 text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <button type="submit" id="btn_submit_2fa"
                        class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                    {{ __('Enable 2FA Now') }} &rarr;
                </button>
            </form>
        </div>

    </div>

</div>
@endsection
