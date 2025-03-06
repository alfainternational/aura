<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;

class MessagingController extends Controller
{
    /**
     * Middleware to ensure user authentication
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the main messaging dashboard
     */
    public function index()
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->orWhere('recipient_id', Auth::id())
            ->with(['lastMessage', 'participants'])
            ->latest('updated_at')
            ->paginate(20);

        return view('messaging.index', compact('conversations'));
    }

    /**
     * List user conversations
     */
    public function conversations()
    {
        $conversations = Conversation::where('user_id', Auth::id())
            ->orWhere('recipient_id', Auth::id())
            ->with(['lastMessage', 'participants'])
            ->latest('updated_at')
            ->get();

        return response()->json($conversations);
    }

    /**
     * Show specific conversation messages
     */
    public function conversation($id)
    {
        $conversation = Conversation::findOrFail($id);
        
        // Ensure user is part of the conversation
        if (!$conversation->isParticipant(Auth::id())) {
            abort(403, 'Unauthorized access');
        }

        $messages = $conversation->messages()
            ->with('sender')
            ->latest()
            ->paginate(50);

        return view('messaging.conversation', compact('conversation', 'messages'));
    }

    /**
     * Send a new message
     */
    public function sendMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'content' => 'required|string|max:5000',
            'type' => 'in:text,image'
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'sender_id' => Auth::id(),
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'status' => 'sent'
        ]);

        // Update conversation timestamp
        $conversation = Conversation::find($request->conversation_id);
        $conversation->touch();

        return response()->json([
            'message' => $message,
            'status' => 'success'
        ]);
    }

    /**
     * Delete a specific message
     */
    public function deleteMessage($id)
    {
        $message = Message::findOrFail($id);

        // Ensure only the sender can delete their own message
        if ($message->sender_id !== Auth::id()) {
            abort(403, 'Unauthorized deletion');
        }

        $message->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Message deleted successfully'
        ]);
    }
}
