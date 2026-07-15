<?php

namespace App\Modules\Shared\Controllers;

use App\Models\Notification;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class NotificationController extends Controller
{
    /**
     * Display paginated list of all notifications for the authenticated user.
     */
    public function index(Request $request): View
    {
        $filter = $request->query('filter', 'all');

        $query = Notification::where('user_id', Auth::id())
            ->orderByRaw('read_at IS NOT NULL ASC')
            ->orderBy('created_at', 'desc');

        if ($filter === 'unread') {
            $query->whereNull('read_at');
        } elseif ($filter === 'read') {
            $query->whereNotNull('read_at');
        }

        $notifications = $query->paginate(20)->withQueryString();

        $unreadCount = Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', compact('notifications', 'unreadCount', 'filter'));
    }

    /**
     * Mark a specific notification as read and redirect to the relevant route if available.
     */
    public function markAsRead(Notification $notification): RedirectResponse
    {
        abort_unless($notification->user_id === Auth::id(), 403);

        $notification->markAsRead();

        // Redirect to the relevant page if a route is stored in notification data
        $routeName = $notification->data['route'] ?? null;
        $routeParams = $notification->data['loan_id']
            ?? $notification->data['installment_id']
            ?? null;

        if ($routeName && \Route::has($routeName)) {
            try {
                return redirect()->route($routeName, $routeParams ? [$routeParams] : []);
            } catch (\Throwable) {
                // Fall through to notifications page if route params are invalid
            }
        }

        return redirect()->route('notifications.index');
    }

    /**
     * Mark all unread notifications as read for the authenticated user.
     */
    public function markAllAsRead(): RedirectResponse
    {
        Notification::where('user_id', Auth::id())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()->route('notifications.index')
            ->with('success', 'Semua notifikasi telah ditandai sebagai sudah dibaca.');
    }
}
