@extends('layouts.admin')

@section('title', __('Financial Configuration') . ' - Admin Terminal')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">
    
    {{-- Flash Notifications --}}
    @if(session('success'))
        <div class="flex items-center gap-3 rounded-2xl bg-emerald-500/10 border border-emerald-500/30 px-4 py-3 text-sm font-semibold text-emerald-600 dark:text-emerald-400">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
            </svg>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="flex items-center gap-3 rounded-2xl bg-rose-500/10 border border-rose-500/30 px-4 py-3 text-sm font-semibold text-rose-600 dark:text-rose-400">
            <svg class="w-5 h-5 shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z" />
            </svg>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if($errors->any())
        <div class="rounded-2xl bg-rose-500/10 border border-rose-500/30 p-4 text-xs text-rose-600 dark:text-rose-400 space-y-1">
            <span class="font-bold">{{ __('Please fix the following validation errors:') }}</span>
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $err)
                    <li>{{ $err }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
    </div>

    {{-- ─── 2-Column Grid ────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        {{-- Left: Interest Rate Brackets (Spans 7 Cols) --}}
        <div class="lg:col-span-7 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
            <form method="POST" action="{{ route('admin.financials.updateRates') }}" class="space-y-4">
                @csrf
                <div class="flex items-center justify-between border-b border-slate-100 dark:border-slate-800 pb-3">
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                        {{ __('Interest Rate Brackets') }}
                    </h3>
                    <span class="text-[11px] font-semibold text-emerald-600 dark:text-emerald-400 bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                        {{ __('Active Rule Set') }}
                    </span>
                </div>
                
                <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">
                    {{ __('Configure min and max annual interest rates per borrower credit risk grade.') }}
                </p>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                                <th class="py-3 px-3">{{ __('GRADE') }}</th>
                                <th class="py-3 px-3">{{ __('MIN RATE (%)') }}</th>
                                <th class="py-3 px-3">{{ __('MAX RATE (%)') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-medium text-slate-700 dark:text-slate-300">
                            @php
                                $grades = [
                                    'A' => ['label' => __('Grade A'), 'class' => 'text-emerald-600 dark:text-emerald-400', 'default_min' => '8.00', 'default_max' => '10.00'],
                                    'B' => ['label' => __('Grade B'), 'class' => 'text-blue-600 dark:text-blue-400', 'default_min' => '11.00', 'default_max' => '14.00'],
                                    'C' => ['label' => __('Grade C'), 'class' => 'text-amber-600 dark:text-amber-400', 'default_min' => '15.00', 'default_max' => '18.00'],
                                    'D' => ['label' => __('Grade D'), 'class' => 'text-rose-600 dark:text-rose-400', 'default_min' => '19.00', 'default_max' => '24.00'],
                                ];
                            @endphp
                            @foreach($grades as $gradeKey => $meta)
                                @php
                                    $rateObj = $interestRates->get($gradeKey);
                                    $minVal = old('grade_' . strtolower($gradeKey) . '_min', $rateObj ? $rateObj->min_rate : $meta['default_min']);
                                    $maxVal = old('grade_' . strtolower($gradeKey) . '_max', $rateObj ? $rateObj->max_rate : $meta['default_max']);
                                @endphp
                                <tr>
                                    <td class="py-3 px-3 font-extrabold {{ $meta['class'] }}">{{ $meta['label'] }}</td>
                                    <td class="py-3 px-3">
                                        <input type="number" step="0.01" name="grade_{{ strtolower($gradeKey) }}_min" value="{{ $minVal }}" class="w-24 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                                    </td>
                                    <td class="py-3 px-3">
                                        <input type="number" step="0.01" name="grade_{{ strtolower($gradeKey) }}_max" value="{{ $maxVal }}" class="w-24 rounded-lg border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-2 py-1 text-xs text-center font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="pt-2 flex items-center justify-end">
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs cursor-pointer">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99" />
                        </svg>
                        <span>{{ __('Update Rates') }}</span>
                    </button>
                </div>
            </form>
        </div>

        {{-- Right: Fee Schedule & Currency Management (Spans 5 Cols) --}}
        <div class="lg:col-span-5 space-y-6">
            
            {{-- Fee Schedule --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <form method="POST" action="{{ route('admin.financials.updateFees') }}" class="space-y-4">
                    @csrf
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                        {{ __('Fee Schedule') }}
                    </h3>

                    @php
                        $originationFee = old('origination_fee', $feeConfigs->has('origination_fee') ? (string) (float) $feeConfigs->get('origination_fee')->value : '1.50');
                        $serviceFee     = old('service_fee', $feeConfigs->has('service_fee') ? (string) (float) $feeConfigs->get('service_fee')->value : '6500');
                        $penaltyRate    = old('penalty_rate', $feeConfigs->has('penalty_rate') ? (string) (float) $feeConfigs->get('penalty_rate')->value : '0.10');
                    @endphp

                    <div class="space-y-3 text-xs">
                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                {{ __('Origination Fee (%)') }}
                            </label>
                            <input type="number" step="0.01" name="origination_fee" value="{{ $originationFee }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                {{ __('Monthly Service / Flat Fee (IDR)') }}
                            </label>
                            <input type="number" step="1" name="service_fee" value="{{ $serviceFee }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                        </div>

                        <div>
                            <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                                {{ __('Late Payment Penalty Rate (%)') }}
                            </label>
                            <input type="number" step="0.01" name="penalty_rate" value="{{ $penaltyRate }}" class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-bold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>{{ __('Save Fee Schedule') }}</span>
                        </button>
                    </div>
                </form>
            </div>

            {{-- Currency Management --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <form method="POST" action="{{ route('admin.financials.updateCurrencies') }}" class="space-y-4">
                    @csrf
                    <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                        {{ __('Currency Management') }}
                    </h3>

                    <div class="space-y-3 text-xs font-medium">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">IDR Fiat</span>
                                <span class="text-[10px] {{ $currencySettings['idr'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} font-bold">
                                    {{ $currencySettings['idr'] ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                            <input type="checkbox" name="currency_idr" value="1" {{ $currencySettings['idr'] ? 'checked' : '' }} class="accent-emerald-600 h-4 w-4 rounded cursor-pointer">
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">Tether USD (USDT)</span>
                                <span class="text-[10px] {{ $currencySettings['usdt'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} font-bold">
                                    {{ $currencySettings['usdt'] ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                            <input type="checkbox" name="currency_usdt" value="1" {{ $currencySettings['usdt'] ? 'checked' : '' }} class="accent-emerald-600 h-4 w-4 rounded cursor-pointer">
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">Ethereum (ETH)</span>
                                <span class="text-[10px] {{ $currencySettings['eth'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} font-bold">
                                    {{ $currencySettings['eth'] ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                            <input type="checkbox" name="currency_eth" value="1" {{ $currencySettings['eth'] ? 'checked' : '' }} class="accent-emerald-600 h-4 w-4 rounded cursor-pointer">
                        </div>

                        <div class="flex items-center justify-between p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">Bitcoin (BTC)</span>
                                <span class="text-[10px] {{ $currencySettings['btc'] ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }} font-bold">
                                    {{ $currencySettings['btc'] ? __('Active') : __('Inactive') }}
                                </span>
                            </div>
                            <input type="checkbox" name="currency_btc" value="1" {{ $currencySettings['btc'] ? 'checked' : '' }} class="accent-emerald-600 h-4 w-4 rounded cursor-pointer">
                        </div>
                    </div>

                    <div class="pt-2 flex items-center justify-end">
                        <button type="submit" 
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs cursor-pointer">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                            </svg>
                            <span>{{ __('Save Currencies') }}</span>
                        </button>
                    </div>
                </form>
            </div>

        </div>

    </div>

</div>
@endsection
