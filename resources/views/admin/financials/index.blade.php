@extends('layouts.app')

@section('title', __('Financial Configuration') . ' - Admin Terminal')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Financial Configuration') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Manage global interest rate brackets, platform fee schedules, and settlement currency toggles.') }}</p>
        </div>
        <button type="button" @click="alert('Financial configuration saved!')" class="py-2 px-5 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
            {{ __('Save Financial Config') }}
        </button>
    </div>

    <!-- 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left: Interest Rate Brackets (Spans 7 Cols) -->
        <div class="lg:col-span-7 rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Interest Rate Brackets') }}</h3>
            <p class="text-xs text-slate-500 font-medium">{{ __('Configure base interest rates and risk premium add-ons per borrower credit grade.') }}</p>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase">
                            <th class="py-2.5 px-3">{{ __('GRADE') }}</th>
                            <th class="py-2.5 px-3">{{ __('BASE RATE (%)') }}</th>
                            <th class="py-2.5 px-3">{{ __('RISK PREMIUM (%)') }}</th>
                            <th class="py-2.5 px-3 text-right">{{ __('TOTAL TARGET (%)') }}</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium text-slate-700">
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-emerald-800">Grade A</td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="3.50" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="1.00" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">{{ __n('4.50') }}%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-blue-800">Grade B</td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="4.25" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="2.50" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">{{ __n('6.75') }}%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-amber-800">Grade C</td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="5.50" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="4.00" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">{{ __n('9.50') }}%</td>
                        </tr>
                        <tr>
                            <td class="py-3 px-3 font-extrabold text-rose-800">Grade D</td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="7.00" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3"><input type="number" step="0.1" value="6.50" class="w-20 rounded-lg border border-slate-200 px-2 py-1 text-xs text-center font-bold"></td>
                            <td class="py-3 px-3 text-right font-extrabold text-slate-900">{{ __n('13.50') }}%</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="pt-2">
                <button type="button" @click="alert('Rates updated!')" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800">
                    {{ __('Update Rates') }}
                </button>
            </div>
        </div>

        <!-- Right: Fee Schedule & Currency Management (Spans 5 Cols) -->
        <div class="lg:col-span-5 space-y-6">
            
            <!-- Fee Schedule -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Fee Schedule') }}</h3>

                <div class="space-y-3 text-xs">
                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Origination Fee (%)') }}</label>
                        <input type="number" step="0.1" value="2.00" class="w-full rounded-xl border border-slate-200 px-3 py-2 font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Monthly Service Fee ($)') }}</label>
                        <input type="number" value="15.00" class="w-full rounded-xl border border-slate-200 px-3 py-2 font-bold text-slate-800">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 uppercase tracking-wider mb-1">{{ __('Late Payment Penalty (%)') }}</label>
                        <input type="number" step="0.1" value="5.00" class="w-full rounded-xl border border-slate-200 px-3 py-2 font-bold text-slate-800">
                    </div>
                </div>
            </div>

            <!-- Currency Management -->
            <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Currency Management') }}</h3>

                <div class="space-y-3 text-xs font-medium">
                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-900 block">USD Fiat</span>
                            <span class="text-[10px] text-emerald-700 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-900 block">USD Coin (USDC)</span>
                            <span class="text-[10px] text-emerald-700 font-bold">{{ __('Active') }}</span>
                        </div>
                        <input type="checkbox" checked class="accent-emerald-700 h-4 w-4">
                    </div>

                    <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 border border-slate-200">
                        <div>
                            <span class="font-bold text-slate-900 block">Bitcoin (BTC)</span>
                            <span class="text-[10px] text-slate-400 font-bold">{{ __('Inactive') }}</span>
                        </div>
                        <input type="checkbox" class="accent-emerald-700 h-4 w-4">
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
