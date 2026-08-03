@extends('layouts.app')

@section('title', __('Role Management') . ' - Admin Terminal')

@section('content')
<div class="space-y-6 max-w-6xl mx-auto">
    
    <!-- Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">{{ __('Role Management') }}</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">{{ __('Configure system access levels, operational permissions, and risk controls across admin roles.') }}</p>
        </div>
        <button type="button" @click="alert('New admin role created!')" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
            {{ __('+ New Role') }}
        </button>
    </div>

    <!-- Permission Matrix Card -->
    <div class="rounded-2xl border border-slate-200 bg-white p-6 shadow-xs space-y-4">
        <h3 class="text-xs font-bold text-slate-900 uppercase tracking-wider border-b border-slate-100 pb-3">{{ __('Operational Permissions Matrix') }}</h3>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[10px] font-bold text-slate-400 uppercase">
                        <th class="py-3 px-4">{{ __('ROLE') }}</th>
                        <th class="py-3 px-4">{{ __('USER MGMT') }}</th>
                        <th class="py-3 px-4">{{ __('LOAN APPROVAL') }}</th>
                        <th class="py-3 px-4">{{ __('FINANCIAL CONFIG') }}</th>
                        <th class="py-3 px-4 text-center">{{ __('SYSTEM LOGS') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 font-semibold text-slate-800">
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-4 font-extrabold text-slate-900">
                            {{ __('Super Admin') }}
                            <span class="block text-[10px] text-slate-400 font-normal">{{ __('Unrestricted system access.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Yes') }}</td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Yes') }}</td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Yes') }}</td>
                        <td class="py-4 px-4 text-center text-emerald-700 font-bold">{{ __('Full Access') }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-4 font-extrabold text-slate-900">
                            {{ __('Risk Officer') }}
                            <span class="block text-[10px] text-slate-400 font-normal">{{ __('Credit decisioning & monitoring.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-slate-400 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Yes') }}</td>
                        <td class="py-4 px-4 text-slate-400 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-center text-emerald-700 font-bold">{{ __('Read Only') }}</td>
                    </tr>
                    <tr class="hover:bg-slate-50/80">
                        <td class="py-4 px-4 font-extrabold text-slate-900">
                            {{ __('Compliance Officer') }}
                            <span class="block text-[10px] text-slate-400 font-normal">{{ __('Regulatory oversight & reporting.') }}</span>
                        </td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Read Only') }}</td>
                        <td class="py-4 px-4 text-emerald-700 font-bold">{{ __('Read Only') }}</td>
                        <td class="py-4 px-4 text-slate-400 font-normal">&mdash;</td>
                        <td class="py-4 px-4 text-center text-emerald-700 font-bold">{{ __('Full Access') }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="pt-2 flex justify-end">
            <button type="button" @click="alert('Role permissions saved!')" class="py-2.5 px-6 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                {{ __('Save Matrix Changes') }} &rarr;
            </button>
        </div>
    </div>

</div>
@endsection
