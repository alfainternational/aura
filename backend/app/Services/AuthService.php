<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class AuthService
{
    /**
     * Register a new user with geolocation validation
     */
    public function registerUser(array $data)
    {
        // Validate location first
        $locationService = app(LocationService::class);
        $locationValidation = $locationService->validateUserLocation(
            $data['latitude'], 
            $data['longitude']
        );

        if ($locationValidation['status'] !== 'success') {
            return [
                'status' => 'error',
                'message' => $locationValidation['message']
            ];
        }

        // Create user
        $user = User::create([
            'name' => $data['name'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'user_type' => $data['user_type'] ?? 'customer',
            'country' => $locationValidation['country'],
            'city' => $locationValidation['city'],
            'status' => 'active',
            'verification_status' => 'pending'
        ]);

        // Update user location
        $locationService->updateUserLocation(
            $user, 
            $locationValidation['country'], 
            $locationValidation['city'], 
            $data['latitude'], 
            $data['longitude']
        );

        // Log registration
        Log::info('User registered', [
            'user_id' => $user->id,
            'email' => $user->email,
            'country' => $user->country
        ]);

        // Send verification email
        $user->sendEmailVerificationNotification();

        return [
            'status' => 'success',
            'message' => 'تم التسجيل بنجاح',
            'user' => $user
        ];
    }

    /**
     * Authenticate user with enhanced security
     */
    public function authenticateUser(string $email, string $password, bool $rememberMe = false)
    {
        $user = User::where('email', $email)->first();

        if (!$user || !Hash::check($password, $user->password)) {
            Log::warning('Login attempt failed', [
                'email' => $email
            ]);

            return [
                'status' => 'error',
                'message' => 'البريد الإلكتروني أو كلمة المرور غير صحيحة'
            ];
        }

        // Check user status
        if ($user->status !== 'active') {
            return [
                'status' => 'error',
                'message' => 'الحساب غير نشط'
            ];
        }

        // Generate authentication token
        $token = $user->createToken('auth_token', 
            $rememberMe ? ['long-lived'] : ['standard']
        )->plainTextToken;

        Log::info('User logged in', [
            'user_id' => $user->id,
            'email' => $user->email
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تسجيل الدخول بنجاح',
            'user' => $user,
            'token' => $token
        ];
    }

    /**
     * Reset user password
     */
    public function resetPassword(User $user, string $newPassword)
    {
        $user->password = Hash::make($newPassword);
        $user->setRememberToken(Str::random(60));
        $user->save();

        Log::info('Password reset', [
            'user_id' => $user->id
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تغيير كلمة المرور بنجاح'
        ];
    }

    /**
     * Update user profile
     */
    public function updateProfile(User $user, array $data)
    {
        $updateData = collect($data)->filter()->toArray();

        // Prevent changing critical fields
        unset(
            $updateData['email'], 
            $updateData['phone'], 
            $updateData['user_type']
        );

        $user->fill($updateData);
        $user->save();

        Log::info('User profile updated', [
            'user_id' => $user->id,
            'updated_fields' => array_keys($updateData)
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تحديث الملف الشخصي بنجاح',
            'user' => $user
        ];
    }

    /**
     * Deactivate user account
     */
    public function deactivateAccount(User $user)
    {
        $user->status = 'inactive';
        $user->save();

        // Revoke all tokens
        $user->tokens()->delete();

        Log::info('User account deactivated', [
            'user_id' => $user->id
        ]);

        return [
            'status' => 'success',
            'message' => 'تم تعطيل الحساب بنجاح'
        ];
    }
}
