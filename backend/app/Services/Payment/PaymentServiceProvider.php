<?php

namespace App\Services\Payment;

use Illuminate\Support\ServiceProvider;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\Gateways\StripeGateway;
use App\Services\Payment\Gateways\PayPalGateway;
use App\Services\Payment\Gateways\MyFatoorahGateway;
use App\Services\Payment\Gateways\CashOnDeliveryGateway;
use App\Services\Payment\Gateways\AuraWalletGateway;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // Register payment gateways
        $this->app->bind('payment.aura_wallet', function ($app) {
            return new AuraWalletGateway();
        });
        
        $this->app->bind('payment.stripe', function ($app) {
            return new StripeGateway(
                config('payment.stripe.secret_key'),
                config('payment.stripe.public_key'),
                config('payment.stripe.webhook_secret')
            );
        });
        
        $this->app->bind('payment.paypal', function ($app) {
            return new PayPalGateway(
                config('payment.paypal.client_id'),
                config('payment.paypal.client_secret'),
                config('payment.paypal.environment')
            );
        });
        
        $this->app->bind('payment.myfatoorah', function ($app) {
            return new MyFatoorahGateway(
                config('payment.myfatoorah.api_key'),
                config('payment.myfatoorah.base_url')
            );
        });
        
        $this->app->bind('payment.cod', function ($app) {
            return new CashOnDeliveryGateway();
        });
        
        // Register payment factory
        $this->app->singleton('payment.factory', function ($app) {
            return new PaymentGatewayFactory($app);
        });
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        // Publish configuration file
        $this->publishes([
            __DIR__.'/../../config/payment.php' => config_path('payment.php'),
        ], 'payment-config');
    }
}
