<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use App\Models\Book;
use App\Models\Booth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Get unread notifications count (API)
     */
    public function unreadCount()
    {
        $count = Notification::where('user_id', Auth::id())
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }

    /**
     * Get all notifications
     */
    public function index()
    {
        $notifications = Notification::where('user_id', Auth::id())
            ->with(['booking', 'client'])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Mark notification as read
     */
    public function markAsRead($id)
    {
        $notification = Notification::where('user_id', Auth::id())
            ->findOrFail($id);
        
        $notification->markAsRead();

        return response()->json(['success' => true]);
    }

    /**
     * Mark all as read
     */
    public function markAllAsRead()
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
     * Create notification (helper method)
     */
    public static function create($type, $title, $message, $userId = null, $clientId = null, $bookingId = null)
    {
        return Notification::create([
            'type' => $type,
            'title' => $title,
            'message' => $message,
            'user_id' => $userId ?? Auth::id(),
            'client_id' => $clientId,
            'booking_id' => $bookingId,
        ]);
    }
}

