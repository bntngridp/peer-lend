@extends('layouts.admin')

@section('title', __('Review KYC Queue') . ' - Admin Terminal')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto">
    
    {{-- ─── Top Header Bar ─────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ __('KYC Review Queue') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Manage and verify pending institutional and retail client identity applications.') }}
            </p>
        </div>
        <div class="flex items-center gap-3">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3.5 py-1.5 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ __n($kycs->total()) }} {{ __('Total Applications') }}
            </span>
        </div>
    </div>

    {{-- ─── 2 Summary & Filter Cards Row ──────────────────────────────────────── --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        {{-- Card 1: QUEUE OVERVIEW (Spans 4 Cols) --}}
        <div class="lg:col-span-4 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block">
                {{ __('QUEUE OVERVIEW') }}
            </span>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <span class="text-3xl font-black text-slate-900 dark:text-slate-100 block leading-tight">
                        {{ __n($kycs->total()) }}
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block mt-0.5">
                        {{ __('Matching Applications') }}
                    </span>
                </div>
                <div>
                    <span class="text-3xl font-black text-rose-600 dark:text-rose-400 block leading-tight">
                        {{ __n($kycs->getCollection()->filter(fn($k) => $k->isRejected())->count()) }}
                    </span>
                    <span class="text-[11px] font-medium text-slate-500 dark:text-slate-400 block mt-0.5">
                        {{ __('High Risk / Rejected') }}
                    </span>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 dark:border-slate-800 text-[11px] font-semibold text-emerald-600 dark:text-emerald-400">
                &uarr; {{ __('Real-time queue synchronized') }}
            </div>
        </div>

        {{-- Card 2: FILTER PARAMETERS (Spans 8 Cols) --}}
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-5 shadow-xs">
            <span class="text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-3">
                {{ __('FILTER PARAMETERS') }}
            </span>
            
            <form method="GET" action="{{ route('admin.kyc.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                @if(request('search')) <input type="hidden" name="search" value="{{ request('search') }}"> @endif
                {{-- 1. Risk Level --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">
                        {{ __('RISK LEVEL') }}
                    </label>
                    <select name="risk" 
                            onchange="this.form.submit()" 
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="">{{ __('All Risk Levels') }}</option>
                        <option value="high" {{ request('risk') === 'high' ? 'selected' : '' }}>{{ __('High Risk') }}</option>
                        <option value="medium" {{ request('risk') === 'medium' ? 'selected' : '' }}>{{ __('Medium Risk') }}</option>
                        <option value="low" {{ request('risk') === 'low' ? 'selected' : '' }}>{{ __('Low Risk') }}</option>
                    </select>
                </div>

                {{-- 2. Document Type --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">
                        {{ __('DOCUMENT TYPE') }}
                    </label>
                    <select name="type" 
                            onchange="this.form.submit()" 
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="ktp" {{ request('type') === 'ktp' ? 'selected' : '' }}>{{ __('National ID (KTP)') }}</option>
                        <option value="passport" {{ request('type') === 'passport' ? 'selected' : '' }}>{{ __('Passport') }}</option>
                        <option value="sim" {{ request('type') === 'sim' ? 'selected' : '' }}>{{ __('Driver License (SIM)') }}</option>
                    </select>
                </div>

                {{-- 3. Submission Date --}}
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase mb-1">
                        {{ __('SUBMISSION DATE') }}
                    </label>
                    <select name="date" 
                            onchange="this.form.submit()" 
                            class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 cursor-pointer">
                        <option value="">{{ __('All Dates') }}</option>
                        <option value="7" {{ request('date') == '7' ? 'selected' : '' }}>{{ __('Last 7 Days') }}</option>
                        <option value="30" {{ request('date') == '30' ? 'selected' : '' }}>{{ __('Last 30 Days') }}</option>
                        <option value="90" {{ request('date') == '90' ? 'selected' : '' }}>{{ __('Last 90 Days') }}</option>
                    </select>
                </div>

                {{-- Clear Filters Action Button --}}
                <div class="flex items-end">
                    <a href="{{ route('admin.kyc.index') }}" 
                       class="w-full text-center py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        {{ __('Clear Filters') }}
                    </a>
                </div>
            </form>
        </div>

    </div>

    {{-- ─── Application Queue Table Card ────────────────────────────────────── --}}
    <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs dark:shadow-none overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 px-6 py-4 bg-slate-50/50 dark:bg-slate-950/40">
            <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider">
                {{ __('Application Queue') }}
            </h3>
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">
                {{ __('Showing :from-:to of :total', ['from' => __n($kycs->firstItem() ?? 0), 'to' => __n($kycs->lastItem() ?? 0), 'total' => __n($kycs->total())]) }}
            </span>
        </div>

        {{-- Table Search Bar --}}
        <form method="GET" action="{{ route('admin.kyc.index') }}" class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/40">
            @if(request('risk')) <input type="hidden" name="risk" value="{{ request('risk') }}"> @endif
            @if(request('type')) <input type="hidden" name="type" value="{{ request('type') }}"> @endif
            @if(request('date')) <input type="hidden" name="date" value="{{ request('date') }}"> @endif

            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Search name, email, NIK, app ID...') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 pl-9 pr-9 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-500">
                @if(request('search'))
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <a href="{{ route('admin.kyc.index', request()->except('search')) }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="{{ __('Clear search') }}">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3 w-full sm:w-auto">
                <button type="submit" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 text-xs font-bold transition-colors">
                    {{ __('Search') }}
                </button>

                @if(request()->hasAny(['search', 'risk', 'type', 'date']))
                    <a href="{{ route('admin.kyc.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 dark:hover:text-rose-400 underline whitespace-nowrap">
                        {{ __('Reset All') }}
                    </a>
                @endif
            </div>
        </form>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('USER / ENTITY NAME') }}</th>
                        <th class="py-3.5 px-6">{{ __('SUBMISSION DATE') }}</th>
                        <th class="py-3.5 px-6">{{ __('DOCUMENT TYPE') }}</th>
                        <th class="py-3.5 px-6">{{ __('RISK LEVEL') }}</th>
                        <th class="py-3.5 px-6">{{ __('STATUS') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('ACTIONS') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-800 dark:text-slate-200">
                    @forelse($kycs as $kyc)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors">
                            {{-- User / Entity Name --}}
                            <td class="py-4 px-6">
                                <div class="flex items-center gap-3">
                                    @if($kyc->user && $kyc->user->profile && $kyc->user->profile->avatar_path)
                                        <img class="h-9 w-9 flex-shrink-0 rounded-xl object-cover border border-slate-300 dark:border-slate-600 shrink-0" 
                                             src="{{ asset('storage/' . $kyc->user->profile->avatar_path) }}" 
                                             alt="Avatar">
                                    @else
                                        <div class="h-9 w-9 flex-shrink-0 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-100 shrink-0">
                                            {{ strtoupper(substr($kyc->user->profile->full_name ?? $kyc->user->email ?? 'US', 0, 2)) }}
                                        </div>
                                    @endif
                                    <div>
                                        <span class="font-bold text-slate-900 dark:text-slate-100 block text-xs">
                                            {{ $kyc->user->profile->full_name ?? 'Institutional Client' }}
                                        </span>
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500 font-normal block">
                                            {{ $kyc->user->email ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            {{-- Submission Date --}}
                            <td class="py-4 px-6 text-slate-600 dark:text-slate-400 font-medium whitespace-nowrap">
                                {{ __n($kyc->created_at->translatedFormat('M d, Y - H:i')) }}
                            </td>

                            {{-- Document Type --}}
                            <td class="py-4 px-6 font-semibold text-slate-700 dark:text-slate-300">
                                @php
                                    $docType = $kyc->documents->first()?->type ?? 'ktp';
                                    $docLabel = match($docType) {
                                        'passport' => __('Passport'),
                                        'sim', 'driver_license' => __('Driver License (SIM)'),
                                        default => __('National ID (KTP)')
                                    };
                                @endphp
                                {{ $docLabel }}
                            </td>

                            {{-- Risk Level --}}
                            <td class="py-4 px-6">
                                @if($kyc->isRejected())
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30 uppercase">
                                        {{ __('HIGH') }}
                                    </span>
                                @elseif($kyc->isApproved())
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30 uppercase">
                                        {{ __('LOW') }}
                                    </span>
                                @else
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold bg-amber-500/15 text-amber-600 dark:text-amber-400 border border-amber-500/30 uppercase">
                                        {{ __('MEDIUM') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="py-4 px-6">
                                @if($kyc->isPending())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-600 dark:text-amber-400">
                                        <span class="h-2 w-2 rounded-full bg-amber-500"></span>
                                        {{ __('Pending') }}
                                    </span>
                                @elseif($kyc->isApproved())
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-600 dark:text-emerald-400">
                                        <span class="h-2 w-2 rounded-full bg-emerald-500"></span>
                                        {{ __('Approved') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-bold text-rose-600 dark:text-rose-400">
                                        <span class="h-2 w-2 rounded-full bg-rose-500"></span>
                                        {{ __('Rejected') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="py-4 px-6 text-right whitespace-nowrap">
                                <a href="{{ route('admin.kyc.show', $kyc->id) }}" 
                                   class="inline-flex items-center gap-1 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-bold transition-colors shadow-xs">
                                    <span>{{ __('Review') }}</span>
                                    <span>&rarr;</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                            {{ __('No KYC applications found matching your filter parameters.') }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                            {{ __('Try selecting a different Risk Level, Document Type, or Submission Date.') }}
                                        </p>
                                    </div>
                                    @if(request()->hasAny(['risk', 'type', 'date']))
                                        <a href="{{ route('admin.kyc.index') }}" 
                                           class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
                                            <span>{{ __('Clear All Filters') }}</span>
                                        </a>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($kycs->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                {{ $kycs->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
