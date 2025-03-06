<?php

namespace App\Http\Controllers;

use App\Models\VoiceCall;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class VoiceCallController extends Controller
{
    public function initiateCall(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'call_type' => 'in:audio,video'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $call = VoiceCall::create([
            'caller_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'start_time' => now(),
            'status' => 'initiated',
            'call_type' => $request->call_type ?? 'audio'
        ]);

        return response()->json($call, 201);
    }

    public function endCall($callId)
    {
        $call = VoiceCall::findOrFail($callId);

        if ($call->caller_id !== auth()->id() && $call->receiver_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $call->update([
            'end_time' => now(),
            'duration' => now()->diffInSeconds($call->start_time),
            'status' => 'completed'
        ]);

        return response()->json($call);
    }

    public function getCallHistory()
    {
        $calls = VoiceCall::where('caller_id', auth()->id())
            ->orWhere('receiver_id', auth()->id())
            ->orderBy('start_time', 'desc')
            ->get();

        return response()->json($calls);
    }
}
