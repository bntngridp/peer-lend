@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- LendFlow OTP Card Container -->
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-2xl bg-emerald-50 text-emerald-700 mb-3 border border-emerald-100">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
            </div>

            <h2 class="text-lg font-bold text-slate-900">Verifikasi Nomor HP</h2>
            <p class="text-xs font-medium text-slate-500 mt-1 max-w-xs mx-auto">
                Masukkan 6 digit kode OTP yang telah kami kirimkan ke nomor 
                <span class="font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded-md inline-block mt-1">{{ $phone }}</span>
            </p>
        </div>

        <!-- Simulated Dev Banner (For easy testing) -->
        @if(config('app.env') === 'local' || true)
        <div class="mb-6 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-amber-800 text-xs font-medium flex items-center justify-between shadow-xs">
            <div class="flex items-center gap-2">
                <svg class="w-4 h-4 text-amber-700 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M15.75 5.25a3 3 0 013 3m3 0a6 6 0 01-7.029 5.912c-.563-.097-1.159.026-1.563.43L10.5 17.25H8.25v2.25H6v2.25H2.25v-2.818c0-.597.237-1.17.659-1.591l6.499-6.499c.404-.404.527-1 .43-1.563A6 6 0 1121.75 8.25z"/></svg>
                <div>
                    <span class="font-bold block">Kode OTP Simulator (Testing):</span>
                    <span class="font-black text-amber-900 tracking-widest text-sm bg-white px-2.5 py-0.5 rounded-lg border border-amber-300 inline-block mt-0.5">{{ $otpCode }}</span>
                </div>
            </div>
            <button type="button" onclick="autoFillOtp('{{ $otpCode }}')" class="px-2.5 py-1 text-[11px] font-bold bg-amber-600 text-white rounded-lg hover:bg-amber-700 transition-colors shadow-2xs">
                Isi Otomatis
            </button>
        </div>
        @endif

        <!-- Alert Notification -->
        @if (session('info'))
            <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-start gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('info') }}</span>
            </div>
        @endif

        @if (session('success'))
            <div class="mb-5 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-medium text-emerald-800 flex items-start gap-2">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('success') }}</span>
            </div>
        @endif

        @if (session('error'))
            <div class="mb-5 p-3 rounded-xl bg-rose-50 border border-rose-200 text-xs font-medium text-rose-800 flex items-start gap-2">
                <svg class="w-4 h-4 text-rose-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <span>{{ session('error') }}</span>
            </div>
        @endif

        <!-- OTP Input Form -->
        <form action="{{ route('register.otp.verify') }}" method="POST" id="otp-form" class="space-y-6">
            @csrf

            <!-- Hidden aggregated OTP field -->
            <input type="hidden" name="otp" id="final-otp-input">

            <!-- 6 Boxes Digit Grid -->
            <div>
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider text-center mb-3">6-Digit OTP Code</label>
                <div class="flex justify-between gap-2 sm:gap-2.5" id="otp-inputs-container">
                    @for ($i = 0; $i < 6; $i++)
                        <input type="text" maxlength="1" pattern="[0-9]" inputmode="numeric"
                               class="otp-digit w-11 h-13 sm:w-12 sm:h-14 text-center text-xl font-black text-slate-900 bg-slate-50 border-2 border-slate-200 rounded-xl focus:border-emerald-600 focus:bg-white focus:ring-4 focus:ring-emerald-600/15 outline-none transition-all duration-150"
                               data-index="{{ $i }}" autocomplete="off">
                    @endfor
                </div>
                @error('otp')
                    <p class="mt-2 text-xs text-rose-600 font-semibold text-center">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn-verify-otp"
                    class="w-full py-3.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs cursor-pointer flex items-center justify-center gap-2">
                <span>{{ __('Verify & Create Account') }} &rarr;</span>
            </button>
        </form>

        <!-- Resend OTP Footer -->
        <div class="mt-6 text-center border-t border-slate-100 pt-5">
            <p class="text-xs text-slate-500 font-medium mb-3">
                {{ __('Didn\'t receive the code?') }}
                <span id="countdown-wrapper" class="text-slate-400 font-bold">{{ __('Resend in') }} <span id="countdown-timer">{{ __n(60) }}</span>{{ __('s') }}</span>
            </p>

            <form action="{{ route('register.otp.resend') }}" method="POST" id="resend-form" class="inline">
                @csrf
                <button type="submit" id="btn-resend" disabled
                        class="px-4 py-2 rounded-xl bg-slate-100 text-slate-400 text-xs font-bold transition-all duration-200 cursor-not-allowed hover:bg-emerald-50 hover:text-emerald-700 disabled:opacity-50">
                    {{ __('Resend OTP Code') }}
                </button>
            </form>
        </div>

        <!-- Back to Register -->
        <div class="mt-4 text-center">
            <a href="{{ route('register') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 transition-colors inline-flex items-center gap-1">
                &larr; {{ __('Change Phone Number') }}
            </a>
        </div>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const inputs = document.querySelectorAll('.otp-digit');
    const finalInput = document.getElementById('final-otp-input');
    const form = document.getElementById('otp-form');

    // Auto focus first input box
    if (inputs.length > 0) {
        inputs[0].focus();
    }

    inputs.forEach((input, index) => {
        // Input event for typing digit
        input.addEventListener('input', function(e) {
            const val = this.value;
            
            // Allow only digits
            if (!/^[0-9]$/.test(val)) {
                this.value = '';
                return;
            }

            // Move focus to next input
            if (val && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }

            updateFinalOtp();
        });

        // Keydown event for handling Backspace & Arrow keys
        input.addEventListener('keydown', function(e) {
            if (e.key === 'Backspace') {
                if (!this.value && index > 0) {
                    inputs[index - 1].focus();
                    inputs[index - 1].value = '';
                } else {
                    this.value = '';
                }
                updateFinalOtp();
            } else if (e.key === 'ArrowLeft' && index > 0) {
                inputs[index - 1].focus();
            } else if (e.key === 'ArrowRight' && index < inputs.length - 1) {
                inputs[index + 1].focus();
            }
        });

        // Handle Paste Event (e.g. user pastes 6 digits)
        input.addEventListener('paste', function(e) {
            e.preventDefault();
            const pasteData = (e.clipboardData || window.clipboardData).getData('text').trim();
            if (/^\d{6}$/.test(pasteData)) {
                autoFillOtp(pasteData);
            }
        });
    });

    function updateFinalOtp() {
        let code = '';
        inputs.forEach(i => code += i.value);
        finalInput.value = code;
    }

    window.autoFillOtp = function(code) {
        if (!code || code.length !== 6) return;
        const digits = code.split('');
        inputs.forEach((input, idx) => {
            input.value = digits[idx] || '';
        });
        updateFinalOtp();
        inputs[5].focus();
    };

    form.addEventListener('submit', function(e) {
        updateFinalOtp();
        if (finalInput.value.length !== 6) {
            e.preventDefault();
            alert('Silakan masukkan 6 digit kode OTP secara lengkap.');
            return false;
        }
    });

    // ⏱️ Countdown Timer for Resend Button
    let secondsLeft = 60;
    const timerSpan = document.getElementById('countdown-timer');
    const countdownWrapper = document.getElementById('countdown-wrapper');
    const btnResend = document.getElementById('btn-resend');

    const interval = setInterval(function() {
        secondsLeft--;
        if (timerSpan) timerSpan.textContent = secondsLeft;

        if (secondsLeft <= 0) {
            clearInterval(interval);
            if (countdownWrapper) countdownWrapper.classList.add('hidden');
            if (btnResend) {
                btnResend.disabled = false;
                btnResend.classList.remove('bg-slate-100', 'text-slate-400', 'cursor-not-allowed');
                btnResend.classList.add('bg-emerald-600', 'text-white', 'hover:bg-emerald-700', 'cursor-pointer', 'shadow-2xs');
            }
        }
    }, 1000);
});
</script>
@endsection
