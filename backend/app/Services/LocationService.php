<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use App\Models\Location;
use App\Models\User;

class LocationService
{
    /**
     * Validate user location using OpenStreetMap Nominatim
     */
    public function validateUserLocation(float $latitude, float $longitude): array
    {
        try {
            // Use OpenStreetMap Nominatim for reverse geocoding
            $response = Http::get('https://nominatim.openstreetmap.org/reverse', [
                'format' => 'json',
                'lat' => $latitude,
                'lon' => $longitude,
                'zoom' => 10,
                'addressdetails' => 1
            ]);

            if (!$response->successful()) {
                return [
                    'status' => 'error',
                    'message' => 'فشل في تحديد الموقع'
                ];
            }

            $data = $response->json();
            $country = $data['address']['country'] ?? null;
            $city = $data['address']['city'] ?? $data['address']['town'] ?? $data['address']['village'] ?? null;

            // Check if country is supported
            $supportedCountries = ['السودان', 'السعودية'];
            if (!in_array($country, $supportedCountries)) {
                return [
                    'status' => 'error',
                    'message' => 'عذرًا، التسجيل متاح فقط في السودان والسعودية'
                ];
            }

            return [
                'status' => 'success',
                'message' => 'تم التحقق من الموقع بنجاح',
                'country' => $country,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude
            ];
        } catch (\Exception $e) {
            return [
                'status' => 'error',
                'message' => 'حدث خطأ أثناء التحقق من الموقع'
            ];
        }
    }

    /**
     * Get supported locations
     */
    public function getSupportedLocations(): array
    {
        return [
            'countries' => ['السودان', 'السعودية'],
            'cities' => $this->getCitiesForSupportedCountries()
        ];
    }

    /**
     * Get cities for supported countries
     */
    private function getCitiesForSupportedCountries(): array
    {
        return [
            'السودان' => [
                'الخرطوم', 
                'أم درمان', 
                'بحري', 
                'الجزيرة', 
                'كسلا'
            ],
            'السعودية' => [
                'الرياض', 
                'جدة', 
                'مكة المكرمة', 
                'المدينة المنورة', 
                'الدمام'
            ]
        ];
    }

    /**
     * Update user location in database
     */
    public function updateUserLocation(User $user, string $country, string $city, ?float $latitude = null, ?float $longitude = null): bool
    {
        // Create or update location record
        $location = Location::updateOrCreate(
            [
                'user_id' => $user->id
            ],
            [
                'country' => $country,
                'city' => $city,
                'latitude' => $latitude,
                'longitude' => $longitude,
                'status' => 'active'
            ]
        );

        // Update user's location reference
        $user->update([
            'country' => $country,
            'city' => $city
        ]);

        return true;
    }
}
