@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md relative">
    
    <!-- Explicit Green Glow Aura Backdrop around Card -->
    <div class="green-glow-card-aura"></div>

    <!-- LendFlow Card Container -->
    <div class="relative bg-white/95 backdrop-blur-md px-8 py-10 shadow-2xl shadow-emerald-900/10 rounded-2xl border border-emerald-100/80 z-10">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-sm group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Sign in to your institutional account</h2>
            <p class="text-xs font-medium text-slate-400 mt-0.5">Institutional Grade P2P Lending</p>
        </div>

        <!-- Login Form -->
        <form class="space-y-5" action="{{ route('login') }}" method="POST">
            @csrf

            <!-- Institutional Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1.5">Institutional Email</label>
                <div class="relative">
                    <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                           placeholder="user@institution.com">
                </div>
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password -->
            <div>
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-xs font-bold text-slate-700">Password</label>
                    <a href="{{ route('password.request') }}" class="text-xs font-bold text-emerald-700 hover:text-emerald-800">
                        Forgot Password?
                    </a>
                </div>
                <div class="relative">
                    <input id="password" name="password" type="password" autocomplete="current-password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 @error('password') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                           placeholder="••••••••">
                </div>
                @error('password')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Remember Me -->
            <div class="flex items-center justify-between py-1">
                <label class="relative inline-flex items-center cursor-pointer select-none">
                    <input id="remember" name="remember" type="checkbox" class="sr-only peer">
                    <div class="w-4 h-4 rounded border border-slate-300 peer-checked:bg-emerald-700 peer-checked:border-emerald-700 flex items-center justify-center transition-all">
                        <svg class="w-3 h-3 text-white hidden peer-checked:block" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                        </svg>
                    </div>
                    <span class="ml-2 text-xs font-semibold text-slate-600">Remember this device for 30 days</span>
                </label>
            </div>

            <!-- Primary Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-sm">
                Sign In
            </button>

            <!-- Divider -->
            <div class="relative my-6">
                <div class="absolute inset-0 flex items-center"><div class="w-full border-t border-slate-200"></div></div>
                <div class="relative flex justify-center text-[10px] uppercase"><span class="bg-white px-2 font-bold text-slate-400">OR CONTINUE WITH</span></div>
            </div>

            <!-- Google SSO Button -->
            <button type="button" class="w-full py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 focus:ring-4 focus:ring-slate-200 transition-all duration-200 flex items-center justify-center gap-2 shadow-xs">
                <svg class="w-4 h-4" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                Google Workspace
            </button>

            <!-- Footer link to Register -->
            <div class="text-center pt-2">
                <p class="text-xs text-slate-500 font-medium">
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Create Account</a>
                </p>
            </div>
        </form>

        <!-- Security Restriction Notice Box -->
        <div class="mt-8 p-3.5 rounded-xl bg-slate-50 border border-slate-200/80 text-[11px] text-slate-500 leading-relaxed font-medium">
            <div class="flex items-start gap-2">
                <span>Access is restricted to authorized personnel only. All activities are monitored and logged. By signing in, you agree to LendFlow's <a href="#" class="font-bold text-slate-700 underline">Terms of Service</a> and <a href="#" class="font-bold text-slate-700 underline">Privacy Policy</a>.</span>
            </div>
        </div>

    </div>
</div>
@endsection
