@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ profileTab: '{{ session('tab') ?? request('tab') ?? 'personal' }}', colorTheme: 'light', density: 'comfortable', disable2faModalOpen: false }">
    
    <!-- Top Header Bar -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Account &amp; System Settings</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">Manage your personal profile, security methods, active sessions, and system preferences.</p>
    </div>

    <!-- Navigation Tabs Header -->
    <div class="border-b border-slate-200 flex gap-6 text-xs font-bold text-slate-500 overflow-x-auto">
        <button @click="profileTab = 'personal'" 
                :class="profileTab === 'personal' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Personal Information
        </button>
        <button @click="profileTab = 'security'" 
                :class="profileTab === 'security' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Security &amp; Access
        </button>
        <button @click="profileTab = 'notifications'" 
                :class="profileTab === 'notifications' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            Notification Preferences
        </button>
        <button @click="profileTab = 'system'" 
                :class="profileTab === 'system' ? 'text-emerald-700 border-b-2 border-emerald-700 pb-3' : 'hover:text-slate-800 pb-3'">
            System Preferences
        </button>
    </div>

    <!-- ─── Tab 1: Personal Information ──────────────────────────────────── -->
    <div x-show="profileTab === 'personal'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6">
        <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-3">Personal Profile Details</h3>

        @if(!$user->profile?->phone)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center shrink-0 text-sm">
                    !
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900">Lengkapi Profil Akun Google Anda</p>
                    <p class="text-[11px] font-medium text-slate-600 mt-0.5">Akun Anda terdaftar melalui Google OAuth. Silakan isi <strong>Nomor Telepon</strong> dan alamat tempat tinggal di bawah untuk membuka akses transaksi &amp; pengajuan pinjaman di LendFlow.</p>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Photo -->
            <div x-data="{ avatarPreview: null }">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Profile Photo</label>
                <div class="flex items-center gap-5">
                    <template x-if="avatarPreview">
                        <img :src="avatarPreview" class="h-16 w-16 rounded-2xl object-cover border border-slate-200 shadow-xs">
                    </template>
                    <template x-if="!avatarPreview">
                        <div>
                            @if($user->profile && $user->profile->avatar_path)
                                <img class="h-16 w-16 rounded-2xl object-cover border border-slate-200 shadow-xs"
                                     src="{{ asset('storage/' . $user->profile->avatar_path) }}" alt="Avatar">
                            @else
                                <div class="h-16 w-16 rounded-2xl bg-emerald-700 text-white font-black text-xl flex items-center justify-center shadow-xs select-none">
                                    {{ strtoupper(substr($user->profile->full_name ?? $user->email, 0, 2)) }}
                                </div>
                            @endif
                        </div>
                    </template>

                    <div>
                        <input type="file" name="avatar" id="avatar" class="hidden" accept="image/*"
                               @change="
                                   const file = $event.target.files[0];
                                   if (file) {
                                       avatarPreview = URL.createObjectURL(file);
                                   }
                               ">
                        <label for="avatar" class="cursor-pointer inline-flex items-center py-2 px-4 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs">
                            Change Photo
                        </label>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">JPG or PNG. Max 2MB.</p>
                        @error('avatar')
                            <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Full Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="full_name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Full Name</label>
                    <input type="text" name="full_name" id="full_name" required value="{{ old('full_name', $user->profile->full_name ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    @error('full_name')
                        <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone Number <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" required placeholder="Contoh: 081234567890" value="{{ old('phone', $user->profile->phone ?? '') }}"
                           class="w-full rounded-xl border {{ $errors->has('phone') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    @error('phone')
                        <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
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
                    Save Profile Changes &rarr;
                </button>
            </div>
        </form>
    </div>

    <!-- ─── Tab 2: Security & Access ──────────────────────────────────────── -->
    <div x-show="profileTab === 'security'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" style="display: none;">
        
        <!-- Main Form Column (Spans 8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Password Management Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Password Management</h3>

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">CURRENT PASSWORD</label>
                        <input type="password" name="current_password" id="current_password" required placeholder="••••••••" 
                               class="w-full rounded-xl border {{ $errors->has('current_password') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                        @error('current_password')
                            <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">NEW PASSWORD</label>
                            <input type="password" name="password" id="password" required placeholder="••••••••" 
                                   class="w-full rounded-xl border {{ $errors->has('password') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                            @error('password')
                                <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">CONFIRM NEW PASSWORD</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••" 
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <button type="submit" id="btn_update_password" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                        Update Password &rarr;
                    </button>
                </form>
            </div>

            <!-- Two-Factor Authentication Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">Two-Factor Authentication (2FA)</h3>

                <div class="space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 text-xs block">Authenticator Apps</span>
                                @if(Auth::user()->google2fa_enabled)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">Active</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">Disabled</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500 font-medium mt-0.5 block">Google Authenticator, Authy, Microsoft Authenticator</span>
                        </div>
                        @if(Auth::user()->google2fa_enabled)
                            <button type="button" @click="disable2faModalOpen = true" id="btn_disable_2fa" class="py-1.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                Nonaktifkan 2FA
                            </button>
                        @else
                            <a href="{{ route('2fa.setup') }}" id="btn_setup_2fa" class="py-1.5 px-3.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs transition-colors">
                                Setup 2FA &rarr;
                            </a>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between opacity-60">
                        <div>
                            <span class="font-bold text-slate-700 text-xs block">SMS Authentication</span>
                            <span class="text-[10px] text-slate-400 font-medium">Text messages sent to +62 **** **492</span>
                        </div>
                        <span class="text-xs font-bold text-slate-400">Setup</span>
                    </div>
                </div>
            </div>

            <!-- API Tokens Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">API Tokens</h3>
                    <button type="button" @click="alert('New institutional API Token generated!')" class="py-1.5 px-3 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">
                        + Generate Token
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase">
                                <th class="py-2.5 px-3">TOKEN NAME</th>
                                <th class="py-2.5 px-3">PERMISSIONS</th>
                                <th class="py-2.5 px-3">LAST USED</th>
                                <th class="py-2.5 px-3 text-right">ACTIONS</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr class="border-b border-slate-100 font-medium text-slate-700">
                                <td class="py-3 px-3 font-bold text-slate-900">Production Key</td>
                                <td class="py-3 px-3"><span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-100 text-slate-700">Read / Write</span></td>
                                <td class="py-3 px-3 text-slate-500">2 mins ago</td>
                                <td class="py-3 px-3 text-right text-rose-600 font-bold cursor-pointer">Revoke</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Side Panel: Security Score & Active Sessions (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Security Score Card -->
            <div class="rounded-2xl border border-emerald-200 bg-emerald-50/40 p-5 shadow-xs space-y-3">
                <span class="text-[10px] font-bold text-emerald-800 uppercase tracking-wider block">SECURITY SCORE</span>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-emerald-700">{{ Auth::user()->google2fa_enabled ? '92' : '75' }}</span>
                    <span class="text-xs font-bold text-slate-400">/ 100</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-700 text-white ml-auto">
                        {{ Auth::user()->google2fa_enabled ? 'Strong Protection' : 'Good Protection' }}
                    </span>
                </div>

                <div class="space-y-2 text-xs border-t border-emerald-200/60 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600">Password Strength</span>
                        <span class="font-bold text-emerald-800">Strong</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600">2FA Setup</span>
                        <span class="font-bold {{ Auth::user()->google2fa_enabled ? 'text-emerald-800' : 'text-amber-700' }}">
                            {{ Auth::user()->google2fa_enabled ? 'Enabled' : 'Disabled' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Active Sessions Card -->
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">Active Sessions</span>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 block text-xs">Mac OS • Safari</span>
                            <span class="text-[10px] text-slate-400 font-medium block">Jakarta, ID • 182.1.22.4</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800">CURRENT</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between opacity-70">
                        <div>
                            <span class="font-bold text-slate-800 block text-xs">iOS • LendFlow App</span>
                            <span class="text-[10px] text-slate-400 font-medium block">Active 2 hours ago</span>
                        </div>
                        <button type="button" @click="alert('Session revoked!')" class="text-[10px] font-bold text-rose-600 hover:underline">Revoke</button>
                    </div>
                </div>

                <button type="button" @click="alert('All other sessions signed out!')" class="w-full py-2 rounded-xl border border-slate-200 text-xs font-bold text-slate-700 hover:bg-slate-50">
                    Sign out all other sessions
                </button>
            </div>

        </div>

    </div>

    <!-- ─── Tab 3: Notification Preferences ──────────────────────────────── -->
    <div x-show="profileTab === 'notifications'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">Notification Preferences</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Manage how LendFlow communicates important updates, alerts, and marketing information to you.</p>
        </div>

        <div class="space-y-6">
            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-2">Security Alerts</h4>
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800">Unrecognized Logins &amp; Password Changes</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Email</label>
                        <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Push</label>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-2">Financial Activity</h4>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <span class="font-bold text-slate-800">Loan Approvals &amp; Updates</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Email</label>
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Push</label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-800">Investment Milestones &amp; Returns</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Email</label>
                            <label class="flex items-center gap-1.5 text-slate-600 font-semibold cursor-pointer"><input type="checkbox" checked class="accent-emerald-700"> Push</label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="button" @click="alert('Notification preferences updated!')" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                Save Preferences &rarr;
            </button>
        </div>
    </div>

    <!-- ─── Tab 4: System Preferences & Appearance ───────────────────────── -->
    <div x-show="profileTab === 'system'" class="rounded-2xl border border-slate-200 bg-white p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 border-b border-slate-100 pb-2">System Preferences</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">Manage your global application settings, appearance, and privacy controls.</p>
        </div>

        <div class="space-y-6">
            <!-- Appearance Settings -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-6">
                <h4 class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-2">Appearance</h4>

                <div class="space-y-4">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Color Theme</label>
                        <div class="flex gap-3">
                            <button type="button" @click="colorTheme = 'light'"
                                    :class="colorTheme === 'light' ? 'border-emerald-700 bg-white shadow-xs' : 'border-slate-200 bg-slate-100'"
                                    class="py-2 px-6 rounded-xl border text-xs font-bold text-slate-800 flex items-center gap-2">
                                Light Theme
                            </button>
                            <button type="button" @click="colorTheme = 'dark'"
                                    :class="colorTheme === 'dark' ? 'border-emerald-700 bg-slate-900 text-white shadow-xs' : 'border-slate-200 bg-slate-100'"
                                    class="py-2 px-6 rounded-xl border text-xs font-bold text-slate-700 flex items-center gap-2">
                                Dark Theme
                            </button>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Data Density</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label @click="density = 'comfortable'"
                                   :class="density === 'comfortable' ? 'border-emerald-700 bg-white' : 'border-slate-200 bg-slate-100'"
                                   class="p-3.5 rounded-xl border flex items-center justify-between cursor-pointer">
                                <div>
                                    <span class="font-bold text-slate-900 text-xs block">Comfortable</span>
                                    <span class="text-[10px] text-slate-500 font-medium">More whitespace, easier to read.</span>
                                </div>
                                <input type="radio" name="density" value="comfortable" checked class="accent-emerald-700">
                            </label>

                            <label @click="density = 'compact'"
                                   :class="density === 'compact' ? 'border-emerald-700 bg-white' : 'border-slate-200 bg-slate-100'"
                                   class="p-3.5 rounded-xl border flex items-center justify-between cursor-pointer">
                                <div>
                                    <span class="font-bold text-slate-900 text-xs block">Compact</span>
                                    <span class="text-[10px] text-slate-500 font-medium">High information density for trading.</span>
                                </div>
                                <input type="radio" name="density" value="compact" class="accent-emerald-700">
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Privacy & Data -->
            <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 border-b border-slate-200 pb-2">Privacy &amp; Data Controls</h4>

                <div class="space-y-3 text-xs font-medium">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <div>
                            <span class="font-bold text-slate-800 block">Public Profile Visibility</span>
                            <span class="text-[10px] text-slate-500">Allow other institutional members to discover your profile in the directory.</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60">
                        <div>
                            <span class="font-bold text-slate-800 block">Data Sharing for Analytics</span>
                            <span class="text-[10px] text-slate-500">Share anonymized usage data to help us improve platform performance.</span>
                        </div>
                        <input type="checkbox" class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <div>
                            <span class="font-bold text-slate-800 block">Third-Party Integrations</span>
                            <span class="text-[10px] text-slate-500">Allow connected API apps to read basic profile information.</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-700 h-4 w-4">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="button" @click="alert('System preferences saved!')" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    Save Changes &rarr;
                </button>
            </div>
        </div>
    </div>

    <!-- Disable 2FA Confirmation Modal -->
    <div x-show="disable2faModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" 
         style="display: none;">
        
        <div @click.away="disable2faModalOpen = false" 
             class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl border border-slate-200 space-y-5 transform transition-all text-center">
            
            <!-- Warning Shield Circle -->
            <div class="mx-auto h-12 w-12 rounded-full bg-rose-50 border border-rose-200 text-rose-600 flex items-center justify-center">
                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                </svg>
            </div>

            <div>
                <h3 class="text-base font-extrabold text-slate-900">Konfirmasi Nonaktifkan 2FA</h3>
                <p class="text-xs text-slate-500 font-medium mt-1.5 leading-relaxed">
                    Apakah Anda yakin ingin menonaktifkan Two-Factor Authentication (2FA)? Akun Anda akan menjadi kurang terlindungi dari risiko akses tanpa izin.
                </p>
            </div>

            <div class="flex items-center gap-3 pt-2">
                <button type="button" @click="disable2faModalOpen = false" 
                        class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs cursor-pointer">
                    Batal
                </button>
                <form action="{{ route('2fa.disable') }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" id="btn_confirm_disable_2fa" 
                            class="w-full py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        Ya, Nonaktifkan 2FA
                    </button>
                </form>
            </div>
        </div>
    </div>

</div>
@endsection
