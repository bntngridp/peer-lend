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
        <form id="registerForm" class="space-y-4" action="{{ route('register') }}" method="POST">
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
                <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('phone') border-rose-300 @enderror">
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
                           class="w-full rounded-r-xl bg-transparent text-xs font-medium text-slate-800 placeholder-slate-400 outline-none border-none px-3.5 py-2.5 focus:ring-0"
                           placeholder="81575971998">
                </div>
                @error('phone')
                    <p class="mt-1 text-xs text-rose-600 font-semibold">{{ $message }}</p>
                @enderror
            </div>

            <!-- Password & Confirm Password -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Password Field -->
                <div>
                    <label for="password" class="block text-xs font-bold text-slate-700 mb-1">Password</label>
                    <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200 @error('password') border-rose-300 @enderror">
                        <input id="password" name="password" type="password" required oninput="checkPasswordRequirements(this.value)"
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

                <!-- Password Confirmation Field -->
                <div>
                    <label for="password_confirmation" class="block text-xs font-bold text-slate-700 mb-1">Confirm Password</label>
                    <div class="flex items-center w-full rounded-xl border border-slate-200 bg-white overflow-hidden px-3.5 py-2.5 focus-within:border-emerald-600 focus-within:ring-4 focus-within:ring-emerald-600/15 transition-all duration-200">
                        <input id="password_confirmation" name="password_confirmation" type="password" required
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

            <!-- Terms Checkbox with Legal Links & Inline Modal Viewers -->
            <div class="py-1">
                <label class="flex items-center select-none cursor-pointer">
                    <input type="checkbox" id="terms_agree" onchange="toggleTermsAgreement(this)" required
                           class="rounded border-slate-300 text-emerald-700 focus:ring-emerald-600 h-4 w-4 cursor-pointer">
                    <span class="ml-2 text-[11px] font-medium text-slate-600">
                        I agree to the 
                        <a href="{{ route('terms.show') }}" target="_blank" onclick="openLegalModal(event, 'terms')" class="font-bold text-emerald-700 hover:underline">Terms &amp; Conditions</a> 
                        and 
                        <a href="{{ route('privacy.show') }}" target="_blank" onclick="openLegalModal(event, 'privacy')" class="font-bold text-emerald-700 hover:underline">Privacy Policy</a>.
                    </span>
                </label>
            </div>

            <!-- Submit Button (Disabled by default until terms checked) -->
            <button type="submit" id="btn_submit" disabled
                    class="w-full py-3 rounded-xl bg-slate-300 text-slate-500 font-bold text-xs cursor-not-allowed transition-all duration-200 shadow-none border-none">
                Create Account &rarr;
            </button>

            <!-- Social Divider -->
            <div class="relative my-4">
                <div class="absolute inset-0 flex items-center">
                    <div class="w-full border-t border-slate-200"></div>
                </div>
                <div class="relative flex justify-center text-xs">
                    <span class="bg-white px-3 text-slate-400 font-semibold uppercase tracking-wider text-[10px]">Or continue with</span>
                </div>
            </div>

            <!-- Sign up with Google Button -->
            <a href="{{ route('auth.google') }}"
               class="w-full py-2.5 px-4 rounded-xl border border-slate-200 bg-white hover:bg-slate-50 text-slate-700 font-bold text-xs flex items-center justify-center gap-2.5 transition-all shadow-xs hover:border-slate-300">
                <svg class="w-4 h-4 shrink-0" viewBox="0 0 24 24">
                    <path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92c-.26 1.37-1.04 2.53-2.21 3.31v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.09z"/>
                    <path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/>
                    <path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.06H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.94l2.85-2.22.81-.63z"/>
                    <path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.06l3.66 2.84c.87-2.6 3.3-4.52 6.16-4.52z"/>
                </svg>
                <span>Sign up with Google</span>
            </a>

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

