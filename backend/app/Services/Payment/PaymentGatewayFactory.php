<?php

namespace App\Services\Payment;

use Illuminate\Contracts\Foundation\Application;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Services\Payment\AuraWalletGateway;
use App\Services\Payment\StripePaymentGateway;
use App\Services\Payment\PayPalPaymentGateway;
use App\Services\Payment\CashOnDeliveryGateway;
use App\Services\Payment\DefaultPaymentGateway;
use InvalidArgumentException;

class PaymentGatewayFactory
{
    /**
     * @var Application
     */
    protected $app;
    
    /**
     * @var array
     */
    protected $gateways = [
        'aura_wallet' => AuraWalletGateway::class,
        'stripe' => StripePaymentGateway::class,
        'paypal' => PayPalPaymentGateway::class,
        'cod' => CashOnDeliveryGateway::class,
        'default' => DefaultPaymentGateway::class,
    ];
    
    /**
     * Constructor
     *
     * @param Application $app
     */
    public function __construct(Application $app)
    {
        $this->app = $app;
    }
    
    /**
     * إنشاء بوابة دفع بناءً على الاسم
     *
     * @param string $name
     * @return PaymentGatewayInterface
     * @throws InvalidArgumentException
     */
    public function create(string $name): PaymentGatewayInterface
    {
        if (!isset($this->gateways[$name])) {
            throw new InvalidArgumentException("Payment gateway [$name] is not supported.");
        }
        
        return $this->app->make($this->gateways[$name]);
    }
    
    /**
     * الحصول على قائمة بوابات الدفع المتاحة
     *
     * @return array
     */
    public function getAvailableGateways(): array
    {
        $available = [];
        
        foreach ($this->gateways as $name => $class) {
            // تفقد ما إذا كانت البوابة مكونة بشكل صحيح ونشطة
            if ($this->isGatewayConfigured($name) && $this->isGatewayActive($name)) {
                $gateway = $this->create($name);
                $available[$name] = [
                    'name' => $gateway->getName(),
                    'options' => $gateway->getPaymentOptions(),
                    'supports_installments' => $gateway->supportsInstallments(),
                ];
            }
        }
        
        return $available;
    }
    
    /**
     * التحقق مما إذا كانت بوابة الدفع نشطة في الإعدادات
     *
     * @param string $name
     * @return bool
     */
    protected function isGatewayActive(string $name): bool
    {
        if ($name === 'default') {
            return true;
        }
        return config("payment.active_gateways.{$name}", false);
    }
    
    /**
     * التحقق مما إذا كانت بوابة الدفع مكونة بشكل صحيح
     *
     * @param string $name
     * @return bool
     */
    protected function isGatewayConfigured(string $name): bool
    {
        // دائمًا إرجاع true للدفع عند الاستلام ومحفظة أورا والبوابة الافتراضية
        if ($name === 'cod' || $name === 'aura_wallet' || $name === 'default') {
            return true;
        }
        
        switch ($name) {
            case 'stripe':
                return !empty(config('payment.stripe.secret_key')) && 
                       !empty(config('payment.stripe.public_key'));
            
            case 'paypal':
                return !empty(config('payment.paypal.client_id')) && 
                       !empty(config('payment.paypal.client_secret'));
            
            case 'myfatoorah':
                return !empty(config('payment.myfatoorah.api_key'));
                
            default:
                return false;
        }
    }
}
