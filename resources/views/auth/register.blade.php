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

            <!-- Sleek, Compact & Light-Border Role Selection Cards -->
            <div class="mb-5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2">I want to register as:</label>

                <div class="grid grid-cols-2 gap-2.5">
                    <!-- Borrower Card -->
                    <label id="card-borrower" onclick="selectRole('borrower')"
                           class="relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 shadow-xs">
                        <input type="radio" name="role" value="borrower" id="radio-borrower" class="sr-only" {{ old('role', 'borrower') === 'borrower' ? 'checked' : '' }}>
                        
                        <!-- Radio Circle Dot -->
                        <div id="check-borrower" class="w-4 h-4 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center shrink-0 transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-white"></div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 leading-tight">Borrower</div>
                            <div class="text-[10px] font-medium text-slate-500 truncate">Apply for credit</div>
                        </div>
                    </label>

                    <!-- Lender Card -->
                    <label id="card-lender" onclick="selectRole('lender')"
                           class="relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300">
                        <input type="radio" name="role" value="lender" id="radio-lender" class="sr-only" {{ old('role') === 'lender' ? 'checked' : '' }}>
                        
                        <!-- Radio Circle Dot -->
                        <div id="check-lender" class="w-4 h-4 rounded-full border border-slate-300 bg-white flex items-center justify-center shrink-0 transition-all">
                            <div class="w-1.5 h-1.5 rounded-full bg-white hidden"></div>
                        </div>

                        <div class="min-w-0">
                            <div class="text-xs font-bold text-slate-900 leading-tight">Lender (Investor)</div>
                            <div class="text-[10px] font-medium text-slate-500 truncate">Fund capital &amp; earn</div>
                        </div>
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

    if (!cardBorrower || !cardLender) return;

    if (role === 'borrower') {
        if (radioBorrower) radioBorrower.checked = true;
        if (radioLender) radioLender.checked = false;

        cardBorrower.className = 'relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 shadow-xs';
        cardLender.className = 'relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300';

        if (checkBorrower) {
            checkBorrower.className = 'w-4 h-4 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center shrink-0 transition-all';
            const dot = checkBorrower.querySelector('div');
            if (dot) dot.classList.remove('hidden');
        }

        if (checkLender) {
            checkLender.className = 'w-4 h-4 rounded-full border border-slate-300 bg-white flex items-center justify-center shrink-0 transition-all';
            const dot = checkLender.querySelector('div');
            if (dot) dot.classList.add('hidden');
        }
    } else {
        if (radioLender) radioLender.checked = true;
        if (radioBorrower) radioBorrower.checked = false;

        cardLender.className = 'relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 shadow-xs';
        cardBorrower.className = 'relative flex items-center gap-3 p-3 border rounded-xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300';

        if (checkLender) {
            checkLender.className = 'w-4 h-4 rounded-full border-2 border-emerald-600 bg-emerald-600 flex items-center justify-center shrink-0 transition-all';
            const dot = checkLender.querySelector('div');
            if (dot) dot.classList.remove('hidden');
        }

        if (checkBorrower) {
            checkBorrower.className = 'w-4 h-4 rounded-full border border-slate-300 bg-white flex items-center justify-center shrink-0 transition-all';
            const dot = checkBorrower.querySelector('div');
            if (dot) dot.classList.add('hidden');
        }
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="role"]:checked');
    if (checkedRadio) {
        selectRole(checkedRadio.value);
    }
});
</script>
@endsection
