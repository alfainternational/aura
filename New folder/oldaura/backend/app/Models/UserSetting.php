<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserSetting extends Model
{
    use HasFactory;

    /**
     * Los atributos que son asignables masivamente.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'require_otp',
        'otp_threshold',
        'notification_preferences',
        'ui_preferences',
    ];

    /**
     * Los atributos que deben convertirse.
     *
     * @var array
     */
    protected $casts = [
        'require_otp' => 'boolean',
        'otp_threshold' => 'float',
        'notification_preferences' => 'array',
        'ui_preferences' => 'array',
    ];

    /**
     * Obtener el usuario propietario de la configuración.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
