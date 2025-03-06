<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Call extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'calls';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'recipient_id',
        'type',  // 'voice', 'video'
        'status', // 'initiated', 'active', 'ended', 'missed'
        'started_at',
        'ended_at',
        'duration'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
        'duration' => 'integer'
    ];

    /**
     * Relationship with initiating user
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relationship with recipient user
     */
    public function recipient(): BelongsTo
    {
        return $this->belongsTo(User::class, 'recipient_id');
    }

    /**
     * Scope for active calls
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for voice calls
     */
    public function scopeVoiceCalls($query)
    {
        return $query->where('type', 'voice');
    }

    /**
     * Scope for video calls
     */
    public function scopeVideoCalls($query)
    {
        return $query->where('type', 'video');
    }

    /**
     * Check if call is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    /**
     * Calculate call duration
     */
    public function calculateDuration()
    {
        if ($this->started_at && $this->ended_at) {
            $this->duration = $this->started_at->diffInSeconds($this->ended_at);
            $this->save();
        }
    }
}
