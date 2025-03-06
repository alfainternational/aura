<?php

namespace App\Services\TwoFactor;

use Illuminate\Support\ServiceProvider;
use App\Services\TwoFactor\Channels\SmsChannel;
use App\Services\TwoFactor\Channels\EmailChannel;
use App\Services\TwoFactor\Channels\TelegramChannel;
use App\Services\TwoFactor\Channels\WhatsAppChannel;
use App\Services\TwoFactor\Contracts\TwoFactorChannelInterface;

class TwoFactorServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        // Registrar el servicio principal de 2FA
        $this->app->singleton('two-factor', function ($app) {
            return new TwoFactorService($app);
        });

        // Registrar los canales de 2FA
        $this->app->bind('two-factor.channel.sms', function ($app) {
            return new SmsChannel($app['config']['services.sms']);
        });

        $this->app->bind('two-factor.channel.email', function ($app) {
            return new EmailChannel();
        });

        $this->app->bind('two-factor.channel.telegram', function ($app) {
            return new TelegramChannel($app['config']['services.telegram']);
        });

        $this->app->bind('two-factor.channel.whatsapp', function ($app) {
            return new WhatsAppChannel($app['config']['services.whatsapp']);
        });
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}
