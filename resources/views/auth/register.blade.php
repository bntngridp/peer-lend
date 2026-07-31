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

            <!-- Guaranteed Clickable Role Selection Cards -->
            <div class="mb-5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2.5">I want to register as:</label>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Borrower Card -->
                    <label id="card-borrower" onclick="selectRole('borrower')"
                           class="relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-700 bg-emerald-50/80 ring-2 ring-emerald-600/20">
                        <input type="radio" name="role" value="borrower" id="radio-borrower" class="sr-only" {{ old('role', 'borrower') === 'borrower' ? 'checked' : '' }}>
                        
                        <!-- Checkmark Indicator -->
                        <div id="check-borrower" class="absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center transition-all">
                            <svg class="w-3 h-3 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <!-- Role Icon -->
                        <div id="icon-borrower" class="w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-2.5 transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>

                        <span class="text-xs font-black text-slate-900 mb-0.5">Borrower</span>
                        <span class="text-[10px] font-semibold text-slate-500 leading-tight">Apply for credit &amp; business loans</span>
                    </label>

                    <!-- Lender Card -->
                    <label id="card-lender" onclick="selectRole('lender')"
                           class="relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 hover:border-slate-300 bg-slate-50/50">
                        <input type="radio" name="role" value="lender" id="radio-lender" class="sr-only" {{ old('role') === 'lender' ? 'checked' : '' }}>
                        
                        <!-- Checkmark Indicator -->
                        <div id="check-lender" class="absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center transition-all">
                            <svg class="w-3 h-3 text-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>

                        <!-- Role Icon -->
                        <div id="icon-lender" class="w-8 h-8 rounded-xl bg-slate-200/60 text-slate-600 flex items-center justify-center mb-2.5 transition-colors shadow-xs">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                            </svg>
                        </div>

                        <span class="text-xs font-black text-slate-900 mb-0.5">Lender (Investor)</span>
                        <span class="text-[10px] font-semibold text-slate-500 leading-tight">Fund loans &amp; earn interest yield</span>
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

<script>
function selectRole(role) {
    const radioBorrower = document.getElementById('radio-borrower');
    const radioLender = document.getElementById('radio-lender');
    
    const cardBorrower = document.getElementById('card-borrower');
    const cardLender = document.getElementById('card-lender');

    const checkBorrower = document.getElementById('check-borrower');
    const checkLender = document.getElementById('check-lender');

    const iconBorrower = document.getElementById('icon-borrower');
    const iconLender = document.getElementById('icon-lender');

    if (!cardBorrower || !cardLender) return;

    if (role === 'borrower') {
        if (radioBorrower) radioBorrower.checked = true;
        if (radioLender) radioLender.checked = false;

        cardBorrower.className = 'relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-700 bg-emerald-50/80 ring-2 ring-emerald-600/20';
        cardLender.className = 'relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 hover:border-slate-300 bg-slate-50/50';

        if (checkBorrower) {
            checkBorrower.className = 'absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center transition-all';
            const svg = checkBorrower.querySelector('svg');
            if (svg) svg.classList.remove('hidden');
        }

        if (checkLender) {
            checkLender.className = 'absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center transition-all';
            const svg = checkLender.querySelector('svg');
            if (svg) svg.classList.add('hidden');
        }

        if (iconBorrower) iconBorrower.className = 'w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-2.5 transition-colors shadow-xs';
        if (iconLender) iconLender.className = 'w-8 h-8 rounded-xl bg-slate-200/60 text-slate-600 flex items-center justify-center mb-2.5 transition-colors shadow-xs';
    } else {
        if (radioLender) radioLender.checked = true;
        if (radioBorrower) radioBorrower.checked = false;

        cardLender.className = 'relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-700 bg-emerald-50/80 ring-2 ring-emerald-600/20';
        cardBorrower.className = 'relative flex flex-col items-start p-4 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 hover:border-slate-300 bg-slate-50/50';

        if (checkLender) {
            checkLender.className = 'absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center transition-all';
            const svg = checkLender.querySelector('svg');
            if (svg) svg.classList.remove('hidden');
        }

        if (checkBorrower) {
            checkBorrower.className = 'absolute top-3.5 right-3.5 w-5 h-5 rounded-full border-2 border-slate-300 bg-white flex items-center justify-center transition-all';
            const svg = checkBorrower.querySelector('svg');
            if (svg) svg.classList.add('hidden');
        }

        if (iconLender) iconLender.className = 'w-8 h-8 rounded-xl bg-emerald-600 text-white flex items-center justify-center mb-2.5 transition-colors shadow-xs';
        if (iconBorrower) iconBorrower.className = 'w-8 h-8 rounded-xl bg-slate-200/60 text-slate-600 flex items-center justify-center mb-2.5 transition-colors shadow-xs';
    }
}
</script>
@endsection
