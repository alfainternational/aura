<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    use HasFactory;
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'country_id',
        'is_active',
        'latitude',
        'longitude'
    ];
    
    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'is_active' => 'boolean',
        'latitude' => 'float',
        'longitude' => 'float',
    ];
    
    /**
     * Get the country that owns the city.
     */
    public function country()
    {
        return $this->belongsTo(Country::class);
    }
    
    /**
     * Get the users for the city.
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }
    
    /**
     * Calculate distance between this city and coordinates.
     * 
     * @param float $latitude
     * @param float $longitude
     * @return float Distance in kilometers
     */
    public function distanceTo($latitude, $longitude)
    {
        if (!$this->latitude || !$this->longitude) {
            return null;
        }
        
        // Convert from degrees to radians
        $latFrom = deg2rad($this->latitude);
        $lonFrom = deg2rad($this->longitude);
        $latTo = deg2rad($latitude);
        $lonTo = deg2rad($longitude);
        
        $latDelta = $latTo - $latFrom;
        $lonDelta = $lonTo - $lonFrom;
        
        $angle = 2 * asin(sqrt(pow(sin($latDelta / 2), 2) + cos($latFrom) * cos($latTo) * pow(sin($lonDelta / 2), 2)));
        
        // Earth radius in kilometers
        $earthRadius = 6371;
        
        return $angle * $earthRadius;
    }
}
