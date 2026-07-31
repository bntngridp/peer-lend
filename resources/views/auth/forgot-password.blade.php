@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- Password Recovery Card -->
    <div class="bg-white/95 backdrop-blur-md px-8 py-10 shadow-xl shadow-emerald-900/5 rounded-2xl border border-slate-200/80 relative">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-6">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Reset Password</h2>
            <p class="text-xs font-medium text-slate-500 mt-1 leading-relaxed">
                Enter the email address associated with your LendFlow account, and we'll send you a secure link to reset your password.
            </p>
        </div>

        <!-- Form -->
        <form class="space-y-5" action="{{ route('password.email') }}" method="POST">
            @csrf

            <!-- Email Input -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Email Address</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                       placeholder="admin@institution.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-sm">
                Send Recovery Link
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
@endsection
