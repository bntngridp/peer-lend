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

            <!-- Premium Visual Role Selection Cards -->
            <div class="mb-5">
                <label class="block text-[11px] font-bold text-slate-500 uppercase tracking-wider mb-2.5">Select Account Type:</label>

                <div class="grid grid-cols-2 gap-3">
                    <!-- Borrower Card -->
                    <label id="card-borrower" onclick="selectRole('borrower')"
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/15 shadow-xs">
                        <input type="radio" name="role" value="borrower" id="radio-borrower" class="sr-only" {{ old('role', 'borrower') === 'borrower' ? 'checked' : '' }}>
                        
                        <!-- Top Row: Icon + Check Indicator -->
                        <div class="flex items-center justify-between w-full mb-2.5">
                            <div id="icon-borrower" class="w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center transition-colors shadow-xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5m3 0v-4a1 1 0 011-1h2a1 1 0 011 1v4m-4 0h4" />
                                </svg>
                            </div>

                            <!-- Check Badge -->
                            <div id="check-borrower" class="w-5 h-5 rounded-full border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center transition-all">
                                <svg class="w-3 h-3 stroke-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <div>
                            <div class="text-xs font-black text-slate-900 leading-snug">Borrower</div>
                            <div class="text-[11px] font-medium text-slate-500 leading-tight mt-0.5">Ajukan Pinjaman &amp; Modal Usaha</div>
                        </div>
                    </label>

                    <!-- Lender Card -->
                    <label id="card-lender" onclick="selectRole('lender')"
                           class="relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300">
                        <input type="radio" name="role" value="lender" id="radio-lender" class="sr-only" {{ old('role') === 'lender' ? 'checked' : '' }}>
                        
                        <!-- Top Row: Icon + Check Indicator -->
                        <div class="flex items-center justify-between w-full mb-2.5">
                            <div id="icon-lender" class="w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center transition-colors shadow-xs">
                                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                                </svg>
                            </div>

                            <!-- Check Badge -->
                            <div id="check-lender" class="w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center transition-all">
                                <svg class="w-3 h-3 stroke-white hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                        </div>

                        <!-- Title & Description -->
                        <div>
                            <div class="text-xs font-black text-slate-900 leading-snug">Lender (Investor)</div>
                            <div class="text-[11px] font-medium text-slate-500 leading-tight mt-0.5">Danai Pinjaman &amp; Imbal Hasil</div>
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

            <!-- Phone Number with Country Code Dropdown Selector -->
            <div>
                <label for="phone" class="block text-xs font-bold text-slate-700 mb-1">Phone Number</label>
                <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('phone') border-rose-300 @enderror">
                    <!-- Country Code Selector -->
                    <select id="country_code" name="country_code"
                            class="rounded-l-xl border-r border-slate-200 bg-slate-50/80 px-2.5 py-2.5 text-xs font-bold text-slate-700 outline-none hover:bg-slate-100 transition-colors cursor-pointer shrink-0">
                        <option value="+62" {{ old('country_code', '+62') === '+62' ? 'selected' : '' }}>🇮🇩 +62</option>
                        <option value="+60" {{ old('country_code') === '+60' ? 'selected' : '' }}>🇲🇾 +60</option>
                        <option value="+65" {{ old('country_code') === '+65' ? 'selected' : '' }}>🇸🇬 +65</option>
                        <option value="+1"  {{ old('country_code') === '+1'  ? 'selected' : '' }}>🇺🇸 +1</option>
                        <option value="+44" {{ old('country_code') === '+44' ? 'selected' : '' }}>🇬🇧 +44</option>
                        <option value="+61" {{ old('country_code') === '+61' ? 'selected' : '' }}>🇦🇺 +61</option>
                        <option value="+81" {{ old('country_code') === '+81' ? 'selected' : '' }}>🇯🇵 +81</option>
                        <option value="+66" {{ old('country_code') === '+66' ? 'selected' : '' }}>🇹🇭 +66</option>
                        <option value="+84" {{ old('country_code') === '+84' ? 'selected' : '' }}>🇻🇳 +84</option>
                        <option value="+63" {{ old('country_code') === '+63' ? 'selected' : '' }}>🇵🇭 +63</option>
                    </select>
                    
                    <!-- Phone Input -->
                    <input id="phone" name="phone" type="tel" required value="{{ old('phone') }}"
                           class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none px-3.5 py-2.5 focus:ring-0"
                           placeholder="81575971998">
                </div>
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password and Confirmation with Embedded Flex Eye Buttons -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('password') border-rose-300 @enderror">
                        <input id="password" name="password" type="password" required
                               class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none p-0 focus:ring-0"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePass('password', this)" class="ml-2 text-slate-400 hover:text-emerald-700 focus:outline-none transition-colors shrink-0 p-0.5" title="Toggle password visibility">
                            <svg class="eye-open w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.763c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.499 4.14m-5.8-5.8a3 3 0 11-4.243-4.243m4.242 4.242L3 3l18 18" />
                            </svg>
                        </button>
                    </div>
                    @error('password')
                        <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Password Confirmation Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password</label>
                    <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="w-full bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none p-0 focus:ring-0"
                               placeholder="••••••••">
                        <button type="button" onclick="togglePass('password_confirmation', this)" class="ml-2 text-slate-400 hover:text-emerald-700 focus:outline-none transition-colors shrink-0 p-0.5" title="Toggle confirm password visibility">
                            <svg class="eye-open w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                            </svg>
                            <svg class="eye-closed w-4 h-4 hidden" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858-5.908a10.018 10.018 0 013.682-.763c4.478 0 8.268 2.943 9.542 7a9.97 9.97 0 01-2.499 4.14m-5.8-5.8a3 3 0 11-4.243-4.243m4.242 4.242L3 3l18 18" />
                            </svg>
                        </button>
                    </div>
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

        cardBorrower.className = 'relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/15 shadow-xs';
        cardLender.className = 'relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300';

        if (checkBorrower) {
            checkBorrower.className = 'w-5 h-5 rounded-full border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center transition-all';
            const svg = checkBorrower.querySelector('svg');
            if (svg) svg.classList.remove('hidden');
        }

        if (checkLender) {
            checkLender.className = 'w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center transition-all';
            const svg = checkLender.querySelector('svg');
            if (svg) svg.classList.add('hidden');
        }

        if (iconBorrower) iconBorrower.className = 'w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center transition-colors shadow-xs';
        if (iconLender) iconLender.className = 'w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center transition-colors shadow-xs';
    } else {
        if (radioLender) radioLender.checked = true;
        if (radioBorrower) radioBorrower.checked = false;

        cardLender.className = 'relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-emerald-500 bg-emerald-50/60 ring-2 ring-emerald-500/15 shadow-xs';
        cardBorrower.className = 'relative flex flex-col p-3.5 border-2 rounded-2xl cursor-pointer transition-all duration-200 select-none text-left w-full border-slate-200 bg-white hover:border-slate-300';

        if (checkLender) {
            checkLender.className = 'w-5 h-5 rounded-full border border-emerald-500 bg-emerald-600 text-white flex items-center justify-center transition-all';
            const svg = checkLender.querySelector('svg');
            if (svg) svg.classList.remove('hidden');
        }

        if (checkBorrower) {
            checkBorrower.className = 'w-5 h-5 rounded-full border border-slate-300 bg-white flex items-center justify-center transition-all';
            const svg = checkBorrower.querySelector('svg');
            if (svg) svg.classList.add('hidden');
        }

        if (iconLender) iconLender.className = 'w-9 h-9 rounded-xl bg-emerald-600 text-white flex items-center justify-center transition-colors shadow-xs';
        if (iconBorrower) iconBorrower.className = 'w-9 h-9 rounded-xl bg-slate-100 text-slate-500 flex items-center justify-center transition-colors shadow-xs';
    }
}

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

document.addEventListener('DOMContentLoaded', function() {
    const checkedRadio = document.querySelector('input[name="role"]:checked');
    if (checkedRadio) {
        selectRole(checkedRadio.value);
    }
});
</script>
@endsection
