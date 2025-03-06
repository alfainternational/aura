<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Group extends Model
{
    protected $fillable = [
        'name', 
        'description', 
        'creator_id', 
        'type', 
        'max_members',
        'is_private'
    ];

    protected $casts = [
        'is_private' => 'boolean'
    ];

    // Relationship: Group belongs to a creator
    public function creator()
    {
        return $this->belongsTo(User::class, 'creator_id');
    }

    // Relationship: Group has many messages
    public function messages()
    {
        return $this->hasMany(Message::class);
    }

    // Relationship: Group has many users
    public function users()
    {
        return $this->belongsToMany(User::class, 'group_users');
    }
}
