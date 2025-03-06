<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Country;
use App\Models\City;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class LocationController extends Controller
{
    /**
     * تحديث موقع المستخدم
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function updateLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric|between:-90,90',
            'longitude' => 'required|numeric|between:-180,180',
            'country_code' => 'sometimes|string|size:2',
            'city_id' => 'sometimes|exists:cities,id',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'بيانات الموقع غير صحيحة',
                'errors' => $validator->errors()
            ], 422);
        }

        $user = $request->user();
        $latitude = $request->latitude;
        $longitude = $request->longitude;
        
        // البحث عن الدولة والمدينة بناءً على الإحداثيات
        $countryId = null;
        $cityId = $request->city_id;
        
        if ($request->has('country_code')) {
            $country = Country::where('code', $request->country_code)->where('is_active', true)->first();
            if ($country) {
                $countryId = $country->id;
                
                // إذا لم يتم تحديد المدينة، نحاول العثور على أقرب مدينة
                if (!$cityId) {
                    $city = City::where('country_id', $countryId)
                        ->where('is_active', true)
                        ->whereNotNull('latitude')
                        ->whereNotNull('longitude')
                        ->orderByRaw("(POW(latitude - ?, 2) + POW(longitude - ?, 2))", [$latitude, $longitude])
                        ->first();
                    
                    if ($city) {
                        $cityId = $city->id;
                    }
                }
            }
        }
        
        try {
            $user->updateLocation($latitude, $longitude, $countryId, $cityId);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تحديث موقعك بنجاح',
                'data' => [
                    'latitude' => $user->latitude,
                    'longitude' => $user->longitude,
                    'country' => $user->country ? $user->country->name : null,
                    'city' => $user->city ? $user->city->name : null,
                ]
            ]);
        } catch (\Exception $e) {
            Log::error('خطأ في تحديث موقع المستخدم: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تحديث موقعك. الرجاء المحاولة مرة أخرى.'
            ], 500);
        }
    }
    
    /**
     * الحصول على قائمة الدول المدعومة
     *
     * @return \Illuminate\Http\Response
     */
    public function getSupportedCountries()
    {
        $countries = Country::where('is_active', true)->get();
        
        return response()->json([
            'success' => true,
            'data' => $countries
        ]);
    }
    
    /**
     * الحصول على قائمة المدن حسب الدولة
     *
     * @param  string  $countryCode
     * @return \Illuminate\Http\Response
     */
    public function getCitiesByCountry($countryCode)
    {
        $country = Country::where('code', $countryCode)->first();
        
        if (!$country) {
            return response()->json([
                'success' => false,
                'message' => 'Country not found'
            ], 404);
        }
        
        $cities = City::where('country_id', $country->id)->get();
        
        return response()->json([
            'success' => true,
            'data' => $cities
        ]);
    }
    
    /**
     * Get nearby agents based on user location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearbyAgents()
    {
        $user = auth()->user();
        
        // Verify if user has location data
        if (!$user || !$user->latitude || !$user->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'User location not available',
                'data' => []
            ]);
        }
        
        // Find nearby agents (users with type = agent)
        // In real implementation, this would use database spatial queries or a distance calculation
        $nearbyAgents = User::where('type', 'agent')
            ->where('is_active', true)
            ->where(function($query) use ($user) {
                // Only include agents if they have location data
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude');
                
                // If user has a city_id, prioritize agents in the same city
                if ($user->city_id) {
                    $query->where('city_id', $user->city_id)
                          ->orWhereRaw('ST_Distance_Sphere(
                              point(longitude, latitude),
                              point(?, ?)
                          ) <= ?', [$user->longitude, $user->latitude, 50000]); // 50 km radius
                } else {
                    // Otherwise just use radius search
                    $query->whereRaw('ST_Distance_Sphere(
                        point(longitude, latitude),
                        point(?, ?)
                    ) <= ?', [$user->longitude, $user->latitude, 50000]); // 50 km radius
                }
            })
            ->get();
        
        // Calculate distance for each agent and add it to the response
        $agents = $nearbyAgents->map(function($agent) use ($user) {
            // Calculate distance in kilometers
            $distance = round($this->calculateDistance(
                $user->latitude, 
                $user->longitude, 
                $agent->latitude, 
                $agent->longitude
            ), 1);
            
            return [
                'id' => $agent->id,
                'name' => $agent->name,
                'phone_number' => $agent->phone_number,
                'address' => $agent->address ?? '',
                'latitude' => $agent->latitude,
                'longitude' => $agent->longitude,
                'distance' => $distance
            ];
        });
        
        // Sort by distance
        $agents = $agents->sortBy('distance')->values()->all();
        
        return response()->json([
            'success' => true,
            'data' => $agents
        ]);
    }

    /**
     * Get nearby stores based on user location
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getNearbyStores()
    {
        $user = auth()->user();
        
        // Verify if user has location data
        if (!$user || !$user->latitude || !$user->longitude) {
            return response()->json([
                'success' => false,
                'message' => 'User location not available',
                'data' => []
            ]);
        }
        
        // Find nearby stores (users with type = merchant)
        // In real implementation, this would use database spatial queries or a distance calculation
        $nearbyStores = User::where('type', 'merchant')
            ->where('is_active', true)
            ->where(function($query) use ($user) {
                // Only include stores if they have location data
                $query->whereNotNull('latitude')
                      ->whereNotNull('longitude');
                
                // If user has a city_id, prioritize stores in the same city
                if ($user->city_id) {
                    $query->where('city_id', $user->city_id)
                          ->orWhereRaw('ST_Distance_Sphere(
                              point(longitude, latitude),
                              point(?, ?)
                          ) <= ?', [$user->longitude, $user->latitude, 50000]); // 50 km radius
                } else {
                    // Otherwise just use radius search
                    $query->whereRaw('ST_Distance_Sphere(
                        point(longitude, latitude),
                        point(?, ?)
                    ) <= ?', [$user->longitude, $user->latitude, 50000]); // 50 km radius
                }
            })
            ->get();
        
        // Calculate distance for each store and add it to the response
        $stores = $nearbyStores->map(function($store) use ($user) {
            // Calculate distance in kilometers
            $distance = round($this->calculateDistance(
                $user->latitude, 
                $user->longitude, 
                $store->latitude, 
                $store->longitude
            ), 1);
            
            return [
                'id' => $store->id,
                'name' => $store->name,
                'phone_number' => $store->phone_number,
                'address' => $store->address ?? '',
                'latitude' => $store->latitude,
                'longitude' => $store->longitude,
                'distance' => $distance
            ];
        });
        
        // Sort by distance
        $stores = $stores->sortBy('distance')->values()->all();
        
        return response()->json([
            'success' => true,
            'data' => $stores
        ]);
    }

    /**
     * Calculate distance between two points
     *
     * @param float $lat1
     * @param float $lon1
     * @param float $lat2
     * @param float $lon2
     * @return float
     */
    private function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        }
        
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        
        // Convert miles to kilometers
        return $miles * 1.609344;
    }
}
