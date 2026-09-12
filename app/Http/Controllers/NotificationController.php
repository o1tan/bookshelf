<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\View\View;

class NotificationController extends Controller
{
    public function __invoke(Request $request): View
    {
        $notifications = $request->user()
            ->notifications()
            ->latest()
            ->paginate(10);

        return view(
            'notifications.index',
            compact('notifications')
        );
    }

    public function markAsRead(
        Request $request,
        string $notificationId
    ): RedirectResponse {
        $notification = DatabaseNotification::findOrFail(
            $notificationId
        );

        abort_unless(
            $notification->notifiable_id === $request->user()->id
                && $notification->notifiable_type ===
                    $request->user()::class,
            403
        );

        $notification->markAsRead();

        return redirect()
            ->route('notifications.index')
            ->with('success', '通知を既読にしました。');
    }
}
