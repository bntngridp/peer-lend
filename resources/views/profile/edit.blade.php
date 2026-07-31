@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-5xl mx-auto" x-data="{ profileTab: 'personal' }">
    
    <!-- Top Header Bar -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account &amp; Settings</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">Manage your personal profile, notification preferences, and security settings.</p>
    </div>

    <!-- Navigation Tabs Header -->
    <div class="border-b border-slate-200 flex gap-6 text-xs font-bold text-slate-500">
        <button @click="profileTab = 'personal'" 
                :class="profileTab === 'personal' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Personal Information
        </button>
        <button @click="profileTab = 'notifications'" 
                :class="profileTab === 'notifications' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Notification Preferences
        </button>
        <button @click="profileTab = 'security'" 
                :class="profileTab === 'security' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Security &amp; Access
        </button>
    </div>

    <!-- ─── Tab 1: Personal Information ──────────────────────────────────── -->
    <div x-show="profileTab === 'personal'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Personal Profile</h3>

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Photo -->
            <div>
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Profile Photo</label>
                <div class="flex items-center gap-5">
                    @if($user->profile && $user->profile->avatar_path)
                        <img class="h-16 w-16 rounded-2xl object-cover border border-slate-200 shadow-xs"
                             src="{{ asset('storage/' . $user->profile->avatar_path) }}" alt="Avatar">
                    @else
                        <div class="h-16 w-16 rounded-2xl bg-emerald-700 text-white font-black text-xl flex items-center justify-center shadow-xs">
                            {{ strtoupper(substr($user->profile->full_name ?? $user->email, 0, 2)) }}
                        </div>
                    @endif
                    <div>
                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*">
                        <label for="avatar" class="cursor-pointer inline-flex items-center py-2 px-4 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                            Change Photo
                        </label>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">JPG or PNG. Max 2MB.</p>
                    </div>
                </div>
            </div>

            <!-- Full Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="full_name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required value="{{ old('full_name', $user->profile->full_name ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>

                <div>
                    <label for="phone" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number</label>
                    <input type="text" name="phone" id="phone" required value="{{ old('phone', $user->profile->phone ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
            </div>

            <!-- Residential Address -->
            <div>
                <label for="address" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Residential Address</label>
                <textarea name="address" id="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-emerald-600">{{ old('address', $user->profile->address ?? '') }}</textarea>
            </div>

            <!-- Occupation & Monthly Income -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label for="occupation" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Occupation / Business</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $user->profile->occupation ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                </div>

                <div>
                    <label for="monthly_income" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Monthly Income (IDR)</label>
                    <input type="number" name="monthly_income" id="monthly_income" value="{{ old('monthly_income', $user->profile->monthly_income ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Save Changes &rarr;
                </button>
            </div>
        </form>
    </div>

    <!-- ─── Tab 2: Notification Preferences ──────────────────────────────── -->
    <div x-show="profileTab === 'notifications'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Notification Preferences</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Manage how LendFlow communicates important updates, alerts, and marketing information to you.</p>
        </div>

        <div class="space-y-6">
            <!-- Section 1: Security Alerts -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Security Alerts</h4>
                        <p class="text-[11px] text-slate-500 font-medium">Critical alerts regarding your account security, login attempts, and password changes.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between">
                        <span class="font-bold text-slate-800">Unrecognized Logins</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Email
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Push
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Section 2: Financial Activity -->
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <div class="flex items-center justify-between border-b border-slate-200 pb-3">
                    <div>
                        <h4 class="text-xs font-bold text-slate-900">Financial Activity</h4>
                        <p class="text-[11px] text-slate-500 font-medium">Updates on deposits, withdrawals, loan status changes, and investment returns.</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="font-bold text-slate-800">Loan Approvals &amp; Updates</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Email
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Push
                            </label>
                        </div>
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-800">Investment Milestones &amp; Returns</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Email
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer">
                                <input type="checkbox" checked class="accent-emerald-700"> Push
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" @click="alert('Notification preferences updated!')" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                Save Preferences &rarr;
            </button>
        </div>
    </div>

    <!-- ─── Tab 3: Security & Access ──────────────────────────────────────── -->
    <div x-show="profileTab === 'security'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Security &amp; Access</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Manage 2-Factor Authentication (2FA) and password security.</p>
        </div>

        <div class="space-y-4">
            <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                <div>
                    <h4 class="text-xs font-bold text-slate-900">Two-Factor Authentication (2FA)</h4>
                    <p class="text-[11px] text-slate-500 font-medium">Add an extra layer of security using Google Authenticator or TOTP.</p>
                </div>
                <a href="{{ route('2fa.setup') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Configure 2FA
                </a>
            </div>
        </div>
    </div>

</div>
@endsection
