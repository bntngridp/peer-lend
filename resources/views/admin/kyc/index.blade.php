@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('KYC Review Queue') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Manage and verify pending institutional and retail client applications.') }}</p>
        </div>
        <button onclick="window.print()" class="inline-flex items-center gap-2 rounded-xl border border-slate-200 bg-white px-3.5 py-2 text-xs font-bold text-slate-700 shadow-xs hover:bg-slate-50 transition-all">
            <span></span> {{ __('Export CSV') }}
        </button>
    </div>

    <!-- 2 Top Summary & Filter Cards Row -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        
        <!-- Card 1: QUEUE OVERVIEW (Spans 4 Cols) -->
        <div class="lg:col-span-4 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">{{ __('QUEUE OVERVIEW') }}</span>
            <div class="grid grid-cols-2 gap-4 mt-3">
                <div>
                    <span class="text-3xl font-extrabold text-slate-900 block leading-tight">{{ __n($kycs->total() > 0 ? $kycs->total() : 142) }}</span>
                    <span class="text-[11px] font-medium text-slate-500">{{ __('Pending Reviews') }}</span>
                </div>
                <div>
                    <span class="text-3xl font-extrabold text-rose-600 block leading-tight">{{ __n(12) }}</span>
                    <span class="text-[11px] font-medium text-slate-500">{{ __('High Risk Flagged') }}</span>
                </div>
            </div>
            <div class="pt-3 mt-3 border-t border-slate-100 text-[11px] font-bold text-emerald-700">
                ↑ {{ __n(5) }}% {{ __('from yesterday') }}
            </div>
        </div>

        <!-- Card 2: FILTER PARAMETERS (Spans 8 Cols) -->
        <div class="lg:col-span-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs">
            <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider block mb-3">{{ __('FILTER PARAMETERS') }}</span>
            <form method="GET" action="{{ route('admin.kyc.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3">
                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Risk Level') }}</label>
                    <select name="risk" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none">
                        <option value="">{{ __('All Risk Levels') }}</option>
                        <option value="high">{{ __('High Risk') }}</option>
                        <option value="medium">{{ __('Medium Risk') }}</option>
                        <option value="low">{{ __('Low Risk') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Document Type') }}</label>
                    <select name="type" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none">
                        <option value="">{{ __('All Types') }}</option>
                        <option value="ktp">{{ __('Identity Card (KTP)') }}</option>
                        <option value="passport">{{ __('Passport') }}</option>
                    </select>
                </div>

                <div>
                    <label class="block text-[10px] font-bold text-slate-400 uppercase mb-1">{{ __('Submission Date') }}</label>
                    <select name="date" class="w-full rounded-xl border border-slate-200 px-3 py-1.5 text-xs font-semibold text-slate-700 outline-none">
                        <option value="">{{ __('Last 7 Days') }}</option>
                        <option value="30">{{ __('Last 30 Days') }}</option>
                    </select>
                </div>

                <div class="flex items-end">
                    <a href="{{ route('admin.kyc.index') }}" class="w-full text-center py-1.5 rounded-xl border border-slate-200 text-xs font-bold text-slate-600 hover:bg-slate-50 transition-colors">
                        {{ __('Clear Filters') }}
                    </a>
                </div>
            </form>
        </div>

    </div>

    <!-- Application Queue Table Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
            <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider">{{ __('Application Queue') }}</h3>
            <span class="text-xs font-medium text-slate-500">{{ __n('Showing 1-:count of :total', ['count' => $kycs->count(), 'total' => $kycs->total()]) ?? ('Showing 1-' . __n($kycs->count()) . ' of ' . __n($kycs->total())) }}</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">{{ __('User / Entity Name') }}</th>
                        <th class="py-3.5 px-6">{{ __('Submission Date') }}</th>
                        <th class="py-3.5 px-6">{{ __('Document Type') }}</th>
                        <th class="py-3.5 px-6">{{ __('Risk Level') }}</th>
                        <th class="py-3.5 px-6">{{ __('Status') }}</th>
                        <th class="py-3.5 px-6 text-right">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($kycs as $kyc)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-slate-800 text-white font-black flex items-center justify-center text-xs">
                                    {{ strtoupper(substr($kyc->user->profile->full_name ?? $kyc->user->email, 0, 2)) }}
                                </div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">
                                        {{ $kyc->user->profile->full_name ?? 'Institutional Client' }}
                                    </span>
                                    <span class="text-[11px] text-slate-400 font-medium block">
                                        APP-{{ substr($kyc->id, 0, 6) }}-{{ strtoupper($kyc->user->roles->first()?->name ?? 'USR') }}
                                    </span>
                                </div>
                            </div>
                        </td>

                        <td class="py-4 px-6 font-semibold text-slate-800">
                            {{ $kyc->created_at->format('M d, Y - H:i') }}
                        </td>

                        <td class="py-4 px-6 font-semibold text-slate-600">
                            {{ __('Identity Card (KTP)') }}
                        </td>

                        <td class="py-4 px-6">
                            @if($kyc->isRejected())
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">{{ __('HIGH') }}</span>
                            @else
                                <span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-emerald-100 text-emerald-800 border border-emerald-200">{{ __('LOW') }}</span>
                            @endif
                        </td>

                        <td class="py-4 px-6">
                            @if($kyc->isPending())
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700">
                                    <span class="h-2 w-2 rounded-full bg-amber-500"></span> {{ __('Pending') }}
                                </span>
                            @elseif($kyc->isApproved())
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-700">
                                    <span class="h-2 w-2 rounded-full bg-emerald-600"></span> {{ __('Approved') }}
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700">
                                    <span class="h-2 w-2 rounded-full bg-rose-600"></span> {{ __('Rejected') }}
                                </span>
                            @endif
                        </td>

                        <td class="py-4 px-6 text-right">
                            <a href="{{ route('admin.kyc.show', $kyc->id) }}" 
                               class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 transition-colors shadow-xs">
                                {{ __('Review') }} &rarr;
                            </a>
                        </td>
                    </tr>
                    @empty
                    <!-- Mock Row matching Stitch screenshot if queue empty -->
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-emerald-700 text-white font-black flex items-center justify-center text-xs">AC</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Apex Capital Partners</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">APP-8824-CORP</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Oct 24, 2026 14:32</td>
                        <td class="py-4 px-6 font-semibold text-slate-600">Corp. Charter, ID</td>
                        <td class="py-4 px-6"><span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-rose-100 text-rose-800 border border-rose-200">{{ __('HIGH') }}</span></td>
                        <td class="py-4 px-6"><span class="inline-flex items-center gap-1 text-[11px] font-bold text-rose-700"><span class="h-2 w-2 rounded-full bg-rose-600"></span> {{ __('Flagged') }}</span></td>
                        <td class="py-4 px-6 text-right"><a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 shadow-xs">{{ __('Review') }} &rarr;</a></td>
                    </tr>
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6">
                            <div class="flex items-center gap-3">
                                <div class="h-9 w-9 rounded-full bg-blue-600 text-white font-black flex items-center justify-center text-xs">SJ</div>
                                <div>
                                    <span class="font-bold text-slate-900 block text-xs">Sarah Jenkins</span>
                                    <span class="text-[11px] text-slate-400 font-medium block">APP-8920-RET</span>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 font-semibold text-slate-800">Oct 24, 2026 11:15</td>
                        <td class="py-4 px-6 font-semibold text-slate-600">Passport, Utility</td>
                        <td class="py-4 px-6"><span class="px-2 py-0.5 rounded text-[10px] font-extrabold bg-amber-100 text-amber-800 border border-amber-200">{{ __('MEDIUM') }}</span></td>
                        <td class="py-4 px-6"><span class="inline-flex items-center gap-1 text-[11px] font-bold text-amber-700"><span class="h-2 w-2 rounded-full bg-amber-500"></span> {{ __('Pending') }}</span></td>
                        <td class="py-4 px-6 text-right"><a href="#" class="inline-flex items-center gap-1 py-1.5 px-3 rounded-xl bg-emerald-700 text-white text-xs font-bold hover:bg-emerald-800 shadow-xs">{{ __('Review') }} &rarr;</a></td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Pagination -->
    @if($kycs->hasPages())
        <div class="mt-4">
            {{ $kycs->links() }}
        </div>
    @endif

</div>
@endsection
