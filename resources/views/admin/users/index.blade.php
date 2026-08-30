@extends('layouts.admin')

@section('title', __('User Management') . ' - Admin Terminal')

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6">

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
                {{ __n($users->total()) }} {{ __('Registered Users') }}
            </span>
        </div>
    </div>

    {{-- ─── Table Card ───────────────────────────────────────────────────────── --}}
    <div class="overflow-hidden rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 shadow-xs dark:shadow-none">
        
        {{-- Table Filters & Search Bar --}}
        <form method="GET" action="{{ route('admin.users.index') }}" class="p-4 border-b border-slate-200 dark:border-slate-800 flex flex-col sm:flex-row items-center justify-between gap-4 bg-slate-50/50 dark:bg-slate-950/40">
            <div class="relative w-full sm:w-96">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <svg class="w-4 h-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                </div>
                <input type="text" 
                       name="search"
                       value="{{ request('search') }}"
                       placeholder="{{ __('Search user ID, name, email...') }}"
                       class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 py-2 pl-9 pr-9 text-xs font-semibold text-slate-800 dark:text-slate-200 outline-none focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 placeholder-slate-400 dark:placeholder-slate-500">
                @if(request('search'))
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center">
                        <a href="{{ route('admin.users.index', request()->except('search')) }}" class="text-slate-400 hover:text-slate-600 dark:hover:text-slate-200" title="{{ __('Clear search') }}">
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                            </svg>
                        </a>
                    </div>
                @endif
            </div>
            
            <div class="flex items-center gap-3 w-full sm:w-auto">
                {{-- Role Filter --}}
                <select name="role" onchange="this.form.submit()" class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="">{{ __('All Roles') }}</option>
                    <option value="admin" {{ request('role') === 'admin' ? 'selected' : '' }}>{{ __('System Admin') }}</option>
                    <option value="borrower" {{ request('role') === 'borrower' ? 'selected' : '' }}>{{ __('Borrower') }}</option>
                    <option value="lender" {{ request('role') === 'lender' ? 'selected' : '' }}>{{ __('Lender / Investor') }}</option>
                    <option value="customer_service" {{ request('role') === 'customer_service' ? 'selected' : '' }}>{{ __('CS Support') }}</option>
                    <option value="collection_officer" {{ request('role') === 'collection_officer' ? 'selected' : '' }}>{{ __('Collection Officer') }}</option>
                </select>

                {{-- Status Filter --}}
                <select name="status" onchange="this.form.submit()" class="rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 text-xs font-bold text-slate-700 dark:text-slate-300 outline-none focus:border-indigo-500 cursor-pointer">
                    <option value="">{{ __('All Statuses') }}</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>{{ __('Active') }}</option>
                    <option value="pending_kyc" {{ request('status') === 'pending_kyc' ? 'selected' : '' }}>{{ __('Pending KYC') }}</option>
                    <option value="unverified" {{ request('status') === 'unverified' ? 'selected' : '' }}>{{ __('Unverified') }}</option>
                </select>

                <button type="submit" class="rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-3.5 py-2 text-xs font-bold transition-colors">
                    {{ __('Search') }}
                </button>

                @if(request()->hasAny(['search', 'role', 'status']))
                    <a href="{{ route('admin.users.index') }}" class="text-xs font-bold text-rose-500 hover:text-rose-600 dark:hover:text-rose-400 underline whitespace-nowrap">
                        {{ __('Reset All') }}
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
                            $shortId = strtoupper(substr(str_replace('-', '', $usr->id), -6));
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
                                    @if($usr->profile && $usr->profile->avatar_path)
                                        <img class="h-9 w-9 flex-shrink-0 rounded-xl object-cover border border-slate-300 dark:border-slate-600 shrink-0" 
                                             src="{{ asset('storage/' . $usr->profile->avatar_path) }}" 
                                             alt="Avatar">
                                    @else
                                        <div class="h-9 w-9 flex-shrink-0 rounded-xl bg-slate-200 dark:bg-slate-700 flex items-center justify-center border border-slate-300 dark:border-slate-600 text-xs font-bold text-slate-700 dark:text-slate-100 shrink-0">
                                            {{ $initials }}
                                        </div>
                                    @endif
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
                                            $roleLabel = match($r->name) {
                                                'admin' => __('System Admin'),
                                                'borrower' => __('Borrower'),
                                                'lender' => __('Lender'),
                                                'customer_service' => __('CS Support'),
                                                'collection_officer' => __('Collection Officer'),
                                                default => ucfirst(str_replace('_', ' ', $r->name)),
                                            };
                                        @endphp
                                        <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                            {{ $roleLabel }}
                                        </span>
                                    @empty
                                        <span class="text-xs font-medium text-slate-400 dark:text-slate-500">
                                            {{ __('No Role') }}
                                        </span>
                                    @endforelse
                                </div>
                            </td>

                            {{-- Status Column --}}
                            <td class="whitespace-nowrap px-4 py-4">
                                @if($isKycApproved)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-500 shrink-0"></span>
                                        <span>{{ __('Active') }}</span>
                                    </span>
                                @elseif($isKycPending)
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                        <span class="h-1.5 w-1.5 rounded-full bg-amber-500 shrink-0"></span>
                                        <span>{{ __('Pending KYC') }}</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                                        <span class="h-1.5 w-1.5 rounded-full bg-slate-400 shrink-0"></span>
                                        <span>{{ __('Unverified') }}</span>
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
                                        {{ __n($isKycApproved ? '100%' : ($isKycPending ? '65%' : '35%')) }}
                                    </span>
                                </div>
                            </td>

                            {{-- Registered Date --}}
                            <td class="whitespace-nowrap px-4 py-4 text-xs font-medium text-slate-500 dark:text-slate-400">
                                {{ $usr->created_at->diffForHumans() }}
                            </td>

                            {{-- Actions --}}
                            <td class="whitespace-nowrap py-4 pl-4 pr-6 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    @if($usr->id !== auth()->id())
                                        <form method="POST" action="{{ route('admin.users.toggleStatus', $usr) }}" onsubmit="return confirm('{{ $usr->is_active ? __('Are you sure you want to suspend this user account?') : __('Are you sure you want to reactivate this user account?') }}')">
                                            @csrf
                                            <button type="submit" class="rounded-xl border {{ $usr->is_active ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60' : 'border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' }} px-2.5 py-1.5 text-xs font-bold transition-colors cursor-pointer">
                                                {{ $usr->is_active ? __('Suspend') : __('Reactivate') }}
                                            </button>
                                        </form>
                                    @endif

                                    <a href="{{ route('admin.users.show', $usr) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-3 py-1.5 text-xs font-bold transition-colors shadow-xs">
                                        <span>{{ __('View Profile') }}</span>
                                        <span>&rarr;</span>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500 font-medium">
                                <div class="flex flex-col items-center justify-center gap-3">
                                    <div class="h-10 w-10 rounded-full bg-slate-100 dark:bg-slate-800 flex items-center justify-center text-slate-400">
                                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                                        </svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-700 dark:text-slate-300">
                                            {{ __('No users found matching your search & filters.') }}
                                        </p>
                                        <p class="text-xs text-slate-400 dark:text-slate-500 mt-1">
                                            {{ __('Try searching for a different keyword or resetting your active filters.') }}
                                        </p>
                                    </div>
                                    @if(request()->hasAny(['search', 'role', 'status']))
                                        <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
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

        {{-- Pagination Footer --}}
        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-200 dark:border-slate-800 bg-slate-50/50 dark:bg-slate-950/40">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>
@endsection
