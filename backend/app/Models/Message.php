<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Message extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'messages';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'conversation_id',
        'sender_id',
        'content',
        'type',  // 'text', 'image', 'voice'
        'status', // 'sent', 'delivered', 'read'
        'metadata'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'metadata' => 'array'
    ];

    /**
     * Relationship with conversation
     */
    public function conversation(): BelongsTo
    {
        return $this->belongsTo(Conversation::class);
    }

    /**
     * Relationship with sender (user)
     */
    public function sender(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    /**
     * Scope for unread messages
     */
    public function scopeUnread($query)
    {
        return $query->where('status', '!=', 'read');
    }

    /**
     * Scope for text messages
     */
    public function scopeTextMessages($query)
    {
        return $query->where('type', 'text');
    }

    /**
     * Scope for image messages
     */
    public function scopeImageMessages($query)
    {
        return $query->where('type', 'image');
    }

    /**
     * Mark message as delivered
     */
    public function markAsDelivered()
    {
        $this->update(['status' => 'delivered']);
    }

    /**
     * Mark message as read
     */
    public function markAsRead()
    {
        $this->update(['status' => 'read']);
    }

    /**
     * Check if message is unread
     */
    public function isUnread(): bool
    {
        return $this->status !== 'read';
    }
}
