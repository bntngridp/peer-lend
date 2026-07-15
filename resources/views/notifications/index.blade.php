@extends('layouts.app')

@section('title', 'Notifications')

@section('content')
<div class="mx-auto max-w-4xl px-4 sm:px-6 lg:px-8 py-10">

    {{-- Header --}}
    <div class="mb-8 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h1 class="text-3xl font-bold text-gray-900">Notifications</h1>
            <p class="mt-1 text-sm text-gray-500">
                @if($unreadCount > 0)
                    You have <span class="font-semibold text-rose-600">{{ $unreadCount }} unread</span> notification{{ $unreadCount > 1 ? 's' : '' }}.
                @else
                    All caught up! No unread notifications.
                @endif
            </p>
        </div>
        <div class="flex items-center gap-2">
            {{-- Filter Tabs --}}
            <div class="flex rounded-xl border border-gray-200 bg-gray-50 p-1 text-sm">
                <a href="{{ route('notifications.index') }}"
                   class="rounded-lg px-3 py-1.5 font-medium transition-colors {{ $filter === 'all' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    All
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'unread']) }}"
                   class="rounded-lg px-3 py-1.5 font-medium transition-colors {{ $filter === 'unread' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    Unread
                </a>
                <a href="{{ route('notifications.index', ['filter' => 'read']) }}"
                   class="rounded-lg px-3 py-1.5 font-medium transition-colors {{ $filter === 'read' ? 'bg-white text-indigo-600 shadow-sm' : 'text-gray-500 hover:text-gray-900' }}">
                    Read
                </a>
            </div>
            {{-- Mark All as Read --}}
            @if($unreadCount > 0)
                <form method="POST" action="{{ route('notifications.read-all') }}">
                    @csrf
                    <button type="submit"
                            class="inline-flex items-center gap-1.5 rounded-xl bg-indigo-600 px-4 py-2 text-sm font-semibold text-white hover:bg-indigo-700 transition-colors shadow-md shadow-indigo-600/10">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5" />
                        </svg>
                        Mark all as read
                    </button>
                </form>
            @endif
        </div>
    </div>

    {{-- Notification List --}}
    @if($notifications->isEmpty())
        <div class="flex flex-col items-center justify-center rounded-2xl border border-dashed border-gray-200 bg-white py-20 text-center">
            <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gray-100">
                <svg class="h-8 w-8 text-gray-400" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.857 17.082a23.848 23.848 0 005.454-1.31A8.967 8.967 0 0118 9.75v-.7V9A6 6 0 006 9v.75a8.967 8.967 0 01-2.312 6.022c1.733.64 3.56 1.085 5.455 1.31m5.714 0a24.255 24.255 0 01-5.714 0m5.714 0a3 3 0 11-5.714 0" />
                </svg>
            </div>
            <p class="mt-4 text-base font-semibold text-gray-700">No notifications yet</p>
            <p class="mt-1 text-sm text-gray-500">We'll let you know when something important happens.</p>
        </div>
    @else
        <div class="space-y-2">
            @foreach($notifications as $notification)
                @php
                    $isUnread = !$notification->isRead();
                    $icon = match($notification->type) {
                        'kyc_approved'        => ['bg' => 'bg-emerald-100', 'text' => 'text-emerald-600', 'emoji' => '✅'],
                        'kyc_rejected'        => ['bg' => 'bg-red-100',     'text' => 'text-red-600',     'emoji' => '❌'],
                        'loan_open_funding'   => ['bg' => 'bg-blue-100',    'text' => 'text-blue-600',    'emoji' => '📋'],
                        'loan_fully_funded'   => ['bg' => 'bg-purple-100',  'text' => 'text-purple-600',  'emoji' => '🎊'],
                        'loan_disbursed'      => ['bg' => 'bg-green-100',   'text' => 'text-green-600',   'emoji' => '💰'],
                        'installment_due'     => ['bg' => 'bg-amber-100',   'text' => 'text-amber-600',   'emoji' => '⏰'],
                        'installment_overdue' => ['bg' => 'bg-rose-100',    'text' => 'text-rose-600',    'emoji' => '🚨'],
                        'installment_paid'    => ['bg' => 'bg-teal-100',    'text' => 'text-teal-600',    'emoji' => '💸'],
                        'loan_completed'      => ['bg' => 'bg-indigo-100',  'text' => 'text-indigo-600',  'emoji' => '🏆'],
                        'loan_liquidated'     => ['bg' => 'bg-red-100',     'text' => 'text-red-600',     'emoji' => '🔴'],
                        'ltv_warning'         => ['bg' => 'bg-orange-100',  'text' => 'text-orange-600',  'emoji' => '⚠️'],
                        default               => ['bg' => 'bg-gray-100',    'text' => 'text-gray-600',    'emoji' => '🔔'],
                    };
                @endphp
                <form method="POST" action="{{ route('notifications.read', $notification) }}">
                    @csrf
                    <button type="submit" class="w-full text-left">
                        <div class="flex items-start gap-4 rounded-2xl border p-4 transition-all hover:shadow-md
                            {{ $isUnread
                                ? 'border-indigo-100 bg-indigo-50/50 hover:bg-indigo-50'
                                : 'border-gray-200 bg-white hover:bg-gray-50' }}">

                            {{-- Icon --}}
                            <div class="flex h-11 w-11 flex-shrink-0 items-center justify-center rounded-xl {{ $icon['bg'] }} text-lg">
                                {{ $icon['emoji'] }}
                            </div>

                            {{-- Content --}}
                            <div class="min-w-0 flex-1">
                                <div class="flex items-center justify-between gap-2">
                                    <p class="text-sm font-semibold text-gray-900 {{ $isUnread ? 'text-indigo-900' : '' }}">
                                        {{ $notification->title }}
                                    </p>
                                    <span class="flex-shrink-0 text-xs text-gray-400">
                                        {{ $notification->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-sm text-gray-600 leading-relaxed">{{ $notification->body }}</p>
                            </div>

                            {{-- Unread indicator --}}
                            @if($isUnread)
                                <div class="mt-2 flex-shrink-0">
                                    <span class="h-2.5 w-2.5 rounded-full bg-indigo-500 block"></span>
                                </div>
                            @endif
                        </div>
                    </button>
                </form>
            @endforeach
        </div>

        {{-- Pagination --}}
        @if($notifications->hasPages())
            <div class="mt-8">
                {{ $notifications->links() }}
            </div>
        @endif
    @endif
</div>
@endsection
