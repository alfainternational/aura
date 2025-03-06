<?php

namespace App\Events;

use App\Models\User;
use App\Models\UserNotification;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class NotificationSentEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * المستخدم المستهدف
     *
     * @var User
     */
    public $user;

    /**
     * الإشعار المرسل
     *
     * @var UserNotification
     */
    public $notification;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param UserNotification $notification
     * @return void
     */
    public function __construct(User $user, UserNotification $notification)
    {
        $this->user = $user;
        $this->notification = $notification;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }
    
    /**
     * The event's broadcast name.
     *
     * @return string
     */
    public function broadcastAs()
    {
        return 'notification.sent';
    }
    
    /**
     * Get the data to broadcast.
     *
     * @return array
     */
    public function broadcastWith()
    {
        return [
            'id' => $this->notification->id,
            'title' => $this->notification->title,
            'message' => $this->notification->message,
            'type' => $this->notification->type,
            'icon' => $this->notification->icon,
            'action_url' => $this->notification->action_url,
            'created_at' => $this->notification->created_at->toIso8601String(),
        ];
    }
}
