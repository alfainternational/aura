<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone_number' => 'required|unique:users|regex:/^[0-9]{10}$/',
            'country_code' => 'required|in:249,966', // Sudan, Saudi Arabia
            'password' => 'required|min:8|confirmed',
            'latitude' => 'nullable|numeric',
            'longitude' => 'nullable|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        $user = User::create([
            'name' => $request->name,
            'phone_number' => $request->phone_number,
            'country_code' => $request->country_code,
            'password' => Hash::make($request->password),
            'latitude' => $request->latitude,
            'longitude' => $request->longitude,
            'user_type' => 'customer', // Default user type
            'is_verified' => false
        ]);

        // Generate token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token
        ], 201);
    }

    public function login(Request $request)
    {
        $credentials = $request->only('phone_number', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $token = $user->createToken('auth_token')->plainTextToken;

            return response()->json([
                'user' => $user,
                'token' => $token
            ]);
        }

        return response()->json([
            'message' => 'Invalid credentials'
        ], 401);
    }

    public function verifyLocation(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'errors' => $validator->errors()
            ], 422);
        }

        // Use OpenStreetMap Nominatim for reverse geocoding
        $url = "https://nominatim.openstreetmap.org/reverse?format=json&lat={$request->latitude}&lon={$request->longitude}";
        $response = json_decode(file_get_contents($url), true);

        $allowedCountries = ['Sudan', 'Saudi Arabia'];
        $country = $response['address']['country'] ?? null;

        if (!in_array($country, $allowedCountries)) {
            return response()->json([
                'message' => 'Registration not allowed from this location'
            ], 403);
        }

        return response()->json([
            'country' => $country,
            'city' => $response['address']['city'] ?? $response['address']['town'] ?? null,
            'valid' => true
        ]);
    }
}
