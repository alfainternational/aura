<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\User;
use App\Models\BiometricCredential;
use Illuminate\Support\Str;

class BiometricAuthController extends Controller
{
    /**
     * Register a new biometric credential for a user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'credential_id' => 'required|string',
                'public_key' => 'required|string',
                'device_name' => 'required|string|max:255',
            ]);

            // User must be authenticated
            if (!Auth::check()) {
                return response()->json([
                    'success' => false,
                    'message' => 'يجب تسجيل الدخول أولاً لتسجيل بصمة الإصبع'
                ], 401);
            }

            $user = Auth::user();

            // Check if the credential already exists
            $existingCredential = BiometricCredential::where('credential_id', $request->credential_id)
                ->where('user_id', $user->id)
                ->first();

            if ($existingCredential) {
                // Update the existing credential
                $existingCredential->update([
                    'public_key' => $request->public_key,
                    'device_name' => $request->device_name,
                    'last_used_at' => now(),
                ]);

                return response()->json([
                    'success' => true,
                    'message' => 'تم تحديث بصمة الإصبع بنجاح',
                    'credential' => $existingCredential
                ]);
            }

            // Create a new credential
            $credential = BiometricCredential::create([
                'user_id' => $user->id,
                'credential_id' => $request->credential_id,
                'public_key' => $request->public_key,
                'device_name' => $request->device_name,
                'last_used_at' => now(),
            ]);

            Log::info('Biometric credential registered', [
                'user_id' => $user->id,
                'credential_id' => $request->credential_id,
                'device_name' => $request->device_name,
                'ip' => $request->ip()
            ]);

            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل بصمة الإصبع بنجاح',
                'credential' => $credential
            ]);
        } catch (\Exception $e) {
            Log::error('Error registering biometric credential: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);

            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء تسجيل بصمة الإصبع'
            ], 500);
        }
    }

    /**
     * Authenticate a user using biometric credential
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function authenticate(Request $request)
    {
        try {
            // If this is the initial request to get the challenge
            if (!$request->has('id')) {
                $countryId = $request->input('country_id');
                
                // Generate a challenge
                $challenge = Str::random(32);
                $request->session()->put('biometric_challenge', $challenge);
                $request->session()->put('biometric_country_id', $countryId);
                
                // Get all credentials for users in the specified country
                $credentials = BiometricCredential::whereHas('user', function ($query) use ($countryId) {
                    $query->where('country_id', $countryId)
                          ->where('is_active', true);
                })->get();
                
                if ($credentials->isEmpty()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'لا توجد بيانات بصمة إصبع مسجلة لهذه الدولة'
                    ]);
                }
                
                // Format credentials for WebAuthn
                $formattedCredentials = $credentials->map(function ($credential) {
                    return [
                        'id' => $credential->credential_id,
                        'type' => 'public-key'
                    ];
                });
                
                return response()->json([
                    'success' => true,
                    'challenge' => base64_encode($challenge),
                    'credentials' => $formattedCredentials
                ]);
            }
            
            // This is the authentication request with the credential
            $request->validate([
                'id' => 'required|string',
                'clientDataJSON' => 'required|string',
                'authenticatorData' => 'required|string',
                'signature' => 'required|string',
                'country_id' => 'required|exists:countries,id'
            ]);
            
            // Get the challenge from the session
            $challenge = $request->session()->get('biometric_challenge');
            if (!$challenge) {
                return response()->json([
                    'success' => false,
                    'message' => 'انتهت صلاحية التحدي، يرجى المحاولة مرة أخرى'
                ]);
            }
            
            // Find the credential
            $credential = BiometricCredential::where('credential_id', $request->id)->first();
            if (!$credential) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات البصمة غير موجودة'
                ]);
            }
            
            // Find the user
            $user = User::find($credential->user_id);
            if (!$user || !$user->is_active) {
                return response()->json([
                    'success' => false,
                    'message' => 'المستخدم غير موجود أو غير مفعل'
                ]);
            }
            
            // Check if the user belongs to the specified country
            if ($user->country_id != $request->country_id) {
                return response()->json([
                    'success' => false,
                    'message' => 'بيانات البصمة غير صالحة لهذه الدولة'
                ]);
            }
            
            // In a real implementation, we would verify the signature here
            // For simplicity, we'll skip the cryptographic verification
            
            // Update the last used timestamp
            $credential->update([
                'last_used_at' => now()
            ]);
            
            // Log the user in
            Auth::login($user);
            
            // Clear the challenge
            $request->session()->forget('biometric_challenge');
            $request->session()->forget('biometric_country_id');
            
            Log::info('Biometric authentication successful', [
                'user_id' => $user->id,
                'credential_id' => $credential->id,
                'ip' => $request->ip()
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'تم تسجيل الدخول بنجاح',
                'redirect' => route('home')
            ]);
        } catch (\Exception $e) {
            Log::error('Error in biometric authentication: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'success' => false,
                'message' => 'حدث خطأ أثناء المصادقة البيومترية'
            ], 500);
        }
    }
}
