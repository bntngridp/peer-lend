@extends('layouts.admin')

@section('title', __('User Profile') . ' - ' . ($user->profile?->full_name ?? $user->email))

@section('content')
<div class="px-4 sm:px-6 lg:px-8 space-y-6 max-w-7xl mx-auto">

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

    {{-- Back Link & Page Title --}}
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.users.index') }}" class="inline-flex items-center gap-1 text-xs font-bold text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors mb-2">
                &larr; {{ __('Back to Users List') }}
            </a>
            <h1 class="text-2xl font-bold tracking-tight text-slate-900 dark:text-slate-100 flex items-center gap-3">
                <span>{{ $user->profile?->full_name ?? __('Unnamed User') }}</span>
                @if($user->is_active)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-500/15 text-emerald-600 dark:text-emerald-400 border border-emerald-500/30">
                        {{ __('Account Active') }}
                    </span>
                @else
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold bg-rose-500/15 text-rose-600 dark:text-rose-400 border border-rose-500/30">
                        {{ __('Account Suspended') }}
                    </span>
                @endif
            </h1>
            <p class="mt-1 text-xs font-mono text-slate-400 dark:text-slate-500">
                ID: {{ $user->id }} &bull; {{ __('Registered') }}: {{ $user->created_at->format('M d, Y H:i') }} ({{ $user->created_at->diffForHumans() }})
            </p>
        </div>

        {{-- Action Buttons --}}
        <div class="flex items-center gap-3">
            @if($user->id !== auth()->id())
                <form method="POST" action="{{ route('admin.users.toggleStatus', $user) }}" onsubmit="return confirm('{{ $user->is_active ? __('Are you sure you want to suspend this user account?') : __('Are you sure you want to reactivate this user account?') }}')">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 rounded-xl border {{ $user->is_active ? 'border-rose-300 dark:border-rose-800 bg-rose-50 dark:bg-rose-950/40 text-rose-600 dark:text-rose-400 hover:bg-rose-100 dark:hover:bg-rose-900/60' : 'border-emerald-300 dark:border-emerald-800 bg-emerald-50 dark:bg-emerald-950/40 text-emerald-600 dark:text-emerald-400 hover:bg-emerald-100 dark:hover:bg-emerald-900/60' }} px-4 py-2 text-xs font-bold transition-colors cursor-pointer shadow-xs">
                        <span>{{ $user->is_active ? __('Suspend Account') : __('Reactivate Account') }}</span>
                    </button>
                </form>
            @endif

            @if($user->kyc)
                <a href="{{ route('admin.kyc.show', $user->kyc) }}" class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 text-xs font-bold transition-colors shadow-xs">
                    <span>{{ __('View KYC Application') }}</span>
                    <span>&rarr;</span>
                </a>
            @endif
        </div>
    </div>

    {{-- Main Grid --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Left: User Overview (4 Cols) --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- User Details Card --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <div class="flex items-center gap-4 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div class="h-14 w-14 rounded-2xl bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-100 flex items-center justify-center font-bold text-lg border border-slate-300 dark:border-slate-700 shrink-0">
                        {{ strtoupper(substr($user->profile?->full_name ?? $user->email, 0, 2)) }}
                    </div>
                    <div>
                        <h3 class="text-base font-bold text-slate-900 dark:text-slate-100">{{ $user->profile?->full_name ?? __('Unnamed User') }}</h3>
                        <p class="text-xs text-slate-500 dark:text-slate-400 font-medium">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="space-y-3 text-xs">
                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">{{ __('Phone Number') }}:</span>
                        <span class="font-bold text-slate-900 dark:text-slate-100">{{ $user->profile?->phone ?? __('Not provided') }}</span>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">{{ __('Roles') }}:</span>
                        <div class="flex flex-wrap gap-2 justify-end">
                            @foreach($user->roles as $r)
                                <span class="text-xs font-semibold text-slate-700 dark:text-slate-300">
                                    {{ ucfirst(str_replace('_', ' ', $r->name)) }}
                                </span>
                            @endforeach
                        </div>
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">{{ __('KYC Verification') }}:</span>
                        @if($user->kyc && $user->kyc->status === 'approved')
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                {{ __('Approved') }}
                            </span>
                        @elseif($user->kyc && $user->kyc->status === 'pending')
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300">
                                <span class="h-1.5 w-1.5 rounded-full bg-amber-500"></span>
                                {{ __('Pending Review') }}
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-500 dark:text-slate-400">
                                <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                {{ __('Unverified') }}
                            </span>
                        @endif
                    </div>

                    <div class="flex justify-between py-1 border-b border-slate-100 dark:border-slate-800">
                        <span class="text-slate-400 font-medium">{{ __('Google 2FA') }}:</span>
                        <span class="font-bold {{ $user->google2fa_enabled ? 'text-emerald-600 dark:text-emerald-400' : 'text-slate-400' }}">
                            {{ $user->google2fa_enabled ? __('Enabled') : __('Disabled') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Wallets Summary Card --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Wallets & Balances') }}
                </h3>

                <div class="space-y-3">
                    @forelse($user->wallets as $wallet)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ $wallet->currency->code }} - {{ $wallet->currency->name }}</span>
                                <span class="text-[10px] text-slate-400 font-medium">{{ __('Hold') }}: {{ number_format($wallet->hold_balance, 2) }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-extrabold text-slate-900 dark:text-slate-100 text-sm block">
                                    {{ number_format($wallet->available_balance, 2) }}
                                </span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 font-medium">{{ __('No wallets initialized.') }}</p>
                    @endforelse
                </div>
            </div>
        </div>

        {{-- Right: Activity, Loans, & Audit Logs (8 Cols) --}}
        <div class="lg:col-span-8 space-y-6">

            {{-- Loan Applications --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Recent Loan Applications') }}
                </h3>

                <div class="overflow-x-auto">
                    <table class="w-full text-left text-xs border-collapse">
                        <thead>
                            <tr class="bg-slate-50/80 dark:bg-slate-950/60 border-b border-slate-200 dark:border-slate-800 text-[10px] font-bold text-slate-400 dark:text-slate-500 uppercase">
                                <th class="py-2.5 px-3">{{ __('LOAN ID') }}</th>
                                <th class="py-2.5 px-3">{{ __('AMOUNT') }}</th>
                                <th class="py-2.5 px-3">{{ __('RATE') }}</th>
                                <th class="py-2.5 px-3">{{ __('GRADE') }}</th>
                                <th class="py-2.5 px-3 text-right">{{ __('STATUS') }}</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100 dark:divide-slate-800 font-semibold text-slate-800 dark:text-slate-200">
                            @forelse($user->loanRequests as $loan)
                                <tr>
                                    <td class="py-3 px-3 font-mono text-xs">
                                        <a href="{{ route('admin.loans.show', $loan) }}" class="text-indigo-600 dark:text-indigo-400 hover:underline">
                                            LN-{{ strtoupper(substr($loan->id, 0, 8)) }}
                                        </a>
                                    </td>
                                    <td class="py-3 px-3 font-bold">Rp {{ number_format($loan->amount, 0, ',', '.') }}</td>
                                    <td class="py-3 px-3">{{ $loan->interest_rate }}%</td>
                                    <td class="py-3 px-3 font-extrabold text-emerald-600 dark:text-emerald-400">{{ $loan->risk_grade }}</td>
                                    <td class="py-3 px-3 text-right">
                                        <span class="inline-flex items-center gap-1.5 text-xs font-medium text-slate-700 dark:text-slate-300 justify-end">
                                            <span class="h-1.5 w-1.5 rounded-full shrink-0
                                                @if($loan->status === 'active') bg-emerald-500
                                                @elseif($loan->status === 'pending' || $loan->status === 'open_funding') bg-amber-500
                                                @elseif($loan->status === 'completed') bg-slate-400
                                                @else bg-rose-500 @endif"></span>
                                            <span>{{ __(ucwords(str_replace('_', ' ', $loan->status))) }}</span>
                                        </span>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="py-6 text-center text-slate-400 text-xs font-medium">{{ __('No loan applications recorded.') }}</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- Audit Trail Timeline --}}
            <div class="rounded-2xl border border-slate-200 dark:border-slate-800 bg-white dark:bg-slate-900 p-6 shadow-xs space-y-4">
                <h3 class="text-xs font-bold text-slate-900 dark:text-slate-100 uppercase tracking-wider border-b border-slate-100 dark:border-slate-800 pb-3">
                    {{ __('Recent Audit Trail') }}
                </h3>

                <div class="space-y-3 text-xs">
                    @forelse($user->auditLogs as $log)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-800/60 border border-slate-200 dark:border-slate-700/80 flex items-center justify-between">
                            <div>
                                <span class="font-bold text-slate-900 dark:text-slate-100 block">{{ ucfirst(str_replace('_', ' ', $log->action)) }}</span>
                                <span class="text-[10px] text-slate-400 font-mono">{{ json_encode($log->payload) }}</span>
                            </div>
                            <span class="text-[10px] font-medium text-slate-400 shrink-0">
                                {{ $log->created_at->diffForHumans() }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400 font-medium">{{ __('No audit log records.') }}</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>

</div>
@endsection
