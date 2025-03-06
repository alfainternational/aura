<?php

namespace App\Services;

use App\Models\User;
use App\Models\Call;
use Illuminate\Support\Facades\Log;

class CallService
{
    /**
     * Initiate a call between two users
     */
    public function initiateCall(User $caller, User $recipient, string $callType = 'voice')
    {
        // Check if users are available for call
        if (!$caller->isAvailableForCall() || !$recipient->isAvailableForCall()) {
            return [
                'status' => 'error',
                'message' => 'أحد المستخدمين غير متاح للمكالمة حاليًا'
            ];
        }

        // Create call record
        $call = Call::create([
            'user_id' => $caller->id,
            'recipient_id' => $recipient->id,
            'type' => $callType,
            'status' => 'initiated',
            'started_at' => now()
        ]);

        // Log call initiation
        Log::info("Call initiated", [
            'call_id' => $call->id,
            'caller_id' => $caller->id,
            'recipient_id' => $recipient->id,
            'type' => $callType
        ]);

        // Trigger notification or WebSocket event to recipient
        $this->notifyRecipient($call);

        return [
            'status' => 'success',
            'message' => 'تم بدء المكالمة',
            'call_id' => $call->id
        ];
    }

    /**
     * Accept an incoming call
     */
    public function acceptCall(Call $call, User $recipient)
    {
        // Validate call can be accepted
        if ($call->status !== 'initiated' || $call->recipient_id !== $recipient->id) {
            return [
                'status' => 'error',
                'message' => 'لا يمكن قبول المكالمة'
            ];
        }

        // Update call status
        $call->update([
            'status' => 'active'
        ]);

        // Log call acceptance
        Log::info("Call accepted", [
            'call_id' => $call->id,
            'recipient_id' => $recipient->id
        ]);

        return [
            'status' => 'success',
            'message' => 'تم قبول المكالمة',
            'call_id' => $call->id
        ];
    }

    /**
     * End an active call
     */
    public function endCall(Call $call)
    {
        // Only end active or initiated calls
        if (!in_array($call->status, ['active', 'initiated'])) {
            return [
                'status' => 'error',
                'message' => 'لا يمكن إنهاء المكالمة'
            ];
        }

        // Update call details
        $call->update([
            'status' => 'ended',
            'ended_at' => now()
        ]);

        // Calculate call duration
        $call->calculateDuration();

        // Log call end
        Log::info("Call ended", [
            'call_id' => $call->id,
            'duration' => $call->duration
        ]);

        return [
            'status' => 'success',
            'message' => 'تم إنهاء المكالمة',
            'duration' => $call->duration
        ];
    }

    /**
     * Notify recipient of incoming call
     * This would typically use WebSockets or push notifications
     */
    private function notifyRecipient(Call $call)
    {
        // Placeholder for WebSocket or push notification logic
        // In a real-world scenario, this would send a real-time notification
        // to the recipient's device or browser
        Log::info("Call notification sent", [
            'call_id' => $call->id,
            'recipient_id' => $call->recipient_id
        ]);
    }

    /**
     * Get user's call history
     */
    public function getUserCallHistory(User $user, int $limit = 20)
    {
        return Call::where(function($query) use ($user) {
            $query->where('user_id', $user->id)
                  ->orWhere('recipient_id', $user->id);
        })
        ->whereIn('status', ['ended', 'missed'])
        ->orderBy('ended_at', 'desc')
        ->limit($limit)
        ->get();
    }
}
