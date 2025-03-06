<?php

namespace App\Services\TwoFactor\Channels;

use App\Services\TwoFactor\Contracts\TwoFactorChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SmsChannel implements TwoFactorChannelInterface
{
    /**
     * Configuración del servicio SMS.
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
        $this->config = $config ?: config('services.sms', []);
    }

    /**
     * Enviar código de verificación por SMS.
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
            
            // Determinar el proveedor de SMS a utilizar
            $provider = $options['provider'] ?? $this->config['default_provider'] ?? 'twilio';
            
            // Enviar SMS según el proveedor
            switch ($provider) {
                case 'twilio':
                    return $this->sendViaTwilio($recipient, $code, $options);
                
                case 'vonage':
                    return $this->sendViaVonage($recipient, $code, $options);
                
                default:
                    Log::error("Proveedor de SMS no soportado: {$provider}");
                    return false;
            }
        } catch (\Exception $e) {
            Log::error("Error al enviar SMS: " . $e->getMessage());
            return false;
        }
    }

    /**
     * Enviar SMS a través de Twilio.
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
                'From' => $fromNumber,
                'To' => $recipient,
                'Body' => $message,
            ]);
        
        if ($response->successful()) {
            Log::info("SMS enviado exitosamente a {$recipient} vía Twilio");
            return true;
        }
        
        Log::error("Error al enviar SMS vía Twilio: " . $response->body());
        return false;
    }

    /**
     * Enviar SMS a través de Vonage (anteriormente Nexmo).
     *
     * @param string $recipient
     * @param string $code
     * @param array $options
     * @return bool
     */
    protected function sendViaVonage(string $recipient, string $code, array $options = []): bool
    {
        $apiKey = $this->config['vonage']['api_key'];
        $apiSecret = $this->config['vonage']['api_secret'];
        $fromName = $this->config['vonage']['from_name'];
        
        $message = $options['message'] ?? "Tu código de verificación es: {$code}";
        
        $response = Http::post('https://rest.nexmo.com/sms/json', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'from' => $fromName,
            'to' => $recipient,
            'text' => $message,
        ]);
        
        if ($response->successful() && isset($response['messages'][0]['status']) && $response['messages'][0]['status'] == 0) {
            Log::info("SMS enviado exitosamente a {$recipient} vía Vonage");
            return true;
        }
        
        Log::error("Error al enviar SMS vía Vonage: " . $response->body());
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
        
        if ($provider === 'vonage') {
            return !empty($this->config['vonage']['api_key']) && 
                   !empty($this->config['vonage']['api_secret']) && 
                   !empty($this->config['vonage']['from_name']);
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
        return 'SMS';
    }

    /**
     * Obtener el identificador del canal.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'sms';
    }
}
