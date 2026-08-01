@extends('layouts.auth')

@section('content')
<div class="min-h-[80vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- 2FA Card Container -->
        <div class="bg-white px-8 py-10 shadow-xs rounded-2xl border border-slate-200 text-center space-y-6">
            
            <!-- Shield Icon Badge -->
            <div class="mx-auto h-14 w-14 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-700 flex items-center justify-center text-2xl shadow-xs">
                <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                </svg>
            </div>

            <!-- Title & Subtitle -->
            <div>
                <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Two-Factor Verification</h2>
                <p class="text-xs text-slate-500 mt-1.5 leading-relaxed font-medium">
                    Masukkan 6 digit kode dari aplikasi <strong>Google Authenticator</strong> / <strong>Authy</strong> Anda untuk melanjutkan masuk ke LendFlow.
                </p>
            </div>

            <!-- 2FA Verification Form -->
            <form action="{{ route('2fa.verify.post') }}" method="POST" class="space-y-5">
                @csrf

                <!-- OTP Input -->
                <div>
                    <label for="code" class="block text-[11px] font-bold uppercase tracking-wider text-slate-400 mb-2">Kode OTP 6 Digit</label>
                    <input type="text" name="code" id="code" required maxlength="6" autofocus
                           class="w-full text-center tracking-[0.6em] font-mono text-2xl font-bold rounded-2xl border {{ $errors->has('code') ? 'border-rose-500 bg-rose-50/50 text-rose-900' : 'border-slate-200 text-slate-900' }} px-4 py-3.5 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs"
                           placeholder="000000">
                    
                    @error('code')
                        <p class="mt-2 text-xs text-rose-600 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit" id="btn_verify_2fa"
                        class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                    Verifikasi Kode 2FA &rarr;
                </button>

                <!-- Resend / Back Links -->
                <div class="flex items-center justify-between text-xs font-semibold text-slate-500 pt-2 border-t border-slate-100">
                    <a href="{{ route('login') }}" class="hover:text-emerald-700">
                        &larr; Kembali ke Login
                    </a>
                    <a href="{{ route('profile.edit', ['tab' => 'security']) }}" class="hover:text-emerald-700">
                        Bantuan Keamanan
                    </a>
                </div>
            </form>

            <!-- Security Footnote -->
            <div class="mt-6 pt-4 border-t border-slate-100 text-[11px] text-slate-400 font-medium">
                Secured by LendFlow Institutional Grade 2FA Encryption
            </div>

        </div>
    </div>
</div>
@endsection
