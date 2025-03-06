<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\User;

class ConnectedDevice extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables en masa.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'device_name',
        'device_type',
        'browser',
        'operating_system',
        'ip_address',
        'location',
        'user_agent',
        'device_token',
        'last_active_at',
        'is_current_device',
        'is_trusted',
    ];

    /**
     * Los atributos que deben convertirse a tipos nativos.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'last_active_at' => 'datetime',
        'is_current_device' => 'boolean',
        'is_trusted' => 'boolean',
    ];

    /**
     * Obtener el usuario al que pertenece este dispositivo.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtrar dispositivos activos (activos en los últimos 30 días).
     */
    public function scopeActive($query)
    {
        return $query->where('last_active_at', '>=', now()->subDays(30));
    }

    /**
     * Scope para filtrar dispositivos actuales.
     */
    public function scopeCurrent($query)
    {
        return $query->where('is_current_device', true);
    }

    /**
     * Scope para filtrar dispositivos confiables.
     */
    public function scopeTrusted($query)
    {
        return $query->where('is_trusted', true);
    }

    /**
     * Actualizar la marca de tiempo de última actividad.
     */
    public function markAsActive()
    {
        $this->update(['last_active_at' => now()]);
    }

    /**
     * Marcar como dispositivo actual.
     */
    public function markAsCurrent()
    {
        // Primero, desmarcar todos los dispositivos del usuario como actuales
        self::where('user_id', $this->user_id)
            ->where('id', '!=', $this->id)
            ->update(['is_current_device' => false]);
        
        // Marcar este dispositivo como actual
        $this->update(['is_current_device' => true]);
    }

    /**
     * Alternar el estado de confianza del dispositivo.
     */
    public function toggleTrust()
    {
        $this->update(['is_trusted' => !$this->is_trusted]);
    }
}
