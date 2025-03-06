<?php

namespace App\Services;

use App\Models\User;
use App\Models\Payment;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class PaymentGatewayService
{
    /**
     * Process Card Payment
     */
    public function processCardPayment(
        User $customer, 
        $amount, 
        $orderId
    ) {
        // Validate card details (these would typically come from a secure payment form)
        $cardToken = $this->tokenizeCard($customer);

        // Prepare payment request
        $paymentRequest = [
            'amount' => $amount,
            'currency' => 'SDG', // Sudanese Pound
            'customer' => [
                'id' => $customer->id,
                'name' => $customer->name,
                'email' => $customer->email,
                'phone' => $customer->phone_number
            ],
            'order' => [
                'id' => $orderId,
                'description' => "Order payment for Order #$orderId"
            ],
            'payment_method' => [
                'type' => 'card',
                'token' => $cardToken
            ]
        ];

        try {
            // Call payment gateway API
            $response = $this->callPaymentGateway('charge', $paymentRequest);

            // Validate response
            if (!$this->validatePaymentResponse($response)) {
                throw new \Exception('Payment processing failed');
            }

            return $response;
        } catch (\Exception $e) {
            // Log error
            \Log::error('Card Payment Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Refund Card Payment
     */
    public function refundCardPayment(Payment $payment)
    {
        // Prepare refund request
        $refundRequest = [
            'payment_id' => $payment->external_id,
            'amount' => $payment->amount,
            'reason' => 'Order cancellation'
        ];

        try {
            // Call payment gateway API for refund
            $response = $this->callPaymentGateway('refund', $refundRequest);

            // Validate refund response
            if (!$this->validateRefundResponse($response)) {
                throw new \Exception('Refund processing failed');
            }

            // Update payment status
            $payment->update([
                'status' => 'refunded'
            ]);

            return $response;
        } catch (\Exception $e) {
            // Log error
            \Log::error('Refund Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Tokenize Card Details
     * 
     * In a real-world scenario, this would involve securely collecting 
     * and tokenizing card details through a payment gateway
     */
    private function tokenizeCard(User $customer)
    {
        // This is a mock implementation
        // In a real system, this would involve secure card tokenization
        return Str::random(40);
    }

    /**
     * Call Payment Gateway API
     */
    private function callPaymentGateway($endpoint, $data)
    {
        // Use environment-specific configuration
        $apiBaseUrl = config('services.payment_gateway.url');
        $apiKey = config('services.payment_gateway.key');

        try {
            $response = Http::withHeaders([
                'Authorization' => "Bearer $apiKey",
                'Content-Type' => 'application/json'
            ])->post("{$apiBaseUrl}/{$endpoint}", $data);

            // Throw exception for non-200 responses
            $response->throw();

            return $response->json();
        } catch (\Exception $e) {
            \Log::error('Payment Gateway API Error: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Validate Payment Response
     */
    private function validatePaymentResponse($response)
    {
        // Implement validation logic based on payment gateway response
        return isset($response['status']) && 
               $response['status'] === 'success' && 
               isset($response['transaction_id']);
    }

    /**
     * Validate Refund Response
     */
    private function validateRefundResponse($response)
    {
        // Implement validation logic based on payment gateway response
        return isset($response['status']) && 
               $response['status'] === 'refunded';
    }

    /**
     * Validate Webhook Signature
     * 
     * Used to verify webhooks from payment gateway
     */
    public function validateWebhookSignature($payload, $signature)
    {
        $secretKey = config('services.payment_gateway.webhook_secret');
        
        // Generate expected signature
        $expectedSignature = hash_hmac(
            'sha256', 
            json_encode($payload), 
            $secretKey
        );

        return hash_equals($expectedSignature, $signature);
    }

    /**
     * Handle Payment Webhook
     */
    public function handlePaymentWebhook($payload)
    {
        // Validate webhook payload
        $eventType = $payload['event_type'] ?? null;
        $transactionId = $payload['transaction_id'] ?? null;

        switch ($eventType) {
            case 'payment_success':
                $this->handleSuccessfulPayment($transactionId, $payload);
                break;

            case 'payment_failed':
                $this->handleFailedPayment($transactionId, $payload);
                break;

            case 'refund_processed':
                $this->handleRefundProcessed($transactionId, $payload);
                break;

            default:
                \Log::warning('Unhandled webhook event: ' . $eventType);
        }
    }

    /**
     * Handle Successful Payment
     */
    private function handleSuccessfulPayment($transactionId, $payload)
    {
        // Find and update payment record
        $payment = Payment::where('external_id', $transactionId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'completed',
                'details' => json_encode($payload)
            ]);

            // Potentially trigger additional actions
            // e.g., update order status, send confirmation email
        }
    }

    /**
     * Handle Failed Payment
     */
    private function handleFailedPayment($transactionId, $payload)
    {
        // Find and update payment record
        $payment = Payment::where('external_id', $transactionId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'failed',
                'details' => json_encode($payload)
            ]);

            // Potentially trigger additional actions
            // e.g., notify customer, cancel order
        }
    }

    /**
     * Handle Refund Processed
     */
    private function handleRefundProcessed($transactionId, $payload)
    {
        // Find and update payment record
        $payment = Payment::where('external_id', $transactionId)->first();
        
        if ($payment) {
            $payment->update([
                'status' => 'refunded',
                'details' => json_encode($payload)
            ]);

            // Potentially trigger additional actions
            // e.g., update order status, send refund confirmation
        }
    }
}
