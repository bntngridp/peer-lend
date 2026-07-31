@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- LendFlow Reset Password Card -->
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Set new password</h2>
            <p class="text-xs font-medium text-slate-400 mt-0.5">Please choose a strong password to secure your account.</p>
        </div>

        <form class="space-y-5" action="{{ route('password.update') }}" method="POST">
            @csrf

            <!-- Password Reset Token -->
            <input type="hidden" name="token" value="{{ $token }}">

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email', $email) }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- New Password Input -->
            <div>
                <label for="password" class="block text-xs font-bold text-slate-700 mb-1.5">New Password</label>
                <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('password') border-rose-300 @enderror">
                    <input id="password" name="password" type="password" required autocomplete="new-password" oninput="checkPasswordRequirements(this.value)"
                           class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none p-0 focus:ring-0"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePass('password', this)" class="ml-2 text-slate-400 hover:text-emerald-700 focus:outline-none transition-colors shrink-0 p-1 rounded-lg hover:bg-slate-100" title="Toggle password visibility">
                        <!-- Eye Open -->
                        <svg class="eye-open w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Off (Clean Modern Lucide Eye-Off) -->
                        <svg class="eye-closed w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Confirm Password Input -->
            <div>
                <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1.5">Confirm New Password</label>
                <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200">
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password"
                           class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none p-0 focus:ring-0"
                           placeholder="••••••••">
                    <button type="button" onclick="togglePass('password_confirmation', this)" class="ml-2 text-slate-400 hover:text-emerald-700 focus:outline-none transition-colors shrink-0 p-1 rounded-lg hover:bg-slate-100" title="Toggle confirm password visibility">
                        <!-- Eye Open -->
                        <svg class="eye-open w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Off (Clean Modern Lucide Eye-Off) -->
                        <svg class="eye-closed w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.94 17.94A10.07 10.07 0 0112 19c-7 0-11-8-11-8a18.45 18.45 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24M1 1l22 22" />
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Password Requirement Real-Time Checklist -->
            <div class="p-3 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] space-y-1.5 font-medium text-slate-500">
                <div id="req-length" class="flex items-center gap-1.5 transition-colors">
                    <span class="bullet w-3.5 h-3.5 rounded-full border border-slate-300 bg-white text-slate-400 flex items-center justify-center text-[9px] shrink-0 font-bold">✕</span>
                    <span>Minimal 8 karakter</span>
                </div>
                <div id="req-case" class="flex items-center gap-1.5 transition-colors">
                    <span class="bullet w-3.5 h-3.5 rounded-full border border-slate-300 bg-white text-slate-400 flex items-center justify-center text-[9px] shrink-0 font-bold">✕</span>
                    <span>Huruf besar (A-Z) &amp; huruf kecil (a-z)</span>
                </div>
                <div id="req-number" class="flex items-center gap-1.5 transition-colors">
                    <span class="bullet w-3.5 h-3.5 rounded-full border border-slate-300 bg-white text-slate-400 flex items-center justify-center text-[9px] shrink-0 font-bold">✕</span>
                    <span>Minimal 1 angka (0-9)</span>
                </div>
                <div id="req-symbol" class="flex items-center gap-1.5 transition-colors">
                    <span class="bullet w-3.5 h-3.5 rounded-full border border-slate-300 bg-white text-slate-400 flex items-center justify-center text-[9px] shrink-0 font-bold">✕</span>
                    <span>Karakter khusus (@, $, !, %, *, #, ?, &amp;)</span>
                </div>
            </div>

            @error('password')
                <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
            @enderror

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs">
                Update Password &rarr;
            </button>
        </form>
    </div>
</div>

<script>
function togglePass(inputId, btn) {
    const input = document.getElementById(inputId);
    if (!input) return;
    const isPassword = input.type === 'password';
    input.type = isPassword ? 'text' : 'password';
    
    const eyeOpen = btn.querySelector('.eye-open');
    const eyeClosed = btn.querySelector('.eye-closed');
    if (eyeOpen && eyeClosed) {
        if (isPassword) {
            eyeOpen.classList.add('hidden');
            eyeClosed.classList.remove('hidden');
        } else {
            eyeOpen.classList.remove('hidden');
            eyeClosed.classList.add('hidden');
        }
    }
}

function checkPasswordRequirements(val) {
    const reqLength = document.getElementById('req-length');
    const reqCase = document.getElementById('req-case');
    const reqNumber = document.getElementById('req-number');
    const reqSymbol = document.getElementById('req-symbol');

    if (!reqLength) return;

    // 1. Min 8 chars
    updateReq(reqLength, val.length >= 8);
    // 2. Upper & lower case
    updateReq(reqCase, /[a-z]/.test(val) && /[A-Z]/.test(val));
    // 3. At least 1 number
    updateReq(reqNumber, /[0-9]/.test(val));
    // 4. At least 1 special char
    updateReq(reqSymbol, /[@$!%*#?&]/.test(val));
}

function updateReq(el, isValid) {
    const bullet = el.querySelector('.bullet');
    if (isValid) {
        el.className = 'flex items-center gap-1.5 text-emerald-600 font-bold transition-colors';
        if (bullet) {
            bullet.className = 'bullet w-3.5 h-3.5 rounded-full bg-emerald-600 text-white flex items-center justify-center text-[9px] shrink-0 font-bold';
            bullet.textContent = '✓';
        }
    } else {
        el.className = 'flex items-center gap-1.5 text-slate-500 font-medium transition-colors';
        if (bullet) {
            bullet.className = 'bullet w-3.5 h-3.5 rounded-full border border-slate-300 bg-white text-slate-400 flex items-center justify-center text-[9px] shrink-0 font-bold';
            bullet.textContent = '✕';
        }
    }
}
</script>
@endsection
