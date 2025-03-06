<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Vehicle extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'model',
        'year',
        'color',
        'plate_number',
        'image',
        'insurance_number',
        'insurance_expiry',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'year' => 'integer',
        'insurance_expiry' => 'date',
    ];

    /**
     * العلاقة مع ملف المندوب
     */
    public function messengerProfile()
    {
        return $this->hasOne(MessengerProfile::class);
    }
}
