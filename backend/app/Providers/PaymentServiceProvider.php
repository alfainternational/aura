<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\Payments\PaymentGatewayInterface;
use App\Services\Payments\DefaultPaymentGateway;

class PaymentServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        // تسجيل واجهة الدفع مع التنفيذ الافتراضي
        $this->app->bind(PaymentGatewayInterface::class, DefaultPaymentGateway::class);
    }

    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        //
    }
}
