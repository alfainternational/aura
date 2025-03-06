<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class CustomerAddress extends Model
{
    use HasFactory;

    /**
     * الصفات التي يمكن تعبئتها جماعياً
     */
    protected $fillable = [
        'user_id',
        'label',
        'address_type',
        'address',
        'apartment',
        'street',
        'city',
        'state',
        'country',
        'postal_code',
        'latitude',
        'longitude',
        'notes',
        'is_default',
    ];

    /**
     * الصفات التي يجب تحويلها
     */
    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'is_default' => 'boolean',
    ];
    
    /**
     * العلاقة مع المستخدم
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
