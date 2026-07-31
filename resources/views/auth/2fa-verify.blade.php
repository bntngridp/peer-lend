@extends('layouts.app')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- 2FA Card Container -->
        <div class="bg-white px-8 py-10 shadow-xs rounded-2xl border border-slate-200 text-center">
            
            <!-- Shield Icon Badge -->
            <div class="mx-auto h-12 w-12 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-xl mb-4 shadow-xs">
                🛡️
            </div>

            <!-- Title & Subtitle -->
            <h2 class="text-base font-bold text-slate-900">Two-Factor Authentication</h2>
            <p class="text-xs text-slate-500 mt-1 mb-6 leading-relaxed font-medium">
                We've sent a 6-digit verification code to your registered device. Enter it below to proceed.
            </p>

            <!-- 2FA Verification Form -->
            <form action="{{ route('2fa.verify.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- OTP Input -->
                <div>
                    <label for="code" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">6-Digit Security Code</label>
                    <input type="text" name="code" id="code" required maxlength="6" autofocus
                           class="w-full text-center tracking-[0.6em] font-mono text-xl font-bold rounded-xl border border-slate-200 px-4 py-3 text-slate-900 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 @error('code') border-rose-300 text-rose-900 @enderror"
                           placeholder="••••••">
                    
                    @error('code')
                        <p class="mt-2 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Trust Device Checkbox -->
                <div class="flex items-center justify-center py-1 select-none">
                    <label class="inline-flex items-center cursor-pointer">
                        <input type="checkbox" name="remember_device" value="1" class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600 h-4 w-4">
                        <span class="ml-2 text-xs font-semibold text-slate-600">Trust this device for 30 days</span>
                    </label>
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Verify Identity &rarr;
                </button>

                <!-- Resend / Recovery Links -->
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 pt-2 border-t border-slate-100">
                    <button type="button" class="hover:text-emerald-700 flex items-center gap-1">
                        <span>🔄</span> Resend Code
                    </button>
                    <a href="{{ route('login') }}" class="hover:text-emerald-700">
                        Use Recovery Code
                    </a>
                </div>
            </form>

            <!-- Security Footnote -->
            <div class="mt-8 pt-4 border-t border-slate-100 text-[11px] text-slate-400 font-medium">
                🔒 Secured by LendFlow Institutional Grade Encryption
            </div>

        </div>
    </div>
</div>
@endsection
