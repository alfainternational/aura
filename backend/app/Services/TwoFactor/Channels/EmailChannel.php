<?php

namespace App\Services\TwoFactor\Channels;

use App\Services\TwoFactor\Contracts\TwoFactorChannelInterface;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use App\Mail\TwoFactorCode;

class EmailChannel implements TwoFactorChannelInterface
{
    /**
     * Enviar código de verificación por email.
     *
     * @param string $recipient Dirección de email del destinatario
     * @param string $code Código de verificación
     * @param array $options Opciones adicionales
     * @return bool
     */
    public function send(string $recipient, string $code, array $options = []): bool
    {
        try {
            $userName = $options['user_name'] ?? null;
            
            Mail::to($recipient)->send(new TwoFactorCode($code, $userName));
            
            Log::info("Código de verificación enviado por email a {$recipient}");
            return true;
        } catch (\Exception $e) {
            Log::error("Error al enviar código por email: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Verificar si el canal está configurado correctamente.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        // El email siempre está configurado si Laravel está configurado correctamente
        return !empty(config('mail.mailers'));
    }

    /**
     * Obtener el nombre del canal.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Email';
    }

    /**
     * Obtener el identificador del canal.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'email';
    }
}
