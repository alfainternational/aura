<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Country extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'code',
        'dial_code',
        'currency',
        'currency_symbol',
        'flag',
        'active',
        'allowed_for_registration',
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'active' => 'boolean',
        'allowed_for_registration' => 'boolean',
    ];
    
    /**
     * Get accessor for legacy 'is_active' attribute
     */
    public function getIsActiveAttribute()
    {
        return $this->active;
    }

    /**
     * Get accessor for legacy 'allow_registration' attribute
     */
    public function getAllowRegistrationAttribute()
    {
        return $this->allowed_for_registration;
    }

    /**
     * Set mutator for legacy 'is_active' attribute
     */
    public function setIsActiveAttribute($value)
    {
        $this->attributes['active'] = $value;
    }

    /**
     * Set mutator for legacy 'allow_registration' attribute
     */
    public function setAllowRegistrationAttribute($value)
    {
        $this->attributes['allowed_for_registration'] = $value;
    }
    
    /**
     * Get the cities for the country.
     */
    public function cities()
    {
        return $this->hasMany(City::class);
    }
    
    /**
     * Get the users for the country.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Check if this country is Sudan.
     */
    public function isSudan()
    {
        return $this->code === 'SD';
    }
    
    /**
     * التحقق من إمكانية التسجيل في هذه الدولة
     */
    public function isRegistrationAllowed()
    {
        return $this->active && $this->allowed_for_registration;
    }
    
    /**
     * الحصول على قائمة الدول التي تسمح بالتسجيل
     */
    public static function getRegistrationAllowedCountries()
    {
        return self::where('active', true)
            ->where('allowed_for_registration', true)
            ->orderBy('name')
            ->get();
    }
}