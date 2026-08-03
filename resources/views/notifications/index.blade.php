@extends('layouts.app')

@section('title', __('Notification Center'))

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ notifTab: 'all', search: '' }">
    
    <!-- Main 2-Column Grid -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
        
        <!-- Left Sub-navigation Sidebar (Spans 3 Cols) -->
        <div class="lg:col-span-3 rounded-2xl border border-slate-200 bg-white p-5 shadow-xs space-y-6 sticky top-20">
            <div>
                <h2 class="text-lg font-extrabold text-slate-900 tracking-tight">{{ __('Notification Center') }}</h2>
                <p class="text-xs font-medium text-slate-500 mt-0.5">{{ __('Manage your alerts & system updates.') }}</p>
            </div>

            <!-- Sub-navigation Links -->
            <div class="space-y-1 text-xs font-semibold text-slate-600">
                <button @click="notifTab = 'all'"
                        :class="notifTab === 'all' ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-emerald-400'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-left">
                    <span>{{ __('All Notifications') }}</span>
                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-300">{{ __n($notifications->count()) }}</span>
                </button>

                <button @click="notifTab = 'unread'"
                        :class="notifTab === 'unread' ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-emerald-400'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-left">
                    <span>{{ __('Unread') }}</span>
                    @if($unreadCount > 0)
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-100 text-rose-800">{{ __n($unreadCount) }}</span>
                    @endif
                </button>

                <button @click="notifTab = 'loan'"
                        :class="notifTab === 'loan' ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-emerald-400'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-left">
                    <span>{{ __('Loan Alerts') }}</span>
                </button>

                <button @click="notifTab = 'wallet'"
                        :class="notifTab === 'wallet' ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-emerald-400'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-left">
                    <span>{{ __('Wallet Updates') }}</span>
                </button>

                <button @click="notifTab = 'security'"
                        :class="notifTab === 'security' ? 'bg-emerald-50 text-emerald-800 border-l-4 border-emerald-700 shadow-xs' : 'text-slate-600 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-emerald-400'"
                        class="w-full flex items-center justify-between px-3.5 py-2.5 rounded-xl transition-all text-left">
                    <span>{{ __('Security') }}</span>
                </button>
            </div>

            <!-- Action Button -->
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}" class="pt-2 border-t border-slate-100">
                    @csrf
                    <button type="submit" class="w-full py-2.5 rounded-xl border border-slate-200 bg-white text-slate-700 font-bold text-xs hover:bg-slate-50 transition-colors shadow-xs">
                        {{ __('Mark all as read') }}
                    </button>
                </form>
            @endif
        </div>

        <!-- Right Main Notifications Feed (Spans 9 Cols) -->
        <div class="lg:col-span-9 space-y-6">
            
            <!-- Top Search & Header Bar -->
            <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-xs flex flex-col sm:flex-row items-center justify-between gap-4">
                <div class="w-full sm:w-80 relative">
                    <input type="text" x-model="search" placeholder="{{ __('Search notifications...') }}"
                           class="w-full rounded-xl border border-slate-200 px-3.5 py-2 text-xs font-semibold text-slate-800 outline-none focus:border-emerald-600 focus:ring-1 focus:ring-emerald-600">
                </div>
                <div class="flex items-center gap-3">
                    <a href="{{ route('loans.create') }}" class="py-2 px-4 rounded-xl bg-emerald-700 text-white font-bold text-xs hover:bg-emerald-800 transition-colors shadow-xs">
                        {{ __('Create Loan') }}
                    </a>
                </div>
            </div>

            <!-- Notifications Stream -->
            @if($notifications->isEmpty())
                <div class="rounded-2xl border border-slate-200 bg-white p-12 text-center shadow-xs">
                    <div class="mx-auto h-12 w-12 rounded-2xl bg-slate-100 border border-slate-200 text-slate-400 font-bold flex items-center justify-center text-sm mb-3">
                        N
                    </div>
                    <h3 class="text-sm font-bold text-slate-900">{{ __('No notifications yet') }}</h3>
                    <p class="text-xs text-slate-500 font-medium mt-1">{{ __('We will notify you when important activity occurs.') }}</p>
                </div>
            @else
                <div class="space-y-3">
                    @foreach($notifications as $notification)
                        @php
                            $isUnread = !$notification->isRead();
                            $category = str_contains(strtolower($notification->type), 'loan') ? 'loan' 
                                      : (str_contains(strtolower($notification->type), 'wallet') || str_contains(strtolower($notification->type), 'deposit') || str_contains(strtolower($notification->type), 'installment') ? 'wallet' 
                                      : (str_contains(strtolower($notification->type), 'security') || str_contains(strtolower($notification->type), 'login') || str_contains(strtolower($notification->type), 'kyc') ? 'security' : 'all'));
                        @endphp

                        <div x-show="(notifTab === 'all' || notifTab === '{{ $category }}' || (notifTab === 'unread' && {{ $isUnread ? 'true' : 'false' }})) && ('{{ strtolower($notification->title . ' ' . $notification->body) }}'.includes(search.toLowerCase()))"
                             class="rounded-2xl border p-5 shadow-xs transition-all hover:border-slate-300
                                {{ $isUnread ? 'border-emerald-200 bg-emerald-50/40' : 'border-slate-200 bg-white' }}">
                            
                            <div class="flex items-start justify-between gap-4">
                                <div class="space-y-1">
                                    <div class="flex items-center gap-2">
                                        <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase tracking-wider
                                            @if($isUnread) bg-emerald-700 text-white
                                            @else bg-slate-100 text-slate-700 border border-slate-200 @endif">
                                            {{ str_replace('_', ' ', $notification->type) }}
                                        </span>
                                        <h4 class="text-xs font-extrabold text-slate-900">{{ $notification->title }}</h4>
                                    </div>
                                    <p class="text-xs text-slate-600 font-medium leading-relaxed pt-1">{{ $notification->body }}</p>
                                </div>

                                <div class="text-right shrink-0">
                                    <span class="text-[11px] font-semibold text-slate-400 block">{{ $notification->created_at->diffForHumans() }}</span>
                                    @if($isUnread)
                                        <form method="POST" action="{{ route('notifications.read', $notification) }}" class="mt-2">
                                            @csrf
                                            <button type="submit" class="text-[10px] font-bold text-emerald-700 hover:underline">
                                                {{ __('Mark Read') }} &rarr;
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                @if($notifications->hasPages())
                    <div class="pt-4">
                        {{ $notifications->links() }}
                    </div>
                @endif
            @endif

        </div>

    </div>

</div>
@endsection
