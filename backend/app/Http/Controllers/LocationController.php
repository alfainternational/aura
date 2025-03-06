<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\LocationService;

class LocationController extends Controller
{
    protected $locationService;

    /**
     * Constructor with dependency injection
     */
    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * Validate user location
     */
    public function validateLocation(Request $request)
    {
        $request->validate([
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        $validationResult = $this->locationService->validateUserLocation(
            $request->input('latitude'), 
            $request->input('longitude')
        );

        return response()->json([
            'status' => $validationResult['status'],
            'message' => $validationResult['message'],
            'country' => $validationResult['country'] ?? null,
            'city' => $validationResult['city'] ?? null
        ]);
    }

    /**
     * Get list of supported locations
     */
    public function getSupportedLocations()
    {
        $supportedLocations = $this->locationService->getSupportedLocations();

        return response()->json([
            'countries' => $supportedLocations['countries'],
            'cities' => $supportedLocations['cities']
        ]);
    }
}
