<?php

namespace App\Http\Controllers;

use App\Models\Message;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class MessageController extends Controller
{
    public function sendMessage(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'receiver_id' => 'required|exists:users,id',
            'content' => 'required_without:media',
            'type' => 'in:text,image,voice',
            'media' => 'nullable|file|max:10240' // 10MB max
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $mediaPath = null;
        if ($request->hasFile('media')) {
            $mediaPath = $request->file('media')->store('messages', 'public');
        }

        $message = Message::create([
            'sender_id' => auth()->id(),
            'receiver_id' => $request->receiver_id,
            'content' => $request->content,
            'type' => $request->type ?? 'text',
            'status' => 'sent',
            'media_path' => $mediaPath
        ]);

        return response()->json($message, 201);
    }

    public function getMessages(Request $request, $receiverId)
    {
        $messages = Message::where(function($query) use ($receiverId) {
            $query->where('sender_id', auth()->id())
                  ->where('receiver_id', $receiverId);
        })->orWhere(function($query) use ($receiverId) {
            $query->where('sender_id', $receiverId)
                  ->where('receiver_id', auth()->id());
        })->orderBy('created_at', 'asc')->get();

        return response()->json($messages);
    }

    public function deleteMessage($messageId)
    {
        $message = Message::findOrFail($messageId);

        // Only sender can delete their message
        if ($message->sender_id !== auth()->id()) {
            return response()->json([
                'message' => 'Unauthorized'
            ], 403);
        }

        $message->update(['is_deleted' => true]);

        return response()->json([
            'message' => 'Message deleted successfully'
        ]);
    }
}
