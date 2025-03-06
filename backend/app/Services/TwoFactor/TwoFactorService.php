<?php

namespace App\Services\TwoFactor;

use Illuminate\Contracts\Foundation\Application;
use App\Models\TwoFactorMethod;
use App\Models\User;
use Illuminate\Support\Str;
use Exception;

class TwoFactorService
{
    /**
     * La instancia de la aplicación.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Los canales disponibles.
     *
     * @var array
     */
    protected $channels = [
        'sms' => 'two-factor.channel.sms',
        'email' => 'two-factor.channel.email',
        'telegram' => 'two-factor.channel.telegram',
        'whatsapp' => 'two-factor.channel.whatsapp',
        'app' => null, // El canal de app no envía códigos, usa TOTP
    ];

    /**
     * Crear una nueva instancia del servicio.
     *
     * @param  \Illuminate\Contracts\Foundation\Application  $app
     * @return void
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }

    /**
     * Generar un código de verificación.
     *
     * @param int $length Longitud del código
     * @return string
     */
    public function generateCode(int $length = 6): string
    {
        return (string) mt_rand(pow(10, $length - 1), pow(10, $length) - 1);
    }

    /**
     * Generar un secreto para TOTP.
     *
     * @return string
     */
    public function generateTOTPSecret(): string
    {
        return Str::random(32);
    }

    /**
     * Generar códigos de recuperación.
     *
     * @param int $count Número de códigos a generar
     * @return array
     */
    public function generateRecoveryCodes(int $count = 8): array
    {
        $codes = [];
        for ($i = 0; $i < $count; $i++) {
            $codes[] = Str::random(10);
        }
        return $codes;
    }

    /**
     * Enviar un código de verificación a través del canal especificado.
     *
     * @param string $channel El canal a utilizar
     * @param string $recipient El destinatario
     * @param string $code El código de verificación
     * @param array $options Opciones adicionales
     * @return bool
     * @throws \Exception
     */
    public function sendCode(string $channel, string $recipient, string $code, array $options = []): bool
    {
        if (!isset($this->channels[$channel]) || $channel === 'app') {
            throw new Exception("Canal no válido o no soporta envío de códigos: {$channel}");
        }

        $channelInstance = $this->app->make($this->channels[$channel]);
        return $channelInstance->send($recipient, $code, $options);
    }

    /**
     * Verificar un código TOTP.
     *
     * @param string $secret El secreto TOTP
     * @param string $code El código ingresado
     * @return bool
     */
    public function verifyTOTP(string $secret, string $code): bool
    {
        // Implementación básica de TOTP
        // En producción, usar una biblioteca como OTPHP
        $timeSlice = floor(time() / 30);
        
        // Verificar el código actual y los adyacentes (para compensar desincronización)
        for ($i = -1; $i <= 1; $i++) {
            $calculatedCode = $this->calculateTOTP($secret, $timeSlice + $i);
            if (hash_equals($calculatedCode, $code)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Calcular un código TOTP para un momento específico.
     *
     * @param string $secret El secreto TOTP
     * @param int $timeSlice La porción de tiempo
     * @return string
     */
    protected function calculateTOTP(string $secret, int $timeSlice): string
    {
        // Implementación simplificada - en producción usar una biblioteca TOTP
        $key = $this->base32Decode($secret);
        $msg = pack('N*', 0) . pack('N*', $timeSlice);
        $hash = hash_hmac('sha1', $msg, $key, true);
        $offset = ord($hash[19]) & 0xf;
        $code = (
            ((ord($hash[$offset + 0]) & 0x7f) << 24) |
            ((ord($hash[$offset + 1]) & 0xff) << 16) |
            ((ord($hash[$offset + 2]) & 0xff) << 8) |
            (ord($hash[$offset + 3]) & 0xff)
        ) % 1000000;
        
        return str_pad($code, 6, '0', STR_PAD_LEFT);
    }
    
    /**
     * Decodificar una cadena base32.
     *
     * @param string $secret
     * @return string
     */
    protected function base32Decode(string $secret): string
    {
        $base32chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ234567';
        $secret = strtoupper($secret);
        $n = 0;
        $j = 0;
        $binary = '';
        
        for ($i = 0; $i < strlen($secret); $i++) {
            $n = $n << 5;
            $n = $n + strpos($base32chars, $secret[$i]);
            $j = $j + 5;
            
            if ($j >= 8) {
                $j = $j - 8;
                $binary .= chr(($n & (0xFF << $j)) >> $j);
            }
        }
        
        return $binary;
    }
    
    /**
     * Registrar un nuevo método de 2FA para un usuario.
     *
     * @param User $user El usuario
     * @param string $methodType El tipo de método
     * @param string $identifier El identificador (teléfono, email, etc.)
     * @param string $secret El secreto o código
     * @param bool $isPrimary Si es el método principal
     * @return TwoFactorMethod
     */
    public function registerMethod(User $user, string $methodType, string $identifier, string $secret, bool $isPrimary = false): TwoFactorMethod
    {
        // Si se marca como primario, desmarcar otros métodos primarios
        if ($isPrimary) {
            $user->twoFactorMethods()->where('is_primary', true)->update(['is_primary' => false]);
        }
        
        return $user->twoFactorMethods()->create([
            'method_type' => $methodType,
            'identifier' => $identifier,
            'secret' => $secret,
            'is_primary' => $isPrimary,
            'is_active' => true,
            'last_used_at' => now(),
        ]);
    }
    
    /**
     * Obtener todos los canales disponibles.
     *
     * @return array
     */
    public function getAvailableChannels(): array
    {
        $available = [];
        
        foreach ($this->channels as $name => $binding) {
            if ($name === 'app' || ($binding && $this->app->make($binding)->isConfigured())) {
                $available[] = $name;
            }
        }
        
        return $available;
    }
}
