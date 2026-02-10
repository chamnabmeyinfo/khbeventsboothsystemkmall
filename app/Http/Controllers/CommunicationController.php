<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Message;
use App\Models\User;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class CommunicationController extends Controller
{
    /**
     * Display messages
     */
    public function index(Request $request)
    {
        $userId = (int) auth()->user()->id;
        $query = Message::where(function ($q) use ($userId) {
            $q->where('to_user_id', $userId)
                ->orWhere('from_user_id', $userId);
        })
            ->with(['fromUser', 'toUser', 'client']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'unread') {
                $query->where('to_user_id', $userId)->where('is_read', false);
            } elseif ($request->status == 'read') {
                $query->where('to_user_id', $userId)->where('is_read', true);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                    ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_messages' => Message::where('to_user_id', $userId)->orWhere('from_user_id', $userId)->count(),
            'unread_messages' => Message::where('to_user_id', $userId)->where('is_read', false)->count(),
            'sent_messages' => Message::where('from_user_id', $userId)->count(),
            'announcements' => Message::where('to_user_id', $userId)->where('type', 'announcement')->count(),
        ];

        return view('communications.index', compact('messages', 'stats'));
    }

    /**
     * Send message
     */
    public function send(Request $request)
    {
        $request->validate([
            'to_user_id' => 'nullable|exists:user,id',
            'client_id' => 'nullable|exists:client,id',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $userId = (int) auth()->user()->id;

        $message = Message::create([
            'from_user_id' => $userId,
            'to_user_id' => $request->to_user_id ? (int) $request->to_user_id : null,
            'client_id' => $request->client_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => 'message',
        ]);

        if ($request->to_user_id && (int) $request->to_user_id !== $userId) {
            $fromName = Auth::user()->username ?? 'Staff';
            $title = 'New message: '.Str::limit($request->subject, 50);
            $body = $fromName.' wrote: '.Str::limit($request->message, 100);
            $link = route('communications.show', $message->id);
            NotificationService::create('system', $title, $body, (int) $request->to_user_id, null, null, $link, null, $message, $userId);
        }

        return redirect()->route('communications.index')
            ->with('success', 'Message sent successfully');
    }

    /**
     * Show compose form
     */
    public function create()
    {
        $users = User::where('status', 1)->get();
        $clients = Client::all();

        return view('communications.create', compact('users', 'clients'));
    }

    /**
     * View message
     */
    public function show($id)
    {
        $message = Message::with(['fromUser', 'toUser', 'client'])->findOrFail($id);

        // Mark as read if recipient
        if ($message->to_user_id == (int) auth()->user()->id && ! $message->is_read) {
            $message->markAsRead();
        }

        return view('communications.show', compact('message'));
    }

    /**
     * Create announcement
     */
    public function announcement(Request $request)
    {
        $request->validate([
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);

        $users = User::where('status', 1)->get();
        $title = 'Announcement: '.Str::limit($request->subject, 50);
        $body = Str::limit($request->message, 120);
        $actorId = (int) auth()->user()->id;

        foreach ($users as $user) {
            $message = Message::create([
                'from_user_id' => $actorId,
                'to_user_id' => (int) $user->id,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => 'announcement',
            ]);

            if ((int) $user->id !== $actorId) {
                NotificationService::create('system', $title, $body, (int) $user->id, null, null, route('communications.show', $message->id), null, $message, $actorId);
            }
        }

        return redirect()->route('communications.index')
            ->with('success', 'Announcement sent to all users');
    }

    /**
     * Get unread messages count (API for badge / polling)
     */
    public function unreadCount()
    {
        $count = Message::where('to_user_id', (int) auth()->user()->id)
            ->where('is_read', false)
            ->count();

        return response()->json(['count' => $count]);
    }
}
