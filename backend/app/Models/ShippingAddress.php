<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ShippingAddress extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'name',
        'phone_number',
        'country',
        'city',
        'district',
        'street_address',
        'building_number',
        'apartment_number',
        'postal_code',
        'latitude',
        'longitude',
        'is_default',
        'additional_instructions'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_default' => 'boolean',
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7'
    ];

    /**
     * Boot method for generating unique identifier
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique identifier when creating
        static::creating(function ($address) {
            $address->uuid = Str::uuid();
        });
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Orders
     */
    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    /**
     * Scope for Default Address
     */
    public function scopeDefault($query)
    {
        return $query->where('is_default', true);
    }

    /**
     * Get Formatted Address
     */
    public function getFormattedAddressAttribute()
    {
        $addressParts = [
            $this->street_address,
            $this->building_number ? "Bldg: {$this->building_number}" : null,
            $this->apartment_number ? "Apt: {$this->apartment_number}" : null,
            $this->district,
            $this->city,
            $this->country,
            $this->postal_code
        ];

        return implode(', ', array_filter($addressParts));
    }

    /**
     * Get Short Address
     */
    public function getShortAddressAttribute()
    {
        return sprintf(
            "%s, %s, %s", 
            $this->street_address, 
            $this->city, 
            $this->country
        );
    }

    /**
     * Check if Address is in Sudan
     */
    public function isInSudanAttribute()
    {
        return strtolower($this->country) === 'sudan';
    }

    /**
     * Validate Address Completeness
     */
    public function isCompleteAttribute()
    {
        $requiredFields = [
            'name', 
            'phone_number', 
            'country', 
            'city', 
            'street_address'
        ];

        foreach ($requiredFields as $field) {
            if (empty($this->$field)) {
                return false;
            }
        }

        return true;
    }
}
