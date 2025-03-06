<?php

namespace App\Services\TwoFactor\Channels;

use App\Services\TwoFactor\Contracts\TwoFactorChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WhatsAppChannel implements TwoFactorChannelInterface
{
    /**
     * Configuración del servicio WhatsApp.
     *
     * @var array
     */
    protected $config;

    /**
     * Crear una nueva instancia del canal.
     *
     * @param array $config
     * @return void
     */
    public function __construct(array $config = null)
    {
        $this->config = $config ?: config('services.whatsapp', []);
    }

    /**
     * Enviar código de verificación por WhatsApp.
     *
     * @param string $recipient Número de teléfono del destinatario
     * @param string $code Código de verificación
     * @param array $options Opciones adicionales
     * @return bool
     */
    public function send(string $recipient, string $code, array $options = []): bool
    {
        try {
            // Formatear el número de teléfono si es necesario
            $recipient = $this->formatPhoneNumber($recipient);
            
            // Determinar el proveedor de WhatsApp a utilizar
            $provider = $options['provider'] ?? $this->config['default_provider'] ?? 'twilio';
            
            // Enviar mensaje según el proveedor
            switch ($provider) {
                case 'twilio':
                    return $this->sendViaTwilio($recipient, $code, $options);
                
                case 'meta':
                    return $this->sendViaMeta($recipient, $code, $options);
                
                default:
                    Log::error("Proveedor de WhatsApp no soportado: {$provider}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Error al enviar mensaje de WhatsApp: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar mensaje de WhatsApp a través de Twilio.
     *
     * @param string $recipient
     * @param string $code
     * @param array $options
     * @return bool
     */
    protected function sendViaTwilio(string $recipient, string $code, array $options = []): bool
    {
        $accountSid = $this->config['twilio']['account_sid'];
        $authToken = $this->config['twilio']['auth_token'];
        $fromNumber = $this->config['twilio']['from_number'];
        
        $message = $options['message'] ?? "Tu código de verificación es: {$code}";
        
        $response = Http::withBasicAuth($accountSid, $authToken)
            ->asForm()
            ->post("https://api.twilio.com/2010-04-01/Accounts/{$accountSid}/Messages.json", [
                'From' => "whatsapp:{$fromNumber}",
                'To' => "whatsapp:{$recipient}",
                'Body' => $message,
            ]);
        
        if ($response->successful()) {
            Log::info("Mensaje de WhatsApp enviado exitosamente a {$recipient} vía Twilio");
            return true;
        }
        
        Log::error("Error al enviar mensaje de WhatsApp vía Twilio: " . $response->body());
        return false;
    }

    /**
     * Enviar mensaje de WhatsApp a través de la API de Meta (WhatsApp Business).
     *
     * @param string $recipient
     * @param string $code
     * @param array $options
     * @return bool
     */
    protected function sendViaMeta(string $recipient, string $code, array $options = []): bool
    {
        $accessToken = $this->config['meta']['access_token'];
        $phoneNumberId = $this->config['meta']['phone_number_id'];
        $apiVersion = $this->config['meta']['api_version'] ?? 'v17.0';
        
        $templateName = $options['template_name'] ?? $this->config['meta']['template_name'] ?? 'verification_code';
        
        $response = Http::withToken($accessToken)
            ->post("https://graph.facebook.com/{$apiVersion}/{$phoneNumberId}/messages", [
                'messaging_product' => 'whatsapp',
                'to' => $recipient,
                'type' => 'template',
                'template' => [
                    'name' => $templateName,
                    'language' => [
                        'code' => $options['language'] ?? 'es',
                    ],
                    'components' => [
                        [
                            'type' => 'body',
                            'parameters' => [
                                [
                                    'type' => 'text',
                                    'text' => $code,
                                ],
                            ],
                        ],
                    ],
                ],
            ]);
        
        if ($response->successful()) {
            Log::info("Mensaje de WhatsApp enviado exitosamente a {$recipient} vía Meta API");
            return true;
        }
        
        Log::error("Error al enviar mensaje de WhatsApp vía Meta API: " . $response->body());
        return false;
    }

    /**
     * Formatear el número de teléfono para asegurar compatibilidad con los proveedores.
     *
     * @param string $phoneNumber
     * @return string
     */
    protected function formatPhoneNumber(string $phoneNumber): string
    {
        // Eliminar espacios, guiones y paréntesis
        $phoneNumber = preg_replace('/\s+|-|\(|\)/', '', $phoneNumber);
        
        // Asegurar que el número tenga el formato internacional
        if (!str_starts_with($phoneNumber, '+')) {
            $defaultCountryCode = $this->config['default_country_code'] ?? '+1';
            $phoneNumber = $defaultCountryCode . ltrim($phoneNumber, '0');
        }
        
        return $phoneNumber;
    }

    /**
     * Verificar si el canal está configurado correctamente.
     *
     * @return bool
     */
    public function isConfigured(): bool
    {
        $provider = $this->config['default_provider'] ?? null;
        
        if ($provider === 'twilio') {
            return !empty($this->config['twilio']['account_sid']) && 
                   !empty($this->config['twilio']['auth_token']) && 
                   !empty($this->config['twilio']['from_number']);
        }
        
        if ($provider === 'meta') {
            return !empty($this->config['meta']['access_token']) && 
                   !empty($this->config['meta']['phone_number_id']);
        }
        
        return false;
    }

    /**
     * Obtener el nombre del canal.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'WhatsApp';
    }

    /**
     * Obtener el identificador del canal.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'whatsapp';
    }
}
