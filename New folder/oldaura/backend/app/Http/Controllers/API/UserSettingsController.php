<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserSettingsController extends Controller
{
    /**
     * Obtener la configuración del usuario actual
     */
    public function getSettings(Request $request)
    {
        $user = $request->user();
        
        // Obtener configuración del usuario o crear valores por defecto
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                // Valores predeterminados
                'require_otp' => true,
                'otp_threshold' => config('aura.transactions.otp_threshold', 1000),
                'notification_preferences' => [
                    'email' => true,
                    'sms' => true,
                    'push' => true
                ],
                'ui_preferences' => [
                    'language' => 'ar',
                    'theme' => 'light',
                    'currency_display' => 'SDG'
                ]
            ]
        );
        
        return response()->json([
            'status' => 'success',
            'data' => $settings
        ]);
    }
    
    /**
     * Actualizar la configuración del usuario
     */
    public function updateSettings(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'require_otp' => 'boolean',
            'otp_threshold' => 'numeric|min:0',
            'notification_preferences' => 'sometimes|array',
            'notification_preferences.email' => 'boolean',
            'notification_preferences.sms' => 'boolean',
            'notification_preferences.push' => 'boolean',
            'ui_preferences' => 'sometimes|array',
            'ui_preferences.language' => 'string|in:ar,en',
            'ui_preferences.theme' => 'string|in:light,dark',
            'ui_preferences.currency_display' => 'string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Obtener o crear configuración
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'require_otp' => true,
                'otp_threshold' => config('aura.transactions.otp_threshold', 1000),
                'notification_preferences' => [
                    'email' => true,
                    'sms' => true,
                    'push' => true
                ],
                'ui_preferences' => [
                    'language' => 'ar',
                    'theme' => 'light',
                    'currency_display' => 'SDG'
                ]
            ]
        );
        
        // Actualizar configuración
        if ($request->has('require_otp')) {
            $settings->require_otp = $request->input('require_otp');
        }
        
        if ($request->has('otp_threshold')) {
            $settings->otp_threshold = $request->input('otp_threshold');
        }
        
        if ($request->has('notification_preferences')) {
            $settings->notification_preferences = array_merge(
                $settings->notification_preferences ?? [],
                $request->input('notification_preferences')
            );
        }
        
        if ($request->has('ui_preferences')) {
            $settings->ui_preferences = array_merge(
                $settings->ui_preferences ?? [],
                $request->input('ui_preferences')
            );
        }
        
        $settings->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Configuración actualizada con éxito',
            'data' => $settings
        ]);
    }
    
    /**
     * Actualizar configuración específica de OTP
     */
    public function updateOtpSettings(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'require_otp' => 'required|boolean',
            'otp_threshold' => 'required|numeric|min:0'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Obtener o crear configuración
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'require_otp' => true,
                'otp_threshold' => config('aura.transactions.otp_threshold', 1000)
            ]
        );
        
        // Actualizar configuración de OTP
        $settings->require_otp = $request->input('require_otp');
        $settings->otp_threshold = $request->input('otp_threshold');
        $settings->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Configuración de OTP actualizada',
            'data' => $settings
        ]);
    }
    
    /**
     * Actualizar preferencias de notificaciones
     */
    public function updateNotificationPreferences(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'email' => 'required|boolean',
            'sms' => 'required|boolean',
            'push' => 'required|boolean'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Obtener o crear configuración
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'notification_preferences' => [
                    'email' => true,
                    'sms' => true,
                    'push' => true
                ]
            ]
        );
        
        // Actualizar preferencias de notificaciones
        $settings->notification_preferences = [
            'email' => $request->input('email'),
            'sms' => $request->input('sms'),
            'push' => $request->input('push')
        ];
        
        $settings->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Preferencias de notificaciones actualizadas',
            'data' => $settings
        ]);
    }
    
    /**
     * Actualizar preferencias de interfaz
     */
    public function updateUiPreferences(Request $request)
    {
        $user = $request->user();
        
        $validator = Validator::make($request->all(), [
            'language' => 'required|string|in:ar,en',
            'theme' => 'required|string|in:light,dark',
            'currency_display' => 'required|string'
        ]);
        
        if ($validator->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => 'Error de validación',
                'errors' => $validator->errors()
            ], 422);
        }
        
        // Obtener o crear configuración
        $settings = UserSetting::firstOrCreate(
            ['user_id' => $user->id],
            [
                'ui_preferences' => [
                    'language' => 'ar',
                    'theme' => 'light',
                    'currency_display' => 'SDG'
                ]
            ]
        );
        
        // Actualizar preferencias de interfaz
        $settings->ui_preferences = [
            'language' => $request->input('language'),
            'theme' => $request->input('theme'),
            'currency_display' => $request->input('currency_display')
        ];
        
        $settings->save();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Preferencias de interfaz actualizadas',
            'data' => $settings
        ]);
    }
}
