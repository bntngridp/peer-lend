@extends('layouts.admin')

@section('title', __('Role Management') . ' - Admin Terminal')

@section('content')
<div x-data="{ 
    showNewRoleModal: false, 
    newRoleName: '',
    newRoleDescription: ''
}" class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto relative">
    
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
                {{ __('Role Management') }}
            </h1>
            <p class="mt-1 text-sm text-slate-500 dark:text-slate-400">
                {{ __('Configure system access levels, operational permissions, and risk controls across admin roles.') }}
            </p>
        </div>
        
        <button type="button" 
                @click="showNewRoleModal = true" 
                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs cursor-pointer">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            <span>{{ __('+ New Role') }}</span>
        </button>
    </div>

    {{-- ─── Roles List & Permission Matrix ───────────────────────────────────── --}}
    <div class="space-y-6">
        @foreach($roles as $role)
            @php
                $systemRoles = ['admin', 'borrower', 'lender', 'collection_officer', 'customer_service'];
                $isSystemRole = in_array($role->name, $systemRoles);
                $assignedPermIds = $role->permissions->pluck('id')->toArray();
            @endphp
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <h3 class="text-base font-extrabold text-slate-900 dark:text-slate-100">
                                {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                            </h3>
                            @if($isSystemRole)
                                <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 bg-indigo-500/10 px-2 py-0.5 rounded-full border border-indigo-500/20">
                                    {{ __('System Role') }}
                                </span>
                            @else
                                <span class="text-[10px] font-bold text-purple-600 dark:text-purple-400 bg-purple-500/10 px-2 py-0.5 rounded-full border border-purple-500/20">
                                    {{ __('Custom Role') }}
                                </span>
                            @endif
                        </div>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">
                            {{ $role->description ?? __('Manage permission grants for :role tier.', ['role' => $role->name]) }}
                        </p>
                    </div>

                    <div class="flex items-center gap-2 shrink-0">
                        @if(!$isSystemRole)
                            <form method="POST" action="{{ route('admin.roles.destroy', $role) }}" onsubmit="return confirm('{{ __('Are you sure you want to delete this role?') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="rounded-xl border border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60 px-3 py-1.5 text-xs font-bold transition-colors cursor-pointer">
                                    {{ __('Delete Role') }}
                                </button>
                            </form>
                        @endif

                        <button type="submit" form="role-form-{{ $role->id }}"
                                class="inline-flex items-center gap-1.5 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-white px-3.5 py-1.5 text-xs font-bold transition-colors shadow-xs cursor-pointer">
                            <span>{{ __('Save Permissions') }}</span>
                        </button>
                    </div>
                </div>

                <form id="role-form-{{ $role->id }}" method="POST" action="{{ route('admin.roles.updatePermissions', $role) }}">
                    @csrf
                    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-3 pt-2">
                        @foreach($permissions as $perm)
                            @php
                                $isChecked = in_array($perm->id, $assignedPermIds);
                            @endphp
                            <label class="flex items-start gap-2.5 p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors">
                                <input type="checkbox" 
                                       name="permissions[]" 
                                       value="{{ $perm->id }}" 
                                       {{ $isChecked ? 'checked' : '' }}
                                       class="accent-emerald-600 h-4 w-4 rounded shrink-0 mt-0.5">
                                <div>
                                    <span class="block text-xs font-bold text-slate-900 dark:text-slate-100">
                                        {{ $perm->name }}
                                    </span>
                                </div>
                            </label>
                        @endforeach
                    </div>
                </form>
            </div>
        @endforeach
    </div>

    {{-- ═══════════════════════════════════════════════════════════════════════ --}}
    {{-- MODAL: CREATE NEW ROLE --}}
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
            
            <form method="POST" action="{{ route('admin.roles.store') }}" class="space-y-4">
                @csrf
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
                            {{ __('Role Key / Identifier (lowercase_with_underscores)') }}
                        </label>
                        <input type="text" 
                               name="name"
                               required
                               placeholder="{{ __('e.g. treasury_officer') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                    </div>

                    <div>
                        <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider mb-1">
                            {{ __('Role Description') }}
                        </label>
                        <input type="text" 
                               name="description"
                               placeholder="{{ __('e.g. Oversees wallet balances and liquidity') }}"
                               class="w-full rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 px-3 py-2 font-semibold text-slate-900 dark:text-slate-100 outline-none focus:border-emerald-500">
                    </div>

                    <div class="pt-1 space-y-2">
                        <label class="block text-[11px] font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider">
                            {{ __('Initial Permission Grants') }}
                        </label>
                        <div class="grid grid-cols-1 gap-2 text-xs">
                            @foreach($permissions as $perm)
                                <label class="flex items-center gap-2 p-2 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 cursor-pointer hover:bg-slate-100 dark:hover:bg-slate-800">
                                    <input type="checkbox" name="permissions[]" value="{{ $perm->id }}" class="accent-emerald-600 h-4 w-4 rounded">
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $perm->name }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 pt-2">
                    <button type="button" 
                            @click="showNewRoleModal = false" 
                            class="px-4 py-2 rounded-xl border border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-800 text-xs font-bold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-700 transition-colors">
                        {{ __('Cancel') }}
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 rounded-xl bg-emerald-700 dark:bg-emerald-600 hover:bg-emerald-800 dark:hover:bg-emerald-500 text-xs font-bold text-white transition-colors shadow-xs cursor-pointer">
                        {{ __('Confirm & Create Role') }}
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection
