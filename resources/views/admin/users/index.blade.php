@extends('layouts.admin')

@section('title', __('User Management') . ' - Admin Terminal')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6">

    {{-- ─── Page Header ──────────────────────────────────────────────────────── --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100">
                {{ __('User Management') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Manage institutional and retail participants across all roles & onboarding states.') }}
            </p>
        </div>
        
        <div class="flex items-center gap-3 flex-shrink-0">
            <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 dark:bg-slate-800 border border-slate-200 dark:border-slate-700 px-3 py-1 text-xs font-semibold text-slate-600 dark:text-slate-300">
                <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span>
                {{ $users->total() }} {{ __('Registered Users') }}
            </span>
        </div>
    </div>

    {{-- ─── Table Card ───────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs dark:shadow-none">
        
        {{-- Table Filters & Search Bar --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/40">
            <div class="w-full sm:w-96 relative">
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Search user ID, name, email...') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3.5 py-2 pl-9 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-500">
                <svg class="w-4 h-4 absolute left-3 top-2.5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Role Filter --}}
                <select name="role" onchange="this.form.submit()" class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500">
                    <option value="">{{ __('All Roles') }}</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('System Admin') }}</option>
                    <option value="borrower" {{ request('role') === 'borrower' ? 'selected' : '' }}>{{ __('Borrower') }}</option>
                    <option value="lender" {{ request('role') === 'lender' ? 'selected' : '' }}>{{ __('Lender / Investor') }}</option>
                    <option value="customer_service" {{ request('role') === 'customer_service' ? 'selected' : '' }}>{{ __('CS Support') }}</option>
                    <option value="collection_officer" {{ request('role') === 'collection_officer' ? 'selected' : '' }}>{{ __('Collection Officer') }}</option>
                </select>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="pending_kyc" {{ request('status') === 'pending_kyc' ? 'selected' : '' }}>{{ __('Pending KYC') }}</option>
                    <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>{{ __('Unverified') }}</option>
                </select>

                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('users.index') }}" class="text-xs font-bold text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 underline">
                        {{ __('Reset') }}
                    </a>
                @endif
            </div>
        </form>

        {{-- Table Scroll Wrapper --}}
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-slate-100 dark:divide-slate-800">
                <thead>
                    <tr class="bg-slate-50/80 dark:bg-slate-950/60">
                        <th scope="col" class="py-3.5 pl-6 pr-3 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('User ID') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('User Details') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Assigned Roles') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('KYC Status') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Onboarding Progress') }}
                        </th>
                        <th scope="col" class="px-4 py-3.5 text-left text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Registered') }}
                        </th>
                        <th scope="col" class="py-3.5 pl-4 pr-6 text-right text-xs font-bold uppercase tracking-wider text-slate-400 dark:text-slate-500 whitespace-nowrap">
                            {{ __('Action') }}
                        </th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800 bg-white dark:bg-slate-900">
                    @forelse($users as $usr)
                        @php
                            $fullName = $usr->profile?->full_name ?? __('Unnamed User');
                            $initials = strtoupper(substr($fullName, 0, 2));
                            $shortId = strtoupper(substr(str_replace('-', '', $usr->id), 0, 8));
                            $isKycApproved = $usr->kyc && $usr->kyc->status === 'approved';
                            $isKycPending = $usr->kyc && $usr->kyc->status === 'pending';
                        @endphp
                        <tr class="group hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition-colors duration-150">
                            
                            {{-- User ID --}}
                            <td class="whitespace-nowrap py-4 pl-6 pr-3">
                                <span class="font-mono text-xs font-bold text-slate-700 dark:text-slate-300" title="{{ $usr->id }}">
                                    USR-{{ $shortId }}
                                </span>
                            </td>

                            {{-- User Details --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 flex-shrink-0 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-100">
                                        {{ $initials }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="text-sm font-semibold text-slate-900 dark:text-slate-100 truncate max-w-[180px]">
                                            {{ $fullName }}
                                        </div>
                                        <div class="text-xs text-slate-400 dark:text-slate-500 truncate max-w-[180px]">
                                            {{ $usr->email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Roles Column --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex flex-wrap items-center gap-1.5">
                                    @forelse($usr->roles as $r)
                                        @php
                                            $roleStyle = match($r->name) {
                                                'admin' => 'background:rgba(168,85,247,0.15);color:#c084fc;border-color:rgba(168,85,247,0.35)',
                                                'borrower' => 'background:rgba(59,130,246,0.15);color:#60a5fa;border-color:rgba(59,130,246,0.35)',
                                                'lender' => 'background:rgba(16,185,129,0.15);color:#34d399;border-color:rgba(16,185,129,0.35)',
                                                'customer_service' => 'background:rgba(245,158,11,0.15);color:#fbbf24;border-color:rgba(245,158,11,0.35)',
                                                'collection_officer' => 'background:rgba(244,63,94,0.15);color:#fb7185;border-color:rgba(244,63,94,0.35)',
                                                default => 'background:rgba(100,116,139,0.15);color:#94a3b8;border-color:rgba(100,116,139,0.35)',
                                            };
                                            $roleLabel = match($r->name) {
                                                'admin' => __('System Admin'),
                                                'borrower' => __('Borrower'),
                                                'lender' => __('Lender'),
                                                'customer_service' => __('CS Support'),
                                                'collection_officer' => __('Collection Officer'),
                                                default => ucfirst(str_replace('_', ' ', $r->name)),
                                            };
                                        @endphp
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-bold" style="{{ $roleStyle }}">
                                            {{ $roleLabel }}
                                        </span>
                                    @empty
                                        <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-medium text-slate-400 dark:text-slate-500 border-slate-200 dark:border-slate-700 bg-slate-100 dark:bg-slate-800">
                                            {{ __('No Role') }}
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status Column --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                @if($isKycApproved)
                                    <span class="inline-flex items-center justify-center text-center rounded-full border border-green-500/30 bg-green-500/15 px-3 py-0.5 text-xs font-bold text-green-400 min-w-[84px]">
                                        {{ __('Active') }}
                                    </span>
                                @elseif($isKycPending)
                                    <span class="inline-flex items-center justify-center text-center rounded-full border border-amber-500/30 bg-amber-500/15 px-3 py-0.5 text-xs font-bold text-amber-400 min-w-[84px]">
                                        {{ __('Pending KYC') }}
                                    </span>
                                @else
                                    <span class="inline-flex items-center justify-center text-center rounded-full border border-slate-500/30 bg-slate-500/15 px-3 py-0.5 text-xs font-medium text-slate-400 min-w-[84px]">
                                        {{ __('Unverified') }}
                                    </span>
                                @endif
                            </td>

                            {{-- Onboarding Progress --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-24 bg-slate-100 dark:bg-slate-800 rounded-full h-1.5 overflow-hidden">
                                        <div class="{{ $isKycApproved ? 'bg-emerald-500' : ($isKycPending ? 'bg-amber-500' : 'bg-slate-400') }} h-1.5 rounded-full transition-all duration-500" style="width: {{ $isKycApproved ? '100%' : ($isKycPending ? '65%' : '35%') }}"></div>
                                    </div>
                                    <span class="text-xs font-semibold {{ $isKycApproved ? 'text-emerald-400' : 'text-slate-500 dark:text-slate-400' }} tabular-nums">
                                        {{ $isKycApproved ? '100%' : ($isKycPending ? '65%' : '35%') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Registered Date --}}
                            <td class="whitespace-nowrap px-4 py-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ $usr->created_at->diffForHumans() }}
                            </td>

                            {{-- Actions --}}
                            <td class="whitespace-nowrap py-4 pl-4 pr-6 text-right">
                                <a href="{{ route('admin.kyc.index') }}" class="inline-flex items-center gap-1.5 rounded-xl border border-slate-300 dark:border-slate-700 bg-slate-100 dark:bg-slate-800 px-3 py-1.5 text-xs font-bold text-slate-700 dark:text-slate-200 hover:bg-slate-200 dark:hover:bg-slate-700 hover:border-slate-400 dark:hover:border-slate-600 transition-all">
                                    <svg class="w-3.5 h-3.5 text-slate-500 dark:text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12c.077-.19.183-.37.313-.54C3.957 9.07 7.247 6 12 6c4.753 0 8.043 3.07 9.651 5.46.13.17.236.35.313.54-.077.19-.183.37-.313.54C20.043 14.93 16.753 18 12 18c-4.753 0-8.043-3.07-9.651-5.46a2.535 2.535 0 0 1-.313-.54Z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                                    </svg>
                                    {{ __('View Profile') }}
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <svg class="w-8 h-8 text-slate-300 dark:text-slate-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 6a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0ZM4.501 20.118a7.5 7.5 0 0 1 14.998 0A17.933 17.933 0 0 1 12 21.75c-2.676 0-5.216-.584-7.499-1.632Z" />
                                    </svg>
                                    <span>{{ __('No user records found.') }}</span>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination Footer --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
