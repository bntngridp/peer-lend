@extends('layouts.admin')

@section('title', __('Role Management') . ' - Admin Terminal')

@section('content')
<div x-data="{ 
    showNewRoleModal: false, 
    showSaveMatrixModal: false,
    newRoleName: '',
    newRoleDescription: '',
    newRoleAccess: 'custom',
    toastMessage: '',
    showToast: false,
    triggerToast(msg) {
        this.toastMessage = msg;
        this.showToast = true;
        setTimeout(() => { this.showToast = false; }, 4000);
    }
}" class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">
    
    {{-- ─── Toast Notification Popup ────────────────────────────────────────── --}}
    <div x-show="showToast" 
         x-transition:enter="transition ease-out duration-300 transform"
         x-transition:enter-start="opacity-0 translate-y-2 scale-95"
         x-transition:enter-end="opacity-100 translate-y-0 scale-100"
         x-transition:leave="transition ease-in duration-200 transform"
         x-transition:leave-start="opacity-100 translate-y-0 scale-100"
         x-transition:leave-end="opacity-0 translate-y-2 scale-95"
         class="fixed top-20 right-6 z-50 flex items-center gap-3 rounded-2xl bg-slate-900 dark:bg-slate-800 border border-slate-700 dark:border-slate-600 px-4 py-3 text-sm font-semibold text-white shadow-2xl"
         style="display: none;">
        <div class="h-8 w-8 rounded-xl bg-emerald-500/20 text-emerald-400 flex items-center justify-center shrink-0">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
        </div>
        <span x-text="toastMessage"></span>
        <button type="button" @click="showToast = false" class="ml-2 text-slate-400 hover:text-white">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
            </svg>
        </button>
    </div>

    {{-- ─── Header ──────────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ __('Role Management') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Configure system access levels, operational permissions, and risk controls across admin roles.') }}
            </p>
        </div>
        
        <button type="button" 
                @click="showNewRoleModal = true" 
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>{{ __('+ New Role') }}</span>
        </button>
    </div>

    {{-- ─── Permission Matrix Card ────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs dark:shadow-none space-y-4 p-6">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-4">
            <div>
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                    {{ __('Operational Permissions Matrix') }}
                </h3>
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium mt-0.5">
                    {{ __('Control capability boundaries and module access tiers for internal staff.') }}
                </p>
            </div>
            
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300 shrink-0">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ __n('5') }} {{ __('Configured Roles') }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                        <th class="py-3 px-4">{{ __('ROLE / TIER') }}</th>
                        <th class="py-3 px-4">{{ __('USER MGMT') }}</th>
                        <th class="py-3 px-4">{{ __('LOAN APPROVAL') }}</th>
                        <th class="py-3 px-4">{{ __('FINANCIAL CONFIG') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('SYSTEM LOGS') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-800 dark:text-slate-200">
                    
                    {{-- Super Admin --}}
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-4 font-extrabold">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ __('Super Admin') }}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">{{ __('Unrestricted system access.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                        <td class="py-4 px-4 text-center text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1 justify-center"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                    </tr>

                    {{-- Customer Service --}}
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-4 font-extrabold">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ __('Customer Service') }}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">{{ __('User onboarding & KYC verification.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>{{ __('Review Only') }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-400 dark:text-slate-500 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-center text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                    </tr>

                    {{-- Risk Officer --}}
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-4 font-extrabold">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ __('Risk Officer') }}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">{{ __('Credit decisioning & risk monitoring.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-400 dark:text-slate-500 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                        <td class="py-4 px-4 text-center text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                    </tr>

                    {{-- Collection Officer --}}
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-4 font-extrabold">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ __('Collection Officer') }}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">{{ __('Repayment tracking & loan liquidation.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                        <td class="py-4 px-4 text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Disburse Only') }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-400 dark:text-slate-500 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-center text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                    </tr>

                    {{-- Compliance Officer --}}
                    <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                        <td class="py-4 px-4 font-extrabold">
                            <span class="text-sm font-bold text-slate-900 dark:text-slate-100 block">{{ __('Compliance Officer') }}</span>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">{{ __('Regulatory oversight & transaction audit.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                        <td class="py-4 px-4 text-amber-600 dark:text-amber-400 font-bold">
                            {{ __('Read Only') }}
                        </td>
                        <td class="py-4 px-4 text-center text-emerald-600 dark:text-emerald-400 font-bold">
                            <span class="inline-flex items-center gap-1 justify-center"><span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>{{ __('Full Access') }}</span>
                        </td>
                    </tr>

                </tbody>
            </table>
        </div>

        <div class="pt-4 flex justify-end border-t border-slate-100 dark:border-slate-800">
            <button type="button" 
                    @click="showSaveMatrixModal = true" 
                    class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2.5 text-xs font-bold transition-colors shadow-xs">
                <span>{{ __('Save Matrix Changes') }}</span>
                <span>&rarr;</span>
            </button>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1: CONFIRM NEW ROLE --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showNewRoleModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showNewRoleModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showNewRoleModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-purple-500/15 text-purple-400 flex items-center justify-center shrink-0 border border-purple-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M18 18.72a9.094 9.094 0 0 0 3.741-.479 3 3 0 0 0-4.682-2.72m.94 3.198.001.031c0 .225-.012.447-.037.666A11.944 11.944 0 0 1 12 21c-2.17 0-4.207-.576-5.963-1.584A6.062 6.062 0 0 1 6 18.719m12 0a5.971 5.971 0 0 0-.941-3.197m0 0A5.995 5.995 0 0 0 12 12.75a5.995 5.995 0 0 0-5.058 2.772m0 0a3 3 0 0 0-4.681 2.72 8.986 8.986 0 0 0 3.74.477m.94-3.197a5.971 5.971 0 0 0-.94 3.197M15 6.75a3 3 0 1 1-6 0 3 3 0 0 1 6 0zm6 3a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0zm-13.5 0a2.25 2.25 0 1 1-4.5 0 2.25 2.25 0 0 1 4.5 0z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Create New System Role') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Define access tier and permission scope') }}
                    </p>
                </div>
            </div>

            <div class="space-y-3 text-xs">
                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                        {{ __('Role Title') }}
                    </label>
                    <input type="text" 
                           x-model="newRoleName"
                           placeholder="{{ __('e.g. Treasury Officer') }}"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                        {{ __('Role Description') }}
                    </label>
                    <input type="text" 
                           x-model="newRoleDescription"
                           placeholder="{{ __('e.g. Oversees wallet balances and liquidity') }}"
                           class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                </div>

                <div>
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                        {{ __('Permission Template') }}
                    </label>
                    <select x-model="newRoleAccess" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                        <option value="custom">{{ __('Custom Multi-Permission Preset') }}</option>
                        <option value="customer_service">{{ __('Customer Service Base') }}</option>
                        <option value="risk_officer">{{ __('Risk Officer Base') }}</option>
                        <option value="collection_officer">{{ __('Collection Officer Base') }}</option>
                    </select>
                </div>

                {{-- Multiple Access Checkboxes --}}
                <div class="pt-1 space-y-2">
                    <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        {{ __('Module Access Grants (Multiple Access)') }}
                    </label>
                    <div class="grid grid-cols-2 gap-2 text-xs">
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                            <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('User Management') }}</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                            <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Loan Approvals') }}</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                            <input type="checkbox" class="accent-emerald-600 h-4 w-4 rounded">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('Financial Config') }}</span>
                        </label>
                        <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                            <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                            <span class="font-bold text-slate-800 dark:text-slate-200">{{ __('System Audit Logs') }}</span>
                        </label>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showNewRoleModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="showNewRoleModal = false; triggerToast(__('New admin role created successfully!'));" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs">
                    {{ __('Confirm & Create Role') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2: CONFIRM SAVE MATRIX CHANGES --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showSaveMatrixModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showSaveMatrixModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showSaveMatrixModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75m-3-7.036A11.959 11.959 0 013.598 6 11.99 11.99 0 003 9.749c0 5.592 3.824 10.29 9 11.623 5.176-1.332 9-6.03 9-11.622 0-1.31-.21-2.571-.598-3.751h-.152c-3.196 0-6.1-1.248-8.25-3.285z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Confirm Save Matrix Changes') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Publish updated operational permission matrix') }}
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs space-y-2">
                <p class="text-slate-600 dark:text-slate-300 font-medium">
                    {{ __('Are you sure you want to save and publish the updated permissions matrix? Changes will take effect immediately for all active administrative staff accounts.') }}
                </p>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-700/60 space-y-1 text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                    <div class="flex justify-between">
                        <span>{{ __('Affected Roles') }}:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ __n('5') }} {{ __('System Tiers') }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span>{{ __('Permission Audit') }}:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">{{ __('Verified & Signed') }}</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showSaveMatrixModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="showSaveMatrixModal = false; triggerToast(__('Operational permission matrix updated successfully!'));" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs">
                    {{ __('Confirm & Save Matrix') }}
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
