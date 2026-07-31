@extends('layouts.auth')

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <!-- LendFlow Register Card Container -->
    <div class="bg-white px-8 py-10 shadow-xl shadow-slate-900/5 rounded-2xl border border-slate-200">
        
        <!-- Brand Logo & Header -->
        <div class="text-center mb-8">
            <a href="{{ route('home') }}" class="inline-flex items-center justify-center gap-2 mb-3 group">
                <span class="h-9 w-9 rounded-xl bg-emerald-700 text-white font-black flex items-center justify-center text-lg shadow-xs group-hover:bg-emerald-800 transition-colors">L</span>
                <span class="text-2xl font-black text-slate-900 tracking-tight">LendFlow</span>
            </a>
            <h2 class="text-base font-bold text-slate-900">Create an Account</h2>
            <p class="text-xs font-medium text-slate-400 mt-0.5">Institutional Grade P2P Lending</p>
        </div>

        <!-- Registration Form -->
        <form class="space-y-4" action="{{ route('register') }}" method="POST">
            @csrf

            <!-- Native Radio Role Selection Cards -->
            <div x-data="{ selectedRole: '{{ old('role', 'borrower') }}' }">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">I want to register as:</label>
                <div class="grid grid-cols-2 gap-3 mb-4">
                    <!-- Borrower Card -->
                    <label @click="selectedRole = 'borrower'"
                           :class="selectedRole === 'borrower' ? 'border-emerald-700 bg-emerald-50/80 ring-2 ring-emerald-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative flex flex-col items-center p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-200 select-none text-center group">
                        <input type="radio" name="role" value="borrower" class="sr-only" x-model="selectedRole" {{ old('role', 'borrower') === 'borrower' ? 'checked' : '' }}>
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Borrower</span>
                            <span x-show="selectedRole === 'borrower'" class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-medium">Apply for credit</span>
                    </label>

                    <!-- Lender Card -->
                    <label @click="selectedRole = 'lender'"
                           :class="selectedRole === 'lender' ? 'border-emerald-700 bg-emerald-50/80 ring-2 ring-emerald-600/20' : 'border-slate-200 hover:border-slate-300 bg-white'"
                           class="relative flex flex-col items-center p-3.5 border-2 rounded-xl cursor-pointer transition-all duration-200 select-none text-center group">
                        <input type="radio" name="role" value="lender" class="sr-only" x-model="selectedRole" {{ old('role') === 'lender' ? 'checked' : '' }}>
                        <div class="flex items-center gap-1.5 mb-0.5">
                            <span class="text-xs font-bold text-slate-900 group-hover:text-emerald-700 transition-colors">Lender (Investor)</span>
                            <span x-show="selectedRole === 'lender'" class="w-2 h-2 rounded-full bg-emerald-600 inline-block"></span>
                        </div>
                        <span class="text-[10px] text-slate-500 font-medium">Fund capital</span>
                    </label>
                </div>
            </div>

            <!-- Full Name -->
            <div>
                <label for="full_name" class="block text-xs font-bold text-slate-700 mb-1">Full Name</label>
                <input id="full_name" name="full_name" type="text" required value="{{ old('full_name') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('full_name') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                       placeholder="John Doe">
                @error('full_name')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Business Email -->
            <div>
                <label for="email" class="block text-xs font-bold text-slate-700 mb-1">Business Email</label>
                <input id="email" name="email" type="email" autocomplete="email" required value="{{ old('email') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('email') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                       placeholder="john@company.com">
                @error('email')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Phone Number -->
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                       class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('phone') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                       placeholder="+1 (555) 000-0000">
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password and Confirmation -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <input id="password" name="password" type="password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200 @error('password') border-rose-300 text-rose-900 focus:ring-rose-500/20 @enderror"
                           placeholder="••••••••">
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required
                           class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 text-xs font-medium text-slate-800 placeholder-slate-400 outline-none focus:border-emerald-600 focus:ring-4 focus:ring-emerald-600/15 transition-all duration-200"
                           placeholder="••••••••">
                </div>
            </div>

            <!-- Terms Checkbox -->
            <div class="py-1">
                <label class="flex items-center select-none cursor-pointer">
                    <input type="checkbox" required class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600 h-4 w-4">
                    <span class="ml-2 text-[11px] font-medium text-slate-600">I agree to the <a href="#" class="font-bold text-slate-700 underline">Terms &amp; Conditions</a> and <a href="#" class="font-bold text-slate-700 underline">Privacy Policy</a>.</span>
                </label>
            </div>

            <!-- Submit Button -->
            <button type="submit"
                    class="w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs">
                Create Account &rarr;
            </button>

            <!-- Footer link to Login -->
            <div class="text-center pt-2">
                <p class="text-xs text-slate-500 font-medium">
                    Already have an account? 
                    <a href="{{ route('login') }}" class="font-bold text-emerald-700 hover:text-emerald-800 hover:underline">Log in here</a>
                </p>
            </div>
        </form>

    </div>
</div>
@endsection
