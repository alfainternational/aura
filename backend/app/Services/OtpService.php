<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;
use App\Models\User;
use Carbon\Carbon;

class OtpService
{
    /**
     * Generar un código OTP para un usuario específico
     *
     * @param User $user
     * @param string $purpose El propósito del OTP (ej: 'transaction', 'login')
     * @param int $length Longitud del código OTP
     * @param int $expiry Tiempo de expiración en minutos
     * @return string El código OTP generado
     */
    public function generateOtp(User $user, string $purpose, int $length = 6, int $expiry = 15): string
    {
        // Generar un código OTP numérico
        $otp = '';
        for ($i = 0; $i < $length; $i++) {
            $otp .= mt_rand(0, 9);
        }
        
        // Almacenar el OTP en la caché con una clave única
        $cacheKey = $this->getOtpCacheKey($user->id, $purpose);
        
        $otpData = [
            'code' => $otp,
            'expires_at' => Carbon::now()->addMinutes($expiry),
            'attempts' => 0,
            'verified' => false,
        ];
        
        Cache::put($cacheKey, $otpData, $expiry * 60);
        
        return $otp;
    }
    
    /**
     * Verificar un código OTP para un usuario específico
     *
     * @param User $user
     * @param string $purpose El propósito del OTP
     * @param string $code El código OTP a verificar
     * @param int $maxAttempts Número máximo de intentos permitidos
     * @return bool Si el código OTP es válido
     */
    public function verifyOtp(User $user, string $purpose, string $code, int $maxAttempts = 3): bool
    {
        $cacheKey = $this->getOtpCacheKey($user->id, $purpose);
        $otpData = Cache::get($cacheKey);
        
        // Si no existe el OTP o ya ha sido verificado
        if (!$otpData || $otpData['verified']) {
            return false;
        }
        
        // Si el OTP ha expirado
        if (Carbon::now()->isAfter($otpData['expires_at'])) {
            Cache::forget($cacheKey);
            return false;
        }
        
        // Incrementar el contador de intentos
        $otpData['attempts']++;
        
        // Si se ha superado el número máximo de intentos
        if ($otpData['attempts'] > $maxAttempts) {
            Cache::forget($cacheKey);
            return false;
        }
        
        // Verificar el código
        if ($otpData['code'] === $code) {
            $otpData['verified'] = true;
            Cache::put($cacheKey, $otpData, Carbon::now()->diffInSeconds($otpData['expires_at']));
            return true;
        }
        
        // Actualizar el contador de intentos en la caché
        Cache::put($cacheKey, $otpData, Carbon::now()->diffInSeconds($otpData['expires_at']));
        
        return false;
    }
    
    /**
     * Invalidar un OTP existente
     *
     * @param int $userId
     * @param string $purpose
     * @return bool
     */
    public function invalidateOtp(int $userId, string $purpose): bool
    {
        $cacheKey = $this->getOtpCacheKey($userId, $purpose);
        return Cache::forget($cacheKey);
    }
    
    /**
     * Obtener información sobre un OTP
     *
     * @param int $userId
     * @param string $purpose
     * @return array|null
     */
    public function getOtpInfo(int $userId, string $purpose): ?array
    {
        $cacheKey = $this->getOtpCacheKey($userId, $purpose);
        return Cache::get($cacheKey);
    }
    
    /**
     * Obtener la clave de caché para un OTP
     *
     * @param int $userId
     * @param string $purpose
     * @return string
     */
    private function getOtpCacheKey(int $userId, string $purpose): string
    {
        return "otp_{$userId}_{$purpose}";
    }
    
    /**
     * Enviar el código OTP al usuario
     *
     * @param User $user
     * @param string $otp
     * @param string $purpose
     * @return bool
     */
    public function sendOtp(User $user, string $otp, string $purpose): bool
    {
        // Aquí implementaríamos el envío del OTP por SMS, email, etc.
        // Por ahora es una simulación que siempre retorna true
        
        $message = "Your verification code for {$purpose} is: {$otp}";
        
        // Podemos enviar por diferentes canales según las preferencias del usuario
        if ($user->phone) {
            // Enviar por SMS
            // $this->sendSms($user->phone, $message);
        }
        
        if ($user->email) {
            // Enviar por email
            // Mail::to($user->email)->send(new OtpMail($otp, $purpose));
        }
        
        // Registrar el envío para fines de auditoría
        \Log::info("OTP sent to user {$user->id} for {$purpose}: {$otp}");
        
        return true;
    }
}
