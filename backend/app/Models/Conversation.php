<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Conversation extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'conversations';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'type',  // 'single' or 'group'
        'title',  // Optional title for group conversations
        'last_message_id'
    ];

    /**
     * Relationship with messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }

    /**
     * Relationship with participants (users)
     */
    public function participants(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'conversation_participants');
    }

    /**
     * Get the last message in the conversation
     */
    public function lastMessage()
    {
        return $this->belongsTo(Message::class, 'last_message_id');
    }

    /**
     * Scope for single conversations
     */
    public function scopeSingleConversations($query)
    {
        return $query->where('type', 'single');
    }

    /**
     * Scope for group conversations
     */
    public function scopeGroupConversations($query)
    {
        return $query->where('type', 'group');
    }

    /**
     * Check if a user is a participant in the conversation
     */
    public function isParticipant($userId): bool
    {
        return $this->participants()->where('user_id', $userId)->exists();
    }
}
