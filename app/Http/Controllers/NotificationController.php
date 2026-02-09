<?php

namespace App\Http\Controllers;

use App\Models\Notification;
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
}
