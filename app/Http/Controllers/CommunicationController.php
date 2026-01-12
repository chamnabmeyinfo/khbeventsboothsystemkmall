<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommunicationController extends Controller
{
    /**
     * Display messages
     */
    public function index(Request $request)
    {
        $query = Message::where(function($q) {
                $q->where('to_user_id', Auth::id())
                  ->orWhere('from_user_id', Auth::id());
            })
            ->with(['fromUser', 'toUser', 'client']);

        // Filter by type
        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        // Filter by status
        if ($request->filled('status')) {
            if ($request->status == 'unread') {
                $query->where('to_user_id', Auth::id())->where('is_read', false);
            } elseif ($request->status == 'read') {
                $query->where('to_user_id', Auth::id())->where('is_read', true);
            }
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('subject', 'like', "%{$search}%")
                  ->orWhere('message', 'like', "%{$search}%");
            });
        }

        $messages = $query->latest()->paginate(20)->withQueryString();

        // Statistics
        $stats = [
            'total_messages' => Message::where('to_user_id', Auth::id())->orWhere('from_user_id', Auth::id())->count(),
            'unread_messages' => Message::where('to_user_id', Auth::id())->where('is_read', false)->count(),
            'sent_messages' => Message::where('from_user_id', Auth::id())->count(),
            'announcements' => Message::where('to_user_id', Auth::id())->where('type', 'announcement')->count(),
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

        Message::create([
            'from_user_id' => Auth::id(),
            'to_user_id' => $request->to_user_id,
            'client_id' => $request->client_id,
            'subject' => $request->subject,
            'message' => $request->message,
            'type' => 'message',
        ]);

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
        if ($message->to_user_id == Auth::id() && !$message->is_read) {
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

        // Send to all active users
        $users = User::where('status', 1)->get();
        
        foreach ($users as $user) {
            Message::create([
                'from_user_id' => Auth::id(),
                'to_user_id' => $user->id,
                'subject' => $request->subject,
                'message' => $request->message,
                'type' => 'announcement',
            ]);
        }

        return redirect()->route('communications.index')
            ->with('success', 'Announcement sent to all users');
    }
}

