<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VoiceCall extends Model
{
    protected $fillable = [
        'caller_id', 
        'receiver_id', 
        'start_time', 
        'end_time', 
        'duration', 
        'status',
        'call_type'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime'
    ];

    // Relationship: Call belongs to a caller
    public function caller()
    {
        return $this->belongsTo(User::class, 'caller_id');
    }

    // Relationship: Call belongs to a receiver
    public function receiver()
    {
        return $this->belongsTo(User::class, 'receiver_id');
    }

    // Scope for filtering active calls
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
