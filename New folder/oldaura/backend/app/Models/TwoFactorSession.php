<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TwoFactorSession extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'session_id',
        'ip_address',
        'user_agent',
        'verified_at',
        'expires_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'expires_at' => 'datetime',
    ];

    /**
     * Relación con el usuario
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener sesiones activas
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('expires_at', '>', now());
    }

    /**
     * Verificar si la sesión ha expirado
     *
     * @return bool
     */
    public function hasExpired()
    {
        return $this->expires_at->isPast();
    }

    /**
     * Extender la duración de la sesión
     *
     * @param int $days
     * @return bool
     */
    public function extend($days = 30)
    {
        $this->expires_at = now()->addDays($days);
        return $this->save();
    }

    /**
     * Revocar la sesión
     *
     * @return bool
     */
    public function revoke()
    {
        $this->expires_at = now()->subMinute();
        return $this->save();
    }
}
