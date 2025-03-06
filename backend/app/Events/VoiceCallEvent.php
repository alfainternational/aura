<?php

namespace App\Events;

use App\Models\VoiceCall;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class VoiceCallEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * المكالمة الصوتية
     *
     * @var VoiceCall
     */
    public $voiceCall;
    
    /**
     * نوع الحدث (started, joined, rejected, ended)
     *
     * @var string
     */
    public $eventType;
    
    /**
     * معرف المستخدم الذي قام بالإجراء
     *
     * @var int
     */
    public $userId;

    /**
     * Create a new event instance.
     *
     * @param VoiceCall $voiceCall
     * @param string $eventType
     * @param int $userId
     * @return void
     */
    public function __construct(VoiceCall $voiceCall, string $eventType, int $userId)
    {
        $this->voiceCall = $voiceCall;
        $this->eventType = $eventType;
        $this->userId = $userId;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];
        
        // إرسال الحدث إلى جميع المشاركين في المكالمة
        foreach ($this->voiceCall->participants as $participant) {
            $channels[] = new PrivateChannel('user.' . $participant->user_id);
        }
        
        return $channels;
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'voice.call.' . $this->eventType;
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        $participants = [];
        
        foreach ($this->voiceCall->participants as $participant) {
            $participants[] = [
                'user_id' => $participant->user_id,
                'status' => $participant->status,
                'is_muted' => $participant->is_muted,
                'joined_at' => $participant->joined_at ? $participant->joined_at->toIso8601String() : null,
                'left_at' => $participant->left_at ? $participant->left_at->toIso8601String() : null,
                'user' => [
                    'id' => $participant->user->id,
                    'name' => $participant->user->name,
                    'avatar' => $participant->user->avatar ? asset('storage/' . $participant->user->avatar) : null,
                ],
            ];
        }
        
        return [
            'id' => $this->voiceCall->id,
            'uuid' => $this->voiceCall->uuid,
            'caller_id' => $this->voiceCall->caller_id,
            'is_group' => $this->voiceCall->is_group,
            'status' => $this->voiceCall->status,
            'started_at' => $this->voiceCall->started_at->toIso8601String(),
            'ended_at' => $this->voiceCall->ended_at ? $this->voiceCall->ended_at->toIso8601String() : null,
            'event_type' => $this->eventType,
            'user_id' => $this->userId,
            'participants' => $participants,
        ];
    }
}
