<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SupervisorProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'department',
        'position',
        'rating',
        'managed_orders_count',
        'managed_messengers_count',
        'last_active_at',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'rating' => 'decimal:2',
        'managed_orders_count' => 'integer',
        'managed_messengers_count' => 'integer',
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
     * العلاقة مع المناطق المسؤول عنها
     */
    public function zones()
    {
        return $this->belongsToMany(Zone::class, 'supervisor_zones', 'supervisor_profile_id', 'zone_id')
            ->withTimestamps();
    }

    /**
     * العلاقة مع المناديب المشرف عليهم
     */
    public function messengers()
    {
        return $this->hasManyThrough(
            MessengerProfile::class,
            Zone::class,
            'id',
            'zone_id',
            'id',
            'id'
        );
    }
}
