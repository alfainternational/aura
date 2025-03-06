<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MessengerProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'national_id',
        'id_document',
        'driving_license',
        'license_document',
        'vehicle_id',
        'zone_id',
        'address',
        'city',
        'status',
        'is_online',
        'delivery_preference',
        'work_hours',
        'current_latitude',
        'current_longitude',
        'completed_deliveries',
        'rating',
        'last_active_at',
        'reference_code',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_online' => 'boolean',
        'current_latitude' => 'decimal:7',
        'current_longitude' => 'decimal:7',
        'completed_deliveries' => 'integer',
        'rating' => 'decimal:2',
        'last_active_at' => 'datetime',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع المركبة
     */
    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    /**
     * العلاقة مع المنطقة
     */
    public function zone()
    {
        return $this->belongsTo(Zone::class);
    }
}
