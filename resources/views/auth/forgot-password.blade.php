@extends('layouts.app')

@section('content')
<div class="min-h-[85vh] flex flex-col justify-center py-12 sm:px-6 lg:px-8">
    <div class="sm:mx-auto sm:w-full sm:max-w-md">
        <!-- Password Recovery Card -->
        <div class="bg-white px-8 py-10 shadow-xs rounded-2xl border border-slate-200">
            
            <!-- Brand Logo & Header -->
            <div class="text-center mb-6">
                <div class="inline-flex items-center justify-center gap-2 mb-3">
                    <span class="h-8 w-8 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-base shadow-xs">L</span>
                    <span class="text-xl font-extrabold text-slate-900 tracking-tight">LendFlow</span>
                </div>
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
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 @error('email') border-rose-300 text-rose-900 @enderror"
                           placeholder="admin@institution.com">
                    @error('email')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Submit Button -->
                <button type="submit"
                        class="w-full py-2.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Send Recovery Link
                </button>

                <!-- Back to Login -->
                <div class="text-center pt-2">
                    <a href="{{ route('login') }}" class="text-xs font-bold text-slate-600 hover:text-emerald-700 inline-flex items-center gap-1">
                        &larr; Return to Login
                    </a>
                </div>
            </form>

        </div>
    </div>
</div>
@endsection
