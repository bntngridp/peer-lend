@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- Password Recovery Card -->
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Reset Password</h2>
            <p class="text-xs font-medium text-slate-500 mt-1 leading-relaxed">
                Masukkan alamat email akun LendFlow Anda. Kami akan mengirimkan tautan aman untuk membuat password baru secara langsung.
            </p>
        </div>

        @if (session('success'))
            <div class="mb-5 p-3.5 rounded-xl bg-emerald-50 border border-emerald-200 text-xs font-semibold text-emerald-800 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-emerald-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <div>
                    <div>{{ session('success') }}</div>
                    <div id="cooldown-banner" class="mt-1 text-[11px] font-bold text-emerald-700">
                        Dapat meminta tautan ulang dalam: <span id="timer-count">60</span>s
                    </div>
                </div>
            </div>
        @endif

        @if (session('warning'))
            <div class="mb-5 p-3.5 rounded-xl bg-amber-50 border border-amber-200 text-xs font-semibold text-amber-800 flex items-start gap-2.5">
                <svg class="w-4 h-4 text-amber-600 shrink-0 mt-0.5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                </svg>
                <div>
                    <div>{{ session('warning') }}</div>
                    <div id="cooldown-banner" class="mt-1 text-[11px] font-bold text-amber-700">
                        Dapat meminta tautan ulang dalam: <span id="timer-count">60</span>s
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                       placeholder="admin@institution.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit" id="btn_submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs cursor-pointer">
                Send Recovery Link &rarr;
            </button>

            <!-- Back to Login -->
            <div class="text-center pt-2">
                <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-700 inline-flex items-center gap-1 hover:underline">
                    &larr; Return to Login
                </a>
            </div>
        </form>

    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let secondsLeft = {{ session('cooldown_seconds', 0) }};
    
    // Check localStorage for persisted cooldown
    const storedExpire = localStorage.getItem('lendflow_reset_cooldown');
    if (storedExpire) {
        const now = Math.floor(Date.now() / 1000);
        const remaining = parseInt(storedExpire) - now;
        if (remaining > 0 && remaining > secondsLeft) {
            secondsLeft = remaining;
        }
    }

    if (secondsLeft > 0) {
        const btnSubmit = document.getElementById('btn_submit');
        const timerCount = document.getElementById('timer-count');
        const expireTime = Math.floor(Date.now() / 1000) + secondsLeft;
        localStorage.setItem('lendflow_reset_cooldown', expireTime);

        function updateTimer() {
            const now = Math.floor(Date.now() / 1000);
            const left = expireTime - now;

            if (left <= 0) {
                localStorage.removeItem('lendflow_reset_cooldown');
                if (btnSubmit) {
                    btnSubmit.disabled = false;
                    btnSubmit.className = 'w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs cursor-pointer';
                    btnSubmit.innerHTML = 'Send Recovery Link &rarr;';
                }
                const banners = document.querySelectorAll('#cooldown-banner');
                banners.forEach(b => b.style.display = 'none');
                return;
            }

            if (timerCount) timerCount.textContent = left;
            if (btnSubmit) {
                btnSubmit.disabled = true;
                btnSubmit.className = 'w-full py-3 rounded-xl bg-slate-200 text-slate-400 font-bold text-xs cursor-not-allowed transition-all duration-200 shadow-none border-none';
                btnSubmit.innerHTML = 'Kirim Ulang Link (' + left + 's)';
            }

            setTimeout(updateTimer, 1000);
        }

        updateTimer();
    }
});
</script>
@endsection