<!-- Legal Modal Viewer (Allows reading Terms & Privacy inline without losing form data) -->
<div id="legalModal" class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-slate-900/60 backdrop-blur-xs transition-opacity duration-200">
    <div class="bg-white rounded-2xl max-w-xl w-full max-h-[85vh] flex flex-col shadow-2xl border border-slate-200 overflow-hidden">
        <!-- Modal Header -->
        <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 id="modalTitle" class="text-sm font-bold text-slate-900">Legal Document</h3>
            <button type="button" onclick="closeLegalModal()" class="text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-200/60 transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
        <!-- Modal Content Container -->
        <div id="modalBody" class="px-6 py-5 overflow-y-auto text-xs text-slate-600 space-y-4 leading-relaxed font-normal">
            <!-- Dynamically Loaded -->
        </div>
        <!-- Modal Footer -->
        <div class="px-6 py-3.5 border-t border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <span class="text-[11px] text-slate-400 font-medium">LendFlow Legal Agreement</span>
            <button type="button" onclick="closeLegalModal()" class="px-4 py-2 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                Tutup &amp; Lanjutkan Pendaftaran
            </button>
        </div>
    </div>
</div>

<script>
// ─── FORM DRAFT PERSISTENCE (Auto-Save & Restore) ─────────────────────────
const DRAFT_KEY = 'lendflow_register_form_draft';

function saveFormDraft() {
    const draftData = {
        role: document.querySelector('input[name="role"]:checked')?.value || 'borrower',
        full_name: document.getElementById('full_name')?.value || '',
        email: document.getElementById('email')?.value || '',
        country_code: document.getElementById('country_code')?.value || '+62',
        phone: document.getElementById('phone')?.value || '',
        terms_agree: document.getElementById('terms_agree')?.checked || false,
    };
    try {
        localStorage.setItem(DRAFT_KEY, JSON.stringify(draftData));
    } catch (e) {
        console.warn('Unable to save form draft:', e);
    }
}

function restoreFormDraft() {
    try {
        const saved = localStorage.getItem(DRAFT_KEY);
        if (!saved) return;
        const draft = JSON.parse(saved);

        // Only restore if fields aren't already set by server old()
        const fullNameInput = document.getElementById('full_name');
        if (fullNameInput && !fullNameInput.value && draft.full_name) {
            fullNameInput.value = draft.full_name;
        }

        const emailInput = document.getElementById('email');
        if (emailInput && !emailInput.value && draft.email) {
            emailInput.value = draft.email;
        }

        const countryCodeInput = document.getElementById('country_code');
        if (countryCodeInput && draft.country_code) {
            countryCodeInput.value = draft.country_code;
        }

        const phoneInput = document.getElementById('phone');
        if (phoneInput && !phoneInput.value && draft.phone) {
            phoneInput.value = draft.phone;
        }

        if (draft.role) {
            selectRole(draft.role);
        }

        const termsCheckbox = document.getElementById('terms_agree');
        if (termsCheckbox && draft.terms_agree) {
            termsCheckbox.checked = true;
            toggleTermsAgreement(termsCheckbox);
        }

    } catch (e) {
        console.warn('Unable to restore form draft:', e);
    }
}

function clearFormDraft() {
    try {
        localStorage.removeItem(DRAFT_KEY);
    } catch (e) {}
}

// ─── ROLE SELECTOR ────────────────────────────────────────────────────────
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

    saveFormDraft();
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

function toggleTermsAgreement(checkbox) {
    const btnSubmit = document.getElementById('btn_submit');
    if (!btnSubmit) return;

    if (checkbox.checked) {
        btnSubmit.disabled = false;
        btnSubmit.className = 'w-full py-3 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 focus:ring-4 focus:ring-emerald-600/20 transition-all duration-200 shadow-xs cursor-pointer';
    } else {
        btnSubmit.disabled = true;
        btnSubmit.className = 'w-full py-3 rounded-xl bg-slate-300 text-slate-500 font-bold text-xs cursor-not-allowed transition-all duration-200 shadow-none border-none';
    }

    saveFormDraft();
}

