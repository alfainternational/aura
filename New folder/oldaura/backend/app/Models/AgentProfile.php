<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AgentProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'agent_id',
        'area_of_operation',
        'agent_type',
        'identification_number',
        'identification_document',
        'vehicle_type',
        'vehicle_number',
        'license_number',
        'is_active',
        'is_verified',
        'last_active_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_verified' => 'boolean',
        'last_active_at' => 'datetime',
    ];

    /**
     * Get the user that owns the agent profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
