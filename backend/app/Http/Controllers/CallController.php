<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Call;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class CallController extends Controller
{
    /**
     * Middleware to ensure user authentication
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Display the calls dashboard
     */
    public function index()
    {
        $recentCalls = Call::where('user_id', Auth::id())
            ->orWhere('recipient_id', Auth::id())
            ->with(['user', 'recipient'])
            ->latest()
            ->paginate(20);

        return view('calls.index', compact('recentCalls'));
    }

    /**
     * Initiate a new voice call
     */
    public function initiateCall(Request $request)
    {
        $request->validate([
            'recipient_id' => 'required|exists:users,id',
            'call_type' => 'in:voice,video'
        ]);

        $recipient = User::findOrFail($request->recipient_id);

        // Check if recipient is available for call
        if (!$recipient->isAvailableForCall()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Recipient is not available for call'
            ], 400);
        }

        $call = Call::create([
            'user_id' => Auth::id(),
            'recipient_id' => $recipient->id,
            'type' => $request->call_type ?? 'voice',
            'status' => 'initiated'
        ]);

        // Trigger call notification to recipient
        // This would typically involve WebSocket or push notification
        event(new CallInitiatedEvent($call));

        return response()->json([
            'status' => 'success',
            'call' => $call
        ]);
    }

    /**
     * Accept an incoming call
     */
    public function acceptCall($callId)
    {
        $call = Call::findOrFail($callId);

        // Ensure only the recipient can accept the call
        if ($call->recipient_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized call acceptance'
            ], 403);
        }

        $call->update([
            'status' => 'active',
            'accepted_at' => now()
        ]);

        // Trigger call acceptance notification
        event(new CallAcceptedEvent($call));

        return response()->json([
            'status' => 'success',
            'call' => $call
        ]);
    }

    /**
     * End an ongoing call
     */
    public function endCall($callId)
    {
        $call = Call::findOrFail($callId);

        // Ensure only call participants can end the call
        if ($call->user_id !== Auth::id() && $call->recipient_id !== Auth::id()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Unauthorized call termination'
            ], 403);
        }

        $call->update([
            'status' => 'ended',
            'ended_at' => now(),
            'duration' => now()->diffInSeconds($call->created_at)
        ]);

        // Trigger call ended notification
        event(new CallEndedEvent($call));

        return response()->json([
            'status' => 'success',
            'call' => $call
        ]);
    }
}
