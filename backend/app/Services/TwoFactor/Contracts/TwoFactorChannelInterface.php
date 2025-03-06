<?php

namespace App\Services\TwoFactor\Contracts;

interface TwoFactorChannelInterface
{
    /**
     * Enviar código de verificación a través del canal.
     *
     * @param string $recipient El destinatario (teléfono, email, ID de chat, etc.)
     * @param string $code El código de verificación
     * @param array $options Opciones adicionales específicas del canal
     * @return bool
     */
    public function send(string $recipient, string $code, array $options = []): bool;
    
    /**
     * Verificar si el canal está configurado correctamente.
     *
     * @return bool
     */
    public function isConfigured(): bool;
    
    /**
     * Obtener el nombre del canal.
     *
     * @return string
     */
    public function getName(): string;
    
    /**
     * Obtener el identificador del canal.
     *
     * @return string
     */
    public function getIdentifier(): string;
}
