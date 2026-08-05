@extends('layouts.app')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto" x-data="{ profileTab: (new URLSearchParams(window.location.search)).get('tab') || localStorage.getItem('lendflow_active_tab') || '{{ session('tab') ?? request('tab') ?? 'personal' }}', colorTheme: localStorage.getItem('lendflow_theme') || '{{ $user->profile?->system_preferences['color_theme'] ?? 'light' }}', density: localStorage.getItem('lendflow_density') || '{{ $user->profile?->system_preferences['data_density'] ?? 'comfortable' }}', disable2faModalOpen: false, generateTokenModalOpen: false, revokeSessionsModalOpen: false }" x-init="$watch('profileTab', tab => { localStorage.setItem('lendflow_active_tab', tab); const u = new URL(window.location.href); u.searchParams.set('tab', tab); window.history.replaceState({}, '', u); })">
    
    <!-- Top Header Bar -->
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900 dark:text-slate-100 tracking-tight">{{ __('Account Settings') }}</h1>
        <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Manage your personal profile, security methods, active sessions, and system preferences.') }}</p>
    </div>

    <!-- Navigation Tabs Header -->
    <div class="border-b border-slate-200 dark:border-slate-800 flex gap-6 text-xs font-bold text-slate-500 overflow-x-auto select-none">
        <button type="button" @click="profileTab = 'personal'" id="tab_btn_personal"
                class="tab-btn pb-3 transition-colors outline-none focus:outline-none bg-transparent cursor-pointer"
                :class="profileTab === 'personal' ? 'text-emerald-700 border-b-2 border-emerald-700 dark:text-emerald-400 dark:border-emerald-400 font-extrabold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100'">
            {{ __('Personal Information') }}
        </button>
        <button type="button" @click="profileTab = 'security'" id="tab_btn_security"
                class="tab-btn pb-3 transition-colors outline-none focus:outline-none bg-transparent cursor-pointer"
                :class="profileTab === 'security' ? 'text-emerald-700 border-b-2 border-emerald-700 dark:text-emerald-400 dark:border-emerald-400 font-extrabold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100'">
            {{ __('Security & Access') }}
        </button>
        <button type="button" @click="profileTab = 'notifications'" id="tab_btn_notifications"
                class="tab-btn pb-3 transition-colors outline-none focus:outline-none bg-transparent cursor-pointer"
                :class="profileTab === 'notifications' ? 'text-emerald-700 border-b-2 border-emerald-700 dark:text-emerald-400 dark:border-emerald-400 font-extrabold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100'">
            {{ __('Notification Preferences') }}
        </button>
        <button type="button" @click="profileTab = 'system'" id="tab_btn_system"
                class="tab-btn pb-3 transition-colors outline-none focus:outline-none bg-transparent cursor-pointer"
                :class="profileTab === 'system' ? 'text-emerald-700 border-b-2 border-emerald-700 dark:text-emerald-400 dark:border-emerald-400 font-extrabold' : 'text-slate-500 hover:text-slate-900 dark:text-slate-400 dark:hover:text-slate-100'">
            {{ __('System Preferences') }}
        </button>
    </div>

    <!-- ─── Tab 1: Personal Information ──────────────────────────────────── -->
    <div x-show="profileTab === 'personal'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs space-y-6">
        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Personal Profile Details') }}</h3>

        @if(!$user->profile?->phone)
            <div class="rounded-xl border border-amber-200 bg-amber-50 p-4 text-amber-900 flex items-start gap-3">
                <div class="h-8 w-8 rounded-full bg-amber-100 text-amber-700 font-bold flex items-center justify-center shrink-0 text-sm">
                    !
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-900 dark:text-slate-100">{{ __('Complete Google Profile') }}</p>
                    <p class="text-[11px] font-medium text-slate-600 mt-0.5">{{ __('Your account is registered via Google OAuth. Please fill in your phone number and residential address below to enable transaction & borrowing access.') }}</p>
                </div>
            </div>
        @endif

        <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- Profile Photo -->
            <div x-data="{ avatarPreview: null }">
                <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">{{ __('Profile Photo') }}</label>
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
                            {{ __('Change Photo') }}
                        </label>
                        <p class="text-[10px] text-slate-400 font-medium mt-1">JPG {{ __('or') }} PNG. {{ __('Max') }} {{ __n('2') }}MB.</p>
                        @error('avatar')
                            <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>

            <!-- Full Name & Phone -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label for="full_name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Full Name') }}</label>
                    <input type="text" name="full_name" id="full_name" required value="{{ old('full_name', $user->profile->full_name ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    @error('full_name')
                        <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="phone" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Phone Number') }} <span class="text-rose-500">*</span></label>
                    <input type="text" name="phone" id="phone" required placeholder="081234567890" value="{{ old('phone', $user->profile->phone ?? '') }}"
                           class="w-full rounded-xl border {{ $errors->has('phone') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                    @error('phone')
                        <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <!-- Residential Address -->
            <div>
                <label for="address" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Residential Address') }}</label>
                <textarea name="address" id="address" rows="2" class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-medium text-slate-800 outline-none focus:border-emerald-600">{{ old('address', $user->profile->address ?? '') }}</textarea>
            </div>

            <!-- Occupation & Monthly Income -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                <div>
                    <label for="occupation" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Occupation / Business') }}</label>
                    <input type="text" name="occupation" id="occupation" value="{{ old('occupation', $user->profile->occupation ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                </div>

                <div>
                    <label for="monthly_income" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Monthly Income (IDR)') }}</label>
                    <input type="number" name="monthly_income" id="monthly_income" value="{{ old('monthly_income', $user->profile->monthly_income ?? '') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none">
                </div>
            </div>

            <div class="pt-2">
                <button type="submit" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                    {{ __('Save Changes') }} &rarr;
                </button>
            </div>
        </form>
    </div>

    <!-- ─── Tab 2: Security & Access ──────────────────────────────────────── -->
    <div x-show="profileTab === 'security'" class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start" style="display: none;">
        
        <!-- Main Form Column (Spans 8 Cols) -->
        <div class="lg:col-span-8 space-y-6">
            
            <!-- Password Management Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Password Management') }}</h3>

                <form action="{{ route('profile.password.update') }}" method="POST" class="space-y-4">
                    @csrf
                    @method('PUT')

                    <div>
                        <label for="current_password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('CURRENT PASSWORD') }}</label>
                        <input type="password" name="current_password" id="current_password" required placeholder="••••••••" 
                               class="w-full rounded-xl border {{ $errors->has('current_password') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                        @error('current_password')
                            <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label for="password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('NEW PASSWORD') }}</label>
                            <input type="password" name="password" id="password" required placeholder="••••••••" 
                                   class="w-full rounded-xl border {{ $errors->has('password') ? 'border-rose-500 bg-rose-50/50' : 'border-slate-200' }} px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                            @error('password')
                                <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                        <div>
                            <label for="password_confirmation" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('CONFIRM NEW PASSWORD') }}</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" required placeholder="••••••••" 
                                   class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600">
                        </div>
                    </div>

                    <button type="submit" id="btn_update_password" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                        {{ __('Update Password') }} &rarr;
                    </button>
                </form>
            </div>

            <!-- Two-Factor Authentication Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">{{ __('Two-Factor Authentication (2FA)') }}</h3>

                <div class="space-y-3">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ __('Authenticator Apps') }}</span>
                                @if(Auth::user()->google2fa_enabled)
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ __('Active') }}</span>
                                @else
                                    <span class="px-2 py-0.5 rounded-md text-[10px] font-bold bg-slate-200 text-slate-700">{{ __('Disabled') }}</span>
                                @endif
                            </div>
                            <span class="text-[10px] text-slate-500 font-medium mt-0.5 block">{{ __('Google Authenticator, Authy, Microsoft Authenticator') }}</span>
                        </div>
                        @if(Auth::user()->google2fa_enabled)
                            <button type="button" @click="disable2faModalOpen = true" id="btn_disable_2fa" class="py-1.5 px-3.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                                {{ __('Disable 2FA') }}
                            </button>
                        @else
                            <a href="{{ route('2fa.setup') }}" id="btn_setup_2fa" class="py-1.5 px-3.5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 shadow-xs transition-colors">
                                {{ __('Setup 2FA') }} &rarr;
                            </a>
                        @endif
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between opacity-60">
                        <div>
                            <span class="font-bold text-slate-700 text-xs block">{{ __('SMS Authentication') }}</span>
                            <span class="text-[10px] text-slate-400 font-medium">{{ __('Text messages sent to') }} {{ __n('+62 **** **492') }}</span>
                        </div>
                        <span class="text-xs font-bold text-slate-400">{{ __('Setup') }}</span>
                    </div>
                </div>
            </div>

            <!-- API Tokens Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 border-b border-slate-100 pb-3">
                    <div>
                        <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">{{ __('API Tokens') }}</h3>
                        <p class="text-[11px] text-slate-500 font-medium">{{ __('Authenticated access for institutions, trading bots & external API integrations.') }}</p>
                    </div>
                    <button type="button" @click="generateTokenModalOpen = true" id="btn_open_generate_token_modal"
                            class="py-1.5 px-3.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white text-xs font-bold shadow-xs transition-colors cursor-pointer self-start sm:self-auto">
                        {{ __('+ Generate Token') }}
                    </button>
                </div>

                @if(session('generated_api_token'))
                    <div class="p-4 rounded-xl border border-emerald-200 bg-emerald-50 text-slate-900 space-y-2">
                        <div class="flex items-center justify-between">
                            <span class="text-xs font-bold text-emerald-900">Token Baru Berhasil Dibuat: {{ session('generated_api_token')['name'] }}</span>
                            <span class="text-[10px] font-bold text-amber-700 bg-amber-100 px-2 py-0.5 rounded">Simpan Sekarang!</span>
                        </div>
                        <p class="text-[11px] text-slate-600 font-medium">Salin dan simpan token ini di tempat aman. Demi alasan keamanan, token ini <strong>tidak akan ditampilkan lagi</strong> setelah halaman ditutup.</p>
                        <div class="flex items-center gap-2 pt-1" x-data="{ tokenCopied: false }">
                            <input type="text" readonly value="{{ session('generated_api_token')['token'] }}" 
                                   class="flex-1 font-mono text-xs font-bold bg-white border border-emerald-300 rounded-xl px-3 py-2 text-emerald-800 select-all outline-none">
                            <button type="button" 
                                    @click="
                                        navigator.clipboard.writeText('{{ session('generated_api_token')['token'] }}');
                                        tokenCopied = true;
                                        setTimeout(() => tokenCopied = false, 2500);
                                    "
                                    class="px-3.5 py-2 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs transition-colors shadow-xs shrink-0 cursor-pointer">
                                <span x-text="tokenCopied ? 'Tercopy!' : 'Salin Token'">Salin Token</span>
                            </button>
                        </div>
                    </div>
                @endif

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase">
                                <th class="py-2.5 px-3">{{ __('TOKEN NAME') }}</th>
                                <th class="py-2.5 px-3">{{ __('PERMISSIONS') }}</th>
                                <th class="py-2.5 px-3">{{ __('LAST USED') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('ACTIONS') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse(Auth::user()->apiTokens as $apiToken)
                                <tr class="border-b border-slate-100 font-medium text-slate-700">
                                    <td class="py-3 px-3 font-bold text-slate-900 dark:text-slate-100">{{ $apiToken->name }}</td>
                                    <td class="py-3 px-3">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $apiToken->permissions === 'Read / Write' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-slate-100 text-slate-700' }}">
                                            {{ __($apiToken->permissions) }}
                                        </span>
                                    </td>
                                    <td class="py-3 px-3 text-slate-500">
                                        {{ $apiToken->last_used_at ? $apiToken->last_used_at->diffForHumans() : __('Never used') }}
                                    </td>
                                    <td class="py-3 px-3 text-right">
                                        <form action="{{ route('profile.tokens.destroy', $apiToken) }}" method="POST" onsubmit="return confirm('{{ __('Are you sure you want to revoke this API Token?') }}');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-800 font-bold hover:underline cursor-pointer">
                                                {{ __('Revoke') }}
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="py-6 text-center text-slate-400 font-medium">
                                        {{ __('No institutional API Tokens created yet. Click the + Generate Token button to create a new token.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>

        <!-- Right Side Panel: Security Score & Active Sessions (Spans 4 Cols) -->
        <div class="lg:col-span-4 space-y-4">
            
            <!-- Security Score Card -->
            <div class="rounded-2xl border border-emerald-200 dark:border-emerald-900/50 bg-emerald-50/40 dark:bg-emerald-950/30 p-5 shadow-xs space-y-3">
                <span class="text-[10px] font-bold text-emerald-800 dark:text-emerald-300 uppercase tracking-wider block">{{ __('SECURITY SCORE') }}</span>
                
                <div class="flex items-baseline gap-2">
                    <span class="text-3xl font-black text-emerald-700 dark:text-emerald-400">{{ __n(Auth::user()->google2fa_enabled ? '92' : '75') }}</span>
                    <span class="text-xs font-bold text-slate-400">/ {{ __n('100') }}</span>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-700 text-white ml-auto">
                        {{ Auth::user()->google2fa_enabled ? __('Strong Protection') : __('Good Protection') }}
                    </span>
                </div>

                <div class="space-y-2 text-xs border-t border-emerald-200/60 dark:border-emerald-900/50 pt-3">
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">{{ __('Password Strength') }}</span>
                        <span class="font-bold text-emerald-800 dark:text-emerald-300">{{ __('Strong') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-slate-600 dark:text-slate-400">{{ __('2FA Setup') }}</span>
                        <span class="font-bold {{ Auth::user()->google2fa_enabled ? 'text-emerald-800 dark:text-emerald-300' : 'text-amber-700 dark:text-amber-400' }}">
                            {{ Auth::user()->google2fa_enabled ? __('Enabled') : __('Disabled') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Active Sessions Card -->
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs space-y-3">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block">{{ __('Active Sessions') }}</span>

                <div class="space-y-3 text-xs">
                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs">Mac OS • Safari</span>
                            <span class="text-[10px] text-slate-400 font-medium block">Jakarta, ID • 182.1.22.4</span>
                        </div>
                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 dark:bg-emerald-950/60 text-emerald-800 dark:text-emerald-300 border border-emerald-200 dark:border-emerald-900/50">{{ __('CURRENT') }}</span>
                    </div>

                    <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 flex items-center justify-between opacity-70">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block text-xs">iOS • LendFlow App</span>
                            <span class="text-[10px] text-slate-400 font-medium block">{{ __('Active') }} {{ __n('2') }} {{ __('hours ago') }}</span>
                        </div>
                        <button type="button" @click="revokeSessionsModalOpen = true" class="text-[10px] font-bold text-rose-600 dark:text-rose-400 hover:underline cursor-pointer">{{ __('Revoke') }}</button>
                    </div>
                </div>

                <button type="button" @click="revokeSessionsModalOpen = true" class="w-full py-2 rounded-xl border border-slate-200 dark:border-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-50 dark:hover:bg-slate-800/60 transition-colors cursor-pointer">
                    {{ __('Sign out all other sessions') }}
                </button>
            </div>

        </div>

    </div>

    <!-- ─── Tab 3: Notification Preferences ──────────────────────────────── -->
    <div x-show="profileTab === 'notifications'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-2">{{ __('Notification Preferences') }}</h3>
            <p class="text-xs text-slate-500 font-medium mt-1">{{ __('Manage how LendFlow communicates important updates, alerts, and marketing information to you.') }}</p>
        </div>

        <form action="{{ route('profile.notifications.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @php
                $settings = $user->profile?->notification_settings ?? [
                    'security_email'   => true,
                    'security_push'    => true,
                    'financial_email'  => true,
                    'financial_push'   => true,
                    'investment_email' => true,
                    'investment_push'  => false,
                ];
            @endphp

            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-800 pb-2">{{ __('Security Alerts') }}</h4>
                <div class="flex items-center justify-between text-xs">
                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Unrecognized Logins & Password Changes') }}</span>
                    <div class="flex gap-4">
                        <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                            <input type="checkbox" name="security_email" value="1" {{ !empty($settings['security_email']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Email') }}
                        </label>
                        <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                            <input type="checkbox" name="security_push" value="1" {{ !empty($settings['security_push']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Push') }}
                        </label>
                    </div>
                </div>
            </div>

            <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-800 pb-2">{{ __('Financial Activity') }}</h4>
                <div class="space-y-3 text-xs">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60 dark:border-slate-800">
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Loan Approvals & Updates') }}</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                                <input type="checkbox" name="financial_email" value="1" {{ !empty($settings['financial_email']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Email') }}
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                                <input type="checkbox" name="financial_push" value="1" {{ !empty($settings['financial_push']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Push') }}
                            </label>
                        </div>
                    </div>
                    <div class="flex items-center justify-between py-1">
                        <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Investment Milestones & Returns') }}</span>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                                <input type="checkbox" name="investment_email" value="1" {{ !empty($settings['investment_email']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Email') }}
                            </label>
                            <label class="flex items-center gap-1.5 text-slate-600 dark:text-slate-400 font-semibold cursor-pointer">
                                <input type="checkbox" name="investment_push" value="1" {{ !empty($settings['investment_push']) ? 'checked' : '' }} class="accent-emerald-700"> {{ __('Push') }}
                            </label>
                        </div>
                    </div>
                </div>
            </div>

            <button type="submit" id="btn_save_notification_preferences" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                {{ __('Save Preferences') }} &rarr;
            </button>
        </form>
    </div>

    <!-- ─── Tab 4: System Preferences & Appearance ───────────────────────── -->
    <div x-show="profileTab === 'system'" class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 sm:p-8 shadow-xs space-y-6" style="display: none;">
        <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-slate-100 border-b border-slate-100 dark:border-slate-800 pb-2">{{ __('System Preferences') }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-1">{{ __('Manage your global application settings, appearance, and privacy controls.') }}</p>
        </div>

        <form action="{{ route('profile.system.update') }}" method="POST" class="space-y-6">
            @csrf
            @method('PUT')

            @php
                $sysSettings = $user->profile?->system_preferences ?? [
                    'color_theme'               => 'light',
                    'data_density'              => 'comfortable',
                    'public_profile'            => true,
                    'data_sharing'              => false,
                    'third_party_integrations' => true,
                ];
            @endphp

            <!-- Appearance Settings -->
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-6">
                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-800 pb-2">{{ __('Appearance') }}</h4>

                <div class="space-y-6">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Color Theme') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label @click="colorTheme = 'light'; applyTheme('light')"
                                   :class="colorTheme === 'light' 
                                       ? 'border-2 border-emerald-600 dark:border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 shadow-xs' 
                                       : 'border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:border-slate-300 dark:hover:border-slate-700'"
                                   class="p-3.5 rounded-xl flex items-center justify-between cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-500 border border-amber-200/80 dark:border-amber-900/50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m0 13.5V21m8.966-8.966h-2.25m-13.5 0h-2.25m15.364-6.364l-1.591 1.591M6.758 17.242l-1.591 1.591m12.728 0l-1.591-1.591M6.758 6.758L5.167 5.167M12 8.25a3.75 3.75 0 100 7.5 3.75 3.75 0 000-7.5z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ __('Light Theme') }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ __('Clean bright layout.') }}</span>
                                    </div>
                                </div>
                                <input type="radio" id="radio_theme_light" name="color_theme" value="light" {{ ($sysSettings['color_theme'] ?? 'light') === 'light' ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                            </label>

                            <label @click="colorTheme = 'dark'; applyTheme('dark')"
                                   :class="colorTheme === 'dark' 
                                       ? 'border-2 border-emerald-600 dark:border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 shadow-xs' 
                                       : 'border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:border-slate-300 dark:hover:border-slate-700'"
                                   class="p-3.5 rounded-xl flex items-center justify-between cursor-pointer transition-all select-none">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-indigo-500 border border-indigo-200/80 dark:border-indigo-900/50">
                                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.718 9.718 0 0118 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 003 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 009.002-5.998z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ __('Dark Theme') }}</span>
                                        <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ __('Sleek dark mode for low light.') }}</span>
                                    </div>
                                </div>
                                <input type="radio" id="radio_theme_dark" name="color_theme" value="dark" {{ ($sysSettings['color_theme'] ?? 'light') === 'dark' ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                            </label>
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Data Density') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <label @click="density = 'comfortable'; applyDensity('comfortable')"
                                   :class="density === 'comfortable' 
                                       ? 'border-2 border-emerald-600 dark:border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 shadow-xs' 
                                       : 'border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:border-slate-300 dark:hover:border-slate-700'"
                                   class="p-3.5 rounded-xl flex items-center justify-between cursor-pointer transition-all select-none">
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ __('Comfortable') }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ __('More whitespace, easier to read.') }}</span>
                                </div>
                                <input type="radio" id="radio_density_comfortable" name="data_density" value="comfortable" @change="density = 'comfortable'; applyDensity('comfortable')" {{ ($sysSettings['data_density'] ?? 'comfortable') === 'comfortable' ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                            </label>

                            <label @click="density = 'compact'; applyDensity('compact')"
                                   :class="density === 'compact' 
                                       ? 'border-2 border-emerald-600 dark:border-emerald-500 bg-emerald-50/50 dark:bg-emerald-950/40 shadow-xs' 
                                       : 'border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900/60 hover:border-slate-300 dark:hover:border-slate-700'"
                                   class="p-3.5 rounded-xl flex items-center justify-between cursor-pointer transition-all select-none">
                                <div>
                                    <span class="font-bold text-slate-900 dark:text-slate-100 text-xs block">{{ __('Compact') }}</span>
                                    <span class="text-[10px] text-slate-500 dark:text-slate-400 font-medium">{{ __('High information density for trading.') }}</span>
                                </div>
                                <input type="radio" id="radio_density_compact" name="data_density" value="compact" @change="density = 'compact'; applyDensity('compact')" {{ ($sysSettings['data_density'] ?? 'comfortable') === 'compact' ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                            </label>
                        </div>
                    </div>

                    <!-- Language Preference Dropdown -->
                    <div>
                        <label for="language_select" class="block text-[11px] font-bold text-slate-500 dark:text-slate-400 uppercase tracking-wider mb-2">{{ __('Language Preference') }}</label>
                        <select id="language_select" @change="const u = new URL(window.location.href); u.searchParams.set('tab', profileTab); window.location.href='/lang/' + $event.target.value + '?redirect=' + encodeURIComponent(u.toString())" class="w-full sm:w-80 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100 px-3.5 py-2.5 text-xs font-semibold outline-none focus:border-emerald-600 focus:ring-2 focus:ring-emerald-600/20 shadow-xs cursor-pointer">
                            <option value="id" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100" {{ app()->getLocale() === 'id' ? 'selected' : '' }}>Bahasa Indonesia (ID)</option>
                            <option value="en" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100" {{ app()->getLocale() === 'en' ? 'selected' : '' }}>English (EN)</option>
                            <option value="es" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100" {{ app()->getLocale() === 'es' ? 'selected' : '' }}>Español (ES)</option>
                            <option value="ar" class="bg-white dark:bg-slate-900 text-slate-900 dark:text-slate-100" {{ app()->getLocale() === 'ar' ? 'selected' : '' }}>العربية (Arabic)</option>
                        </select>
                        <p class="text-[10px] text-slate-500 dark:text-slate-400 font-medium mt-1.5">{{ __('Select your preferred system interface language across all pages.') }}</p>
                    </div>
                </div>
            </div>

            <!-- Privacy & Data -->
            <div class="p-6 rounded-2xl bg-slate-50 dark:bg-slate-950/60 border border-slate-200 dark:border-slate-800 space-y-4">
                <h4 class="text-xs font-bold text-slate-900 dark:text-slate-100 border-b border-slate-200 dark:border-slate-800 pb-2">{{ __('Privacy & Data Controls') }}</h4>

                <div class="space-y-3 text-xs font-medium">
                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60 dark:border-slate-800">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ __('Public Profile Visibility') }}</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ __('Allow other institutional members to discover your profile in the directory.') }}</span>
                        </div>
                        <input type="checkbox" name="public_profile" value="1" {{ !empty($sysSettings['public_profile']) ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between py-1 border-b border-slate-200/60 dark:border-slate-800">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ __('Data Sharing for Analytics') }}</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ __('Share anonymized usage data to help us improve platform performance.') }}</span>
                        </div>
                        <input type="checkbox" name="data_sharing" value="1" {{ !empty($sysSettings['data_sharing']) ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between py-1">
                        <div>
                            <span class="font-bold text-slate-800 dark:text-slate-200 block">{{ __('Third-Party Integrations') }}</span>
                            <span class="text-[10px] text-slate-500 dark:text-slate-400">{{ __('Allow connected API apps to read basic profile information.') }}</span>
                        </div>
                        <input type="checkbox" name="third_party_integrations" value="1" {{ !empty($sysSettings['third_party_integrations']) ? 'checked' : '' }} class="accent-emerald-700 h-4 w-4">
                    </div>
                </div>
            </div>

            <div class="flex gap-3">
                <button type="submit" id="btn_save_system_preferences" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs cursor-pointer">
                    {{ __('Save Changes') }} &rarr;
                </button>
            </div>
        </form>
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
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">Konfirmasi Nonaktifkan 2FA</h3>
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

    <!-- Generate API Token Modal -->
    <div x-show="generateTokenModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" 
         style="display: none;">
        
        <div @click.away="generateTokenModalOpen = false" 
             class="w-full max-w-md bg-white rounded-2xl p-6 shadow-xl border border-slate-200 space-y-5 transform transition-all text-left">
            
            <div class="flex items-center justify-between border-b border-slate-100 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">Generate New API Token</h3>
                <button type="button" @click="generateTokenModalOpen = false" class="text-slate-400 hover:text-slate-600 p-1">
                    &times;
                </button>
            </div>

            <form action="{{ route('profile.tokens.store') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="token_name" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">Token Name <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="token_name" required placeholder="Contoh: Trading Bot Node 1" 
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2.5 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-2">Permissions / Hak Akses <span class="text-rose-500">*</span></label>
                    <div class="space-y-2 text-xs">
                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="permissions" value="write" checked class="accent-emerald-700 h-4 w-4">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">Read / Write</span>
                                <span class="text-[11px] text-slate-500 font-medium">Akses penuh untuk membaca data &amp; mengeksekusi transaksi P2P.</span>
                            </div>
                        </label>

                        <label class="flex items-center gap-2.5 p-3 rounded-xl border border-slate-200 cursor-pointer hover:bg-slate-50">
                            <input type="radio" name="permissions" value="read" class="accent-emerald-700 h-4 w-4">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">Read Only</span>
                                <span class="text-[11px] text-slate-500 font-medium">Akses terbatas hanya untuk membaca saldo, portofolio &amp; marketplace.</span>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="flex items-center gap-3 pt-3">
                    <button type="button" @click="generateTokenModalOpen = false" 
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 bg-white text-xs font-bold text-slate-700 hover:bg-slate-50 transition-colors shadow-xs cursor-pointer">
                        Batal
                    </button>
                    <button type="submit" id="btn_submit_generate_token" 
                            class="flex-1 py-2.5 rounded-xl bg-emerald-700 hover:bg-emerald-800 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        Buat Token Baru &rarr;
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Revoke Other Sessions Modal -->
    <div x-show="revokeSessionsModalOpen" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/60 backdrop-blur-xs p-4" 
         style="display: none;">
        
        <div @click.away="revokeSessionsModalOpen = false" 
             class="w-full max-w-md bg-white dark:bg-slate-900 rounded-2xl p-6 shadow-xl border border-slate-200 dark:border-slate-800 space-y-5 transform transition-all text-left">
            
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">{{ __('Confirm Session Revocation') }}</h3>
                <button type="button" @click="revokeSessionsModalOpen = false" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 p-1 cursor-pointer">
                    &times;
                </button>
            </div>

            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium leading-relaxed">
                {{ __('Please enter your password to confirm signing out all other browser sessions across your devices.') }}
            </p>

            <form action="{{ route('profile.sessions.revoke-others') }}" method="POST" class="space-y-4">
                @csrf

                <div>
                    <label for="session_password" class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('CURRENT PASSWORD') }} <span class="text-rose-500">*</span></label>
                    <input type="password" name="password" id="session_password" required placeholder="••••••••" 
                           class="w-full rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 px-3.5 py-2.5 text-xs font-semibold text-slate-800 dark:text-slate-100 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600 shadow-xs">
                    @error('session_password')
                        <p class="text-[11px] font-bold text-rose-600 mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-3 pt-2">
                    <button type="button" @click="revokeSessionsModalOpen = false" 
                            class="flex-1 py-2.5 rounded-xl border border-slate-200 dark:border-slate-700 bg-white dark:bg-slate-900 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-50 dark:hover:bg-slate-800 transition-colors shadow-xs cursor-pointer">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" id="btn_confirm_revoke_sessions" 
                            class="flex-1 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-bold text-xs shadow-xs transition-colors cursor-pointer">
                        {{ __('Sign Out All Sessions') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
