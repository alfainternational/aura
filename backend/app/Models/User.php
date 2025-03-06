<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class User extends Authenticatable implements MustVerifyEmail
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 
        'phone', 
        'email', 
        'password', 
        'user_type',
        'country', 
        'city', 
        'profile_picture',
        'verification_status',
        'status'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password', 
        'remember_token'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed'
    ];

    /**
     * Relationship with Location
     */
    public function location(): HasOne
    {
        return $this->hasOne(Location::class);
    }

    /**
     * Relationship with Identity Verifications
     */
    public function identityVerifications(): HasMany
    {
        return $this->hasMany(IdentityVerification::class);
    }

    /**
     * Relationship with Conversations
     */
    public function conversations(): BelongsToMany
    {
        return $this->belongsToMany(Conversation::class, 'conversation_participants');
    }

    /**
     * Relationship with Messages
     */
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class, 'sender_id');
    }

    /**
     * Relationship with Calls (initiated)
     */
    public function initiatedCalls(): HasMany
    {
        return $this->hasMany(Call::class, 'user_id');
    }

    /**
     * Relationship with Calls (received)
     */
    public function receivedCalls(): HasMany
    {
        return $this->hasMany(Call::class, 'recipient_id');
    }

    /**
     * Get recent contacts
     */
    public function recentContacts()
    {
        return User::whereHas('messages', function($query) {
            $query->where('sender_id', $this->id)
                  ->orWhere('conversation_id', 
                      $this->conversations()->pluck('conversations.id')
                  );
        })->distinct()->take(10);
    }

    /**
     * Get unread messages
     */
    public function unreadMessages()
    {
        return Message::whereHas('conversation', function($query) {
            $query->whereHas('participants', function($subQuery) {
                $subQuery->where('user_id', $this->id);
            });
        })->where('status', '!=', 'read');
    }

    /**
     * Get pending calls
     */
    public function pendingCalls()
    {
        return Call::where(function($query) {
            $query->where('user_id', $this->id)
                  ->orWhere('recipient_id', $this->id);
        })->whereIn('status', ['initiated', 'ringing']);
    }

    /**
     * Check if user is available for calls
     */
    public function isAvailableForCall(): bool
    {
        return $this->status === 'active' && 
               $this->pendingCalls()->count() === 0;
    }

    /**
     * Scope for active users
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for users by type
     */
    public function scopeOfType($query, $type)
    {
        return $query->where('user_type', $type);
    }
}
