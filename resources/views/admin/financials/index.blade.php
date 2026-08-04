@extends('layouts.admin')

@section('title', __('Financial Configuration') . ' - Admin Terminal')

@section('content')
<div x-data="{ 
    showUpdateRatesModal: false, 
    showSaveConfigModal: false,
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
                {{ __('Financial Configuration') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Manage global interest rate brackets, platform fee schedules, and settlement currency toggles.') }}
            </p>
        </div>
        
        <button type="button" 
                @click="showSaveConfigModal = true" 
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
            </svg>
            <span>{{ __('Save Financial Config') }}</span>
        </button>
    </div>

    {{-- ─── 2-Column Grid ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left: Interest Rate Brackets (Spans 7 Cols) --}}
        <div class="lg:col-span-7 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
            <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                    {{ __('Interest Rate Brackets') }}
                </h3>
                <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                    {{ __('Active Rule Set') }}
                </span>
            </div>
            
            <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                {{ __('Configure base interest rates and risk premium add-ons per borrower credit grade.') }}
            </p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                            <th class="py-3 px-3">{{ __('GRADE') }}</th>
                            <th class="py-3 px-3">{{ __('BASE RATE (%)') }}</th>
                            <th class="py-3 px-3">{{ __('RISK PREMIUM (%)') }}</th>
                            <th class="py-3 px-3 text-right">{{ __('TOTAL TARGET (%)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-emerald-600 dark:text-emerald-400">Grade A</td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="3.50" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="1.00" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900 dark:text-slate-100">4.50%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-blue-600 dark:text-blue-400">Grade B</td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="4.25" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="2.50" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900 dark:text-slate-100">6.75%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-amber-600 dark:text-amber-400">Grade C</td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="5.50" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="4.00" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900 dark:text-slate-100">9.50%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-rose-600 dark:text-rose-400">Grade D</td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="7.00" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3">
                                <input type="number" step="0.1" value="6.50" class="w-20 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                            </td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900 dark:text-slate-100">13.50%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-2 flex items-center justify-end">
                <button type="button" 
                        @click="showUpdateRatesModal = true" 
                        class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                    </svg>
                    <span>{{ __('Update Rates') }}</span>
                </button>
            </div>
        </div>

        {{-- Right: Fee Schedule & Currency Management (Spans 5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Fee Schedule --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Fee Schedule') }}
                </h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                            {{ __('Origination Fee (%)') }}
                        </label>
                        <input type="number" step="0.1" value="2.00" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                            {{ __('Monthly Service Fee ($)') }}
                        </label>
                        <input type="number" value="15.00" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                            {{ __('Late Payment Penalty (%)') }}
                        </label>
                        <input type="number" step="0.1" value="5.00" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                    </div>
                </div>
            </div>

            {{-- Currency Management --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Currency Management') }}
                </h3>

                <div class="space-y-3 text-xs font-medium">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 block">IDR Fiat</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 block">Tether USD (USDT)</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 block">Ethereum (ETH)</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                        <div>
                            <span class="font-bold text-slate-900 dark:text-slate-100 block">Bitcoin (BTC)</span>
                            <span class="text-[10px] text-emerald-600 dark:text-emerald-400 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-600 h-4 w-4 rounded">
                    </div>
                </div>
            </div>

        </div>

    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 1: CONFIRM UPDATE RATES --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showUpdateRatesModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showUpdateRatesModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showUpdateRatesModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500/15 text-amber-500 flex items-center justify-center shrink-0 border border-amber-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Confirm Interest Rates Update') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Update base interest rates & risk premiums') }}
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs space-y-2">
                <p class="text-slate-600 dark:text-slate-300 font-medium">
                    {{ __('Are you sure you want to update the interest rate brackets for all borrower credit grades?') }}
                </p>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-700/60 grid grid-cols-2 gap-2 font-bold text-[11px]">
                    <div class="text-emerald-600 dark:text-emerald-400">Grade A: 4.50% Total</div>
                    <div class="text-blue-600 dark:text-blue-400">Grade B: 6.75% Total</div>
                    <div class="text-amber-600 dark:text-amber-400">Grade C: 9.50% Total</div>
                    <div class="text-rose-600 dark:text-rose-400">Grade D: 13.50% Total</div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showUpdateRatesModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="showUpdateRatesModal = false; triggerToast(__('Interest rates updated successfully!'));" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs">
                    {{ __('Confirm & Update Rates') }}
                </button>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL 2: CONFIRM SAVE FINANCIAL CONFIG --}}
    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    <div x-show="showSaveConfigModal" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/70 backdrop-blur-xs"
         style="display: none;">
        
        <div x-show="showSaveConfigModal"
             x-transition:enter="transition ease-out duration-200 transform"
             x-transition:enter-start="opacity-0 scale-95"
             x-transition:enter-end="opacity-100 scale-100"
             x-transition:leave="transition ease-in duration-150 transform"
             x-transition:leave-start="opacity-100 scale-100"
             x-transition:leave-end="opacity-0 scale-95"
             @click.away="showSaveConfigModal = false"
             class="w-full max-w-md rounded-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 p-6 shadow-2xl space-y-4">
            
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-emerald-500/15 text-emerald-500 flex items-center justify-center shrink-0 border border-emerald-500/30">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6z" />
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5z" />
                    </svg>
                </div>
                <div>
                    <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">
                        {{ __('Confirm Save Financial Config') }}
                    </h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                        {{ __('Publish global fee schedules & currency settings') }}
                    </p>
                </div>
            </div>

            <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 text-xs space-y-2">
                <p class="text-slate-600 dark:text-slate-300 font-medium">
                    {{ __('Are you sure you want to save and publish these global financial configuration settings? This will immediately take effect for all platform loans and repayments.') }}
                </p>
                <div class="pt-2 border-t border-slate-200 dark:border-slate-700/60 space-y-1 text-[11px] font-semibold text-slate-700 dark:text-slate-300">
                    <div class="flex justify-between">
                        <span>Origination Fee:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">2.00%</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Monthly Service Fee:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">$15.00</span>
                    </div>
                    <div class="flex justify-between">
                        <span>Late Payment Penalty:</span>
                        <span class="font-bold text-emerald-600 dark:text-emerald-400">5.00%</span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" 
                        @click="showSaveConfigModal = false" 
                        class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                    {{ __('Cancel') }}
                </button>
                <button type="button" 
                        @click="showSaveConfigModal = false; triggerToast(__('Financial configuration saved successfully!'));" 
                        class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs">
                    {{ __('Confirm & Save Config') }}
                </button>
            </div>
        </div>
    </div>

</div>
@endsection