// ─── LEGAL INLINE MODAL VIEWER ─────────────────────────────────────────────
const LEGAL_DOCS = {
    terms: {
        title: 'Terms & Conditions — LendFlow',
        content: `
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">1. Pendahuluan &amp; Ketentuan Umum</h4>
                <p>Selamat datang di LendFlow. Dengan mendaftar dan menggunakan platform Peer-to-Peer (P2P) Lending LendFlow, Anda menyatakan menyetujui seluruh Syarat dan Ketentuan ini.</p>
            </section>
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">2. Peran Pengguna (Borrower &amp; Lender)</h4>
                <ul class="list-disc pl-4 space-y-1">
                    <li><strong>Borrower (Peminjam):</strong> Peminjam wajib memberikan data KYC yang asli dan valid. Peminjam bertanggung jawab penuh atas pembayaran pokok dan bunga cicilan.</li>
                    <li><strong>Lender (Investor):</strong> Pemberi pinjaman menyadari bahwa aktivitas pendanaan memiliki risiko bisnis. LendFlow memfasilitasi penilaian kredit dan dana jaminan (collateral).</li>
                </ul>
            </section>
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">3. Verifikasi Identitas (KYC &amp; AML)</h4>
                <p>Seluruh pengguna wajib melalui prosedur Know Your Customer (KYC) dan Anti-Money Laundering (AML) sesuai regulasi yang berlaku.</p>
            </section>
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">4. Dompet Digital &amp; Keamanan</h4>
                <p>Seluruh transaksi keuangan diproses melalui Virtual Account dan dompet digital IDR terenkripsi.</p>
            </section>
        `
    },
    privacy: {
        title: 'Privacy Policy — LendFlow',
        content: `
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">1. Pengumpulan Informasi Pribadi</h4>
                <p>LendFlow mengumpulkan informasi identitas diri seperti Nama Lengkap, Alamat Email, Nomor Telepon, dokumen KYC, dan data keuangan.</p>
            </section>
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">2. Penggunaan &amp; Perlindungan Data</h4>
                <p>Informasi pribadi Anda hanya digunakan untuk verifikasi akun, penilaian risiko kredit (credit scoring), dan pemrosesan transaksi P2P Lending.</p>
            </section>
            <section class="space-y-1.5">
                <h4 class="font-bold text-slate-900 text-xs">3. Keamanan Data Terenkripsi</h4>
                <p>Seluruh data sensitif dan enkripsi kata sandi menggunakan standar industri SSL/TLS 256-bit &amp; BCRYPT Hashing.</p>
            </section>
        `
    }
};

function openLegalModal(event, docType) {
    saveFormDraft();

    // If device width is desktop or middle click / ctrl click, allow default link tab behavior while draft is saved
    if (event && (event.ctrlKey || event.metaKey)) {
        return true;
    }

    if (event) {
        event.preventDefault();
    }

    const modal = document.getElementById('legalModal');
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');

    if (!modal || !modalTitle || !modalBody || !LEGAL_DOCS[docType]) return;

    modalTitle.textContent = LEGAL_DOCS[docType].title;
    modalBody.innerHTML = LEGAL_DOCS[docType].content;

    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden';
}

function closeLegalModal() {
    const modal = document.getElementById('legalModal');
    if (!modal) return;
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = '';
}

// Close modal on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeLegalModal();
});

// Attach listeners for auto-save & auto-restore
document.addEventListener('DOMContentLoaded', function() {
    restoreFormDraft();

    // Attach input listeners
    const inputs = ['full_name', 'email', 'country_code', 'phone'];
    inputs.forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            el.addEventListener('input', saveFormDraft);
            el.addEventListener('change', saveFormDraft);
        }
    });

    const checkedRadio = document.querySelector('input[name="role"]:checked');
    if (checkedRadio) {
        selectRole(checkedRadio.value);
    }

    const passInput = document.getElementById('password');
    if (passInput && passInput.value) {
        checkPasswordRequirements(passInput.value);
    }

    const termsCheckbox = document.getElementById('terms_agree');
    if (termsCheckbox) {
        toggleTermsAgreement(termsCheckbox);
    }

    const registerForm = document.getElementById('registerForm');
    if (registerForm) {
        registerForm.addEventListener('submit', function() {
            clearFormDraft();
        });
    }
});
</script>
@endsection
