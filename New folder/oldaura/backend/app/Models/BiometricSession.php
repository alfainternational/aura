<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BiometricSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_id',
        'device_name',
        'credential_id',
        'public_key',
        'device_info',
        'last_used_at',
        'is_active',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'device_info' => 'json',
        'last_used_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the biometric session.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Update the last used timestamp.
     */
    public function updateLastUsed()
    {
        $this->update([
            'last_used_at' => now(),
        ]);
    }

    /**
     * Deactivate this biometric session.
     */
    public function deactivate()
    {
        $this->update([
            'is_active' => false,
        ]);
    }
}
