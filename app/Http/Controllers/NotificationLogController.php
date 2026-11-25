<?php

namespace App\Http\Controllers;

use App\Models\NotificationLog;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NotificationLogController extends Controller
{
    public function index(Request $request): View
    {
        $user = $request->user();

        $notifications = NotificationLog::forUser($user)
            ->with(['relatedItem', 'relatedClaim'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        $unreadCount = NotificationLog::forUser($user)
            ->whereNull('read_at')
            ->count();

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadCount' => $unreadCount,
        ]);
    }

    public function markRead(Request $request, NotificationLog $notification): RedirectResponse
    {
        $user = $request->user();

        if (! $user->isAdmin() && ! NotificationLog::forUser($user)->whereKey($notification->id)->exists()) {
            abort(403);
        }

        if ($notification->read_at === null) {
            $notification->update(['read_at' => now()]);
        }

        return redirect()
            ->back()
            ->with('status', __('app.flash.notification_read'));
    }

    public function markAllRead(Request $request): RedirectResponse
    {
        NotificationLog::forUser($request->user())
            ->whereNull('read_at')
            ->update(['read_at' => now()]);

        return redirect()
            ->back()
            ->with('status', __('app.flash.notifications_all_read'));
    }
}
