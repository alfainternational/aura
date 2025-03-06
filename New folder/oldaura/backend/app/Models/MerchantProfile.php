<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MerchantProfile extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'business_name',
        'business_type',
        'business_description',
        'business_logo',
        'business_cover',
        'business_address',
        'business_phone',
        'business_email',
        'business_website',
        'is_verified',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_verified' => 'boolean',
    ];

    /**
     * Get the user that owns the merchant profile.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
