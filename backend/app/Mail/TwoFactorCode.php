<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class TwoFactorCode extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * El código de verificación.
     *
     * @var string
     */
    public $code;

    /**
     * El nombre del usuario.
     *
     * @var string|null
     */
    public $userName;

    /**
     * Crear una nueva instancia del mensaje.
     *
     * @param string $code
     * @param string|null $userName
     * @return void
     */
    public function __construct(string $code, ?string $userName = null)
    {
        $this->code = $code;
        $this->userName = $userName;
    }

    /**
     * Construir el mensaje.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject('Código de verificación para tu cuenta')
                    ->markdown('emails.auth.two-factor-code');
    }
}
