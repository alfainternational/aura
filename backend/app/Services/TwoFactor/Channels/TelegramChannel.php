<?php

namespace App\Services\TwoFactor\Channels;

use App\Services\TwoFactor\Contracts\TwoFactorChannelInterface;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramChannel implements TwoFactorChannelInterface
{
    /**
     * Configuración del servicio Telegram.
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
        $this->config = $config ?: config('services.telegram', []);
    }

    /**
     * Enviar código de verificación por Telegram.
     *
     * @param string $recipient ID de chat de Telegram
     * @param string $code Código de verificación
     * @param array $options Opciones adicionales
     * @return bool
     */
    public function send(string $recipient, string $code, array $options = []): bool
    {
        try {
            $botToken = $this->config['bot_token'];
            $message = $options['message'] ?? "Tu código de verificación es: *{$code}*";
            
            $response = Http::post("https://api.telegram.org/bot{$botToken}/sendMessage", [
                'chat_id' => $recipient,
                'text' => $message,
                'parse_mode' => 'Markdown',
            ]);
            
            if ($response->successful() && $response->json('ok') === true) {
                Log::info("Código de verificación enviado por Telegram a {$recipient}");
                return true;
            }
            
            Log::error("Error al enviar mensaje por Telegram: " . $response->body());
            return false;
        } catch (\Exception $e) {
            Log::error("Error al enviar código por Telegram: " . $e->getMessage());
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
        return !empty($this->config['bot_token']);
    }

    /**
     * Obtener el nombre del canal.
     *
     * @return string
     */
    public function getName(): string
    {
        return 'Telegram';
    }

    /**
     * Obtener el identificador del canal.
     *
     * @return string
     */
    public function getIdentifier(): string
    {
        return 'telegram';
    }
}
