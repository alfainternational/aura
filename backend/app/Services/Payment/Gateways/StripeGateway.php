<?php

namespace App\Services\Payment\Gateways;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Illuminate\Support\Facades\Log;

class StripeGateway implements PaymentGatewayInterface
{
    /**
     * @var StripeClient
     */
    protected $stripeClient;
    
    /**
     * @var string
     */
    protected $publicKey;
    
    /**
     * @var string
     */
    protected $webhookSecret;
    
    /**
     * Constructor
     *
     * @param string $secretKey
     * @param string $publicKey
     * @param string $webhookSecret
     */
    public function __construct(string $secretKey, string $publicKey, string $webhookSecret)
    {
        $this->stripeClient = new StripeClient($secretKey);
        $this->publicKey = $publicKey;
        $this->webhookSecret = $webhookSecret;
    }
    
    /**
     * {@inheritdoc}
     */
    public function createTransaction(Order $order, array $paymentData = []): array
    {
        try {
            $currency = strtolower($order->currency);
            $amount = $this->formatAmount($order->total, $currency);
            
            // Create payment intent
            $paymentIntent = $this->stripeClient->paymentIntents->create([
                'amount' => $amount,
                'currency' => $currency,
                'description' => "Order #{$order->id} Payment",
                'metadata' => [
                    'order_id' => $order->id,
                    'customer_email' => $order->user->email,
                ],
                'receipt_email' => $order->user->email,
                'automatic_payment_methods' => [
                    'enabled' => true,
                ],
            ]);
            
            return [
                'success' => true,
                'transaction_id' => $paymentIntent->id,
                'client_secret' => $paymentIntent->client_secret,
                'public_key' => $this->publicKey,
                'amount' => $amount,
                'currency' => $currency,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment intent creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        try {
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($transactionId);
            
            $status = match ($paymentIntent->status) {
                'succeeded' => 'completed',
                'processing' => 'processing',
                'requires_payment_method', 'requires_confirmation', 'requires_action' => 'pending',
                'canceled' => 'cancelled',
                default => 'failed',
            };
            
            return [
                'success' => true,
                'status' => $status,
                'transaction_id' => $transactionId,
                'amount' => $this->unformatAmount($paymentIntent->amount, $paymentIntent->currency),
                'currency' => strtoupper($paymentIntent->currency),
                'payment_method' => $paymentIntent->payment_method_types[0] ?? 'unknown',
                'metadata' => $paymentIntent->metadata->toArray(),
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment status check failed', [
                'transaction_id' => $transactionId,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'status' => 'error',
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function confirmPayment(Payment $payment, array $data = []): bool
    {
        try {
            if (empty($payment->transaction_id)) {
                return false;
            }
            
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);
            
            if ($paymentIntent->status === 'succeeded') {
                $gatewayResponse = [
                    'id' => $paymentIntent->id,
                    'status' => $paymentIntent->status,
                    'amount' => $paymentIntent->amount,
                    'currency' => $paymentIntent->currency,
                    'payment_method' => $paymentIntent->payment_method,
                    'payment_method_types' => $paymentIntent->payment_method_types,
                    'receipt_url' => $paymentIntent->charges->data[0]->receipt_url ?? null,
                ];
                
                $payment->markAsCompleted($payment->transaction_id, $gatewayResponse);
                return true;
            }
            
            return false;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment confirmation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function cancelTransaction(Payment $payment): bool
    {
        try {
            if (empty($payment->transaction_id)) {
                return false;
            }
            
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);
            
            if ($paymentIntent->status !== 'succeeded' && $paymentIntent->status !== 'canceled') {
                $this->stripeClient->paymentIntents->cancel($payment->transaction_id);
                
                $payment->status = 'cancelled';
                $payment->gateway_response = array_merge($payment->gateway_response ?? [], [
                    'cancellation_date' => now()->toIso8601String(),
                    'cancellation_reason' => 'Cancelled by user',
                ]);
                $payment->save();
                
                return true;
            }
            
            return false;
        } catch (ApiErrorException $e) {
            Log::error('Stripe payment cancellation failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);
            
            return false;
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function refundTransaction(Payment $payment, ?float $amount = null, string $reason = ''): array
    {
        try {
            if (empty($payment->transaction_id) || $payment->status !== 'completed') {
                return [
                    'success' => false,
                    'error' => 'Payment cannot be refunded',
                ];
            }
            
            // Get the charge ID from the payment intent
            $paymentIntent = $this->stripeClient->paymentIntents->retrieve($payment->transaction_id);
            $chargeId = $paymentIntent->charges->data[0]->id ?? null;
            
            if (!$chargeId) {
                return [
                    'success' => false,
                    'error' => 'No charge found for this payment',
                ];
            }
            
            $refundData = [
                'charge' => $chargeId,
                'reason' => 'requested_by_customer',
                'metadata' => [
                    'payment_id' => $payment->id,
                    'order_id' => $payment->order_id,
                    'reason' => $reason,
                ],
            ];
            
            // If amount is specified, refund only that amount
            if ($amount !== null) {
                $refundData['amount'] = $this->formatAmount($amount, $payment->currency);
            }
            
            $refund = $this->stripeClient->refunds->create($refundData);
            
            // Update payment status
            $payment->processRefund($amount, $reason);
            
            return [
                'success' => true,
                'refund_id' => $refund->id,
                'amount' => $amount ?? $payment->amount,
                'currency' => $payment->currency,
                'status' => $refund->status,
            ];
        } catch (ApiErrorException $e) {
            Log::error('Stripe refund failed', [
                'payment_id' => $payment->id,
                'transaction_id' => $payment->transaction_id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function handleWebhook(array $data): array
    {
        try {
            $event = \Stripe\Webhook::constructEvent(
                $data['payload'],
                $data['signature'],
                $this->webhookSecret
            );
            
            $result = [
                'success' => true,
                'event_type' => $event->type,
                'action' => 'no_action',
            ];
            
            // Handle specific event types
            switch ($event->type) {
                case 'payment_intent.succeeded':
                    $paymentIntent = $event->data->object;
                    $result['transaction_id'] = $paymentIntent->id;
                    $result['action'] = 'payment_success';
                    
                    // Find the associated payment and mark it as completed
                    $payment = Payment::where('transaction_id', $paymentIntent->id)->first();
                    if ($payment) {
                        $this->confirmPayment($payment);
                        $result['payment_id'] = $payment->id;
                    }
                    break;
                    
                case 'payment_intent.payment_failed':
                    $paymentIntent = $event->data->object;
                    $result['transaction_id'] = $paymentIntent->id;
                    $result['action'] = 'payment_failed';
                    
                    // Find the associated payment and mark it as failed
                    $payment = Payment::where('transaction_id', $paymentIntent->id)->first();
                    if ($payment) {
                        $payment->markAsFailed(['error' => $paymentIntent->last_payment_error->message ?? 'Payment failed']);
                        $result['payment_id'] = $payment->id;
                    }
                    break;
                    
                case 'charge.refunded':
                    $charge = $event->data->object;
                    $result['transaction_id'] = $charge->payment_intent;
                    $result['action'] = 'refund_processed';
                    
                    // Find the associated payment and ensure it's marked as refunded
                    $payment = Payment::where('transaction_id', $charge->payment_intent)->first();
                    if ($payment && $payment->status !== 'refunded') {
                        $payment->processRefund(
                            $this->unformatAmount($charge->amount_refunded, $charge->currency),
                            'Refund processed via Stripe webhook'
                        );
                        $result['payment_id'] = $payment->id;
                    }
                    break;
            }
            
            return $result;
        } catch (\Exception $e) {
            Log::error('Stripe webhook processing failed', [
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'Stripe';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPaymentOptions(): array
    {
        return [
            'credit_card' => 'بطاقة ائتمان',
            'mada' => 'مدى',
            'apple_pay' => 'Apple Pay',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function supportsInstallments(): bool
    {
        return false;
    }
    
    /**
     * {@inheritdoc}
     */
    public function getInstallmentPlans(float $amount): array
    {
        return [];
    }
    
    /**
     * Format amount for Stripe API (convert to smallest currency unit)
     *
     * @param float $amount
     * @param string $currency
     * @return int
     */
    protected function formatAmount(float $amount, string $currency): int
    {
        // Convert to smallest currency unit (e.g., cents for USD)
        $zeroDecimalCurrencies = ['jpy', 'krw', 'vnd', 'bif', 'clp', 'djf', 'gnf', 'kmf', 'mga', 'pyg', 'rwf', 'ugx', 'vuv', 'xaf', 'xof', 'xpf'];
        
        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (int) $amount;
        }
        
        return (int) ($amount * 100);
    }
    
    /**
     * Convert from smallest currency unit to regular amount
     *
     * @param int $amount
     * @param string $currency
     * @return float
     */
    protected function unformatAmount(int $amount, string $currency): float
    {
        $zeroDecimalCurrencies = ['jpy', 'krw', 'vnd', 'bif', 'clp', 'djf', 'gnf', 'kmf', 'mga', 'pyg', 'rwf', 'ugx', 'vuv', 'xaf', 'xof', 'xpf'];
        
        if (in_array(strtolower($currency), $zeroDecimalCurrencies)) {
            return (float) $amount;
        }
        
        return (float) ($amount / 100);
    }
}
