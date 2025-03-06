<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerProfile extends Model
{
    use HasFactory;

    /**
     * الصفات التي يمكن تعبئتها جماعياً
     */
    protected $fillable = [
        'user_id',
        'birth_date',
        'gender',
        'address',
        'city',
        'country',
        'postal_code',
        'default_latitude',
        'default_longitude',
        'preferences',
        'is_guest',
        'referral_code',
        'referred_by',
        'orders_count',
        'total_spent',
    ];

    /**
     * الصفات التي يجب تحويلها
     */
    protected $casts = [
        'birth_date' => 'date',
        'default_latitude' => 'decimal:7',
        'default_longitude' => 'decimal:7',
        'is_guest' => 'boolean',
        'preferences' => 'json',
        'orders_count' => 'integer',
        'total_spent' => 'decimal:2',
    ];

    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * العلاقة مع المستخدم الذي قام بالإحالة
     */
    public function referrer()
    {
        return $this->belongsTo(User::class, 'referred_by');
    }

    /**
     * العلاقة مع العناوين
     */
    public function addresses()
    {
        return $this->hasMany(CustomerAddress::class, 'user_id', 'user_id');
    }
}
