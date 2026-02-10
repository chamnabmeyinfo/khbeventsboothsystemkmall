<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\PushSubscription;
use App\Services\PushSender;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count (API)
     */
    public function unreadCount(): JsonResponse
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get recent notifications for dropdown (API)
     */
    public function recent(Request $request): JsonResponse
    {
        $limit = min((int) $request->input('limit', 10), 20);
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['actor', 'booking', 'client'])
            ->orderBy('created_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'notifications' => $notifications->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'type_label' => $n->type_label,
                'icon' => $n->icon,
                'title' => $n->title,
                'message' => \Illuminate\Support\Str::limit($n->message, 80),
                'link' => $n->link,
                'is_read' => $n->is_read,
                'actor_name' => $n->actor?->username,
                'created_at' => $n->created_at->diffForHumans(),
            ]),
        ]);
    }

    /**
     * Get all notifications (full page)
     */
    public function index(): View
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['actor', 'booking', 'client', 'activityLog'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read. If link present and request wants redirect, redirect to link.
     */
    public function markAsRead(Request $request, $id): JsonResponse|RedirectResponse
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);

        $notification->markAsRead();

        if ($request->boolean('redirect') && $notification->link) {
            return redirect($notification->link);
        }

        return response()->json(['success' => true, 'link' => $notification->link]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead(): JsonResponse
    {
        Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->update([
                'is_read' => true,
                'read_at' => now(),
            ]);

        return response()->json(['success' => true]);
    }

    /**
     * Save browser push subscription for the current user (Web Push).
     */
    public function pushSubscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'endpoint' => 'required|string|max:1024',
            'keys' => 'required|array',
            'keys.p256dh' => 'required|string|max:256',
            'keys.auth' => 'required|string|max:256',
            'contentEncoding' => 'nullable|string|in:aesgcm,aes128gcm',
        ]);

        $endpointHash = hash('sha256', $validated['endpoint']);
        PushSubscription::updateOrCreate(
            [
                'user_id' => Auth::id(),
                'endpoint_hash' => $endpointHash,
            ],
            [
                'endpoint' => $validated['endpoint'],
                'public_key' => $validated['keys']['p256dh'],
                'auth_token' => $validated['keys']['auth'],
                'content_encoding' => $validated['contentEncoding'] ?? 'aesgcm',
            ]
        );

        return response()->json(['success' => true]);
    }

    /**
     * Send a test push notification to the current user (for testing).
     */
    public function sendTestPush(Request $request): JsonResponse
    {
        if (! PushSender::isPushEnabled()) {
            return response()->json(['success' => false, 'message' => 'Push notifications are not enabled or VAPID keys are missing.'], 400);
        }

        $user = Auth::user();
        $subs = PushSubscription::where('user_id', $user->id)->count();
        if ($subs === 0) {
            return response()->json(['success' => false, 'message' => 'No push subscription. Enable push in your browser first (e.g. click "Enable push notifications" on the Notifications page).'], 400);
        }

        PushSender::sendToUser(
            $user->id,
            'Test notification',
            'If you see this, push notifications are working!',
            route('notifications.index')
        );

        return response()->json(['success' => true, 'message' => 'Test push sent. Check your browser or system tray.']);
    }
}
