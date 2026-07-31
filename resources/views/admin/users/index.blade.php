@extends('layouts.app')

@section('title', 'User Management - Admin Terminal')

@section('content')
<div class="space-y-6 max-w-7xl mx-auto">
    
    <!-- Top Header Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">User Management</h1>
            <p class="text-xs font-medium text-slate-500 mt-1">Manage institutional and retail participants across all roles &amp; onboarding states.</p>
        </div>
        <div class="flex items-center gap-3">
            <button type="button" @click="alert('Downloading User Export CSV...')" class="py-2 px-3.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 shadow-xs">
                Download Export CSV
            </button>
            <button type="button" @click="alert('Add New User dialog opened.')" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                + Add New User
            </button>
        </div>
    </div>

    <!-- Table Container Card -->
    <div class="rounded-2xl border border-slate-200 bg-white shadow-xs overflow-hidden">
        
        <!-- Table Filters & Search -->
        <div class="p-4 border-b border-slate-100 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="w-full sm:w-80">
                <input type="text" placeholder="Search user ID, name, email..."
                       class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
            </div>
            <div class="flex items-center gap-3 w-full sm:w-auto">
                <select class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none">
                    <option value="">All Roles</option>
                    <option value="borrower">Borrower</option>
                    <option value="lender">Lender</option>
                    <option value="admin">Admin</option>
                </select>
                <select class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-bold text-slate-700 outline-none">
                    <option value="">All Statuses</option>
                    <option value="active">Active</option>
                    <option value="pending_kyc">Pending KYC</option>
                    <option value="suspended">Suspended</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-slate-50 border-b border-slate-100 text-[11px] font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-6">USER ID</th>
                        <th class="py-3.5 px-6">NAME</th>
                        <th class="py-3.5 px-6">ROLE</th>
                        <th class="py-3.5 px-6">STATUS</th>
                        <th class="py-3.5 px-6">ONBOARDING</th>
                        <th class="py-3.5 px-6">LAST LOGIN</th>
                        <th class="py-3.5 px-6 text-right">ACTIONS</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 text-xs font-medium text-slate-700">
                    @forelse($users as $usr)
                    <tr class="hover:bg-slate-50/80 transition-colors">
                        <td class="py-4 px-6 font-bold text-slate-900">USR-{{ str_pad($usr->id, 4, '0', STR_PAD_LEFT) }}</td>
                        <td class="py-4 px-6">
                            <span class="font-bold text-slate-900 block">{{ $usr->profile?->full_name ?? 'N/A' }}</span>
                            <span class="text-[11px] text-slate-400 font-normal">{{ $usr->email }}</span>
                        </td>
                        <td class="py-4 px-6">
                            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider
                                @if($usr->role === 'admin') bg-purple-100 text-purple-800
                                @elseif($usr->role === 'lender') bg-blue-100 text-blue-800
                                @else bg-slate-100 text-slate-700 @endif">
                                {{ $usr->role }}
                            </span>
                        </td>
                        <td class="py-4 px-6">
                            @if($usr->kyc && $usr->kyc->status === 'approved')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            @elseif($usr->kyc && $usr->kyc->status === 'pending')
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-amber-100 text-amber-800">
                                    Pending KYC
                                </span>
                            @else
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[11px] font-bold bg-slate-100 text-slate-600">
                                    Unverified
                                </span>
                            @endif
                        </td>
                        <td class="py-4 px-6">
                            <div class="w-28 space-y-1">
                                <div class="flex justify-between text-[10px] font-bold text-slate-500">
                                    <span>Progress</span>
                                    <span>{{ $usr->kyc && $usr->kyc->status === 'approved' ? '100%' : '45%' }}</span>
                                </div>
                                <div class="h-1.5 w-full bg-slate-100 rounded-full overflow-hidden">
                                    <div class="h-full bg-emerald-600 rounded-full" style="width: {{ $usr->kyc && $usr->kyc->status === 'approved' ? '100%' : '45%' }}"></div>
                                </div>
                            </div>
                        </td>
                        <td class="py-4 px-6 text-slate-500 text-[11px]">
                            {{ $usr->created_at->diffForHumans() }}
                        </td>
                        <td class="py-4 px-6 text-right">
                            <button type="button" @click="alert('User details dialog opened for {{ $usr->email }}')" class="font-bold text-emerald-700 hover:underline">
                                View Details
                            </button>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="py-12 text-center text-slate-400 font-medium">No user records found.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
        <div class="p-4 border-t border-slate-100">
            {{ $users->links() }}
        </div>
        @endif
    </div>

</div>
@endsection
