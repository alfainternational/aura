<?php

namespace App\Services;

use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Cache;

class PayPalService
{
    protected $client;
    protected $baseUrl;
    protected $clientId;
    protected $clientSecret;
    protected $mode;

    /**
     * Create a new PayPal service instance.
     */
    public function __construct()
    {
        $this->mode = env('PAYPAL_MODE', 'sandbox');
        
        // Set API credentials based on mode
        if ($this->mode === 'sandbox') {
            $this->clientId = env('PAYPAL_SANDBOX_CLIENT_ID');
            $this->clientSecret = env('PAYPAL_SANDBOX_CLIENT_SECRET');
            $this->baseUrl = 'https://api-m.sandbox.paypal.com';
        } else {
            $this->clientId = env('PAYPAL_CLIENT_ID');
            $this->clientSecret = env('PAYPAL_SECRET');
            $this->baseUrl = 'https://api-m.paypal.com';
        }
        
        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout' => 30,
        ]);
    }

    /**
     * Get PayPal OAuth2 token
     *
     * @return string
     */
    protected function getAccessToken()
    {
        // Try to get token from cache first
        $cacheKey = 'paypal_access_token_' . $this->mode;
        if (Cache::has($cacheKey)) {
            return Cache::get($cacheKey);
        }
        
        try {
            $response = $this->client->post('/v1/oauth2/token', [
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                'auth' => [$this->clientId, $this->clientSecret],
                'form_params' => [
                    'grant_type' => 'client_credentials',
                ],
            ]);
            
            $data = json_decode($response->getBody(), true);
            
            // Cache the token for slightly less than the expiry time
            $expiresIn = $data['expires_in'] - 60; // Subtract 1 minute to be safe
            Cache::put($cacheKey, $data['access_token'], $expiresIn);
            
            return $data['access_token'];
        } catch (\Exception $e) {
            Log::error('PayPal getAccessToken failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a payment for withdrawal
     *
     * @param string $receiverEmail Email address of the payment recipient
     * @param float $amount Amount to pay
     * @param string $currency Currency code (default: USD)
     * @param string $note Note to the recipient
     * @return array
     */
    public function createPayout($receiverEmail, $amount, $currency = 'USD', $note = 'Payment from ' . APP_NAME)
    {
        try {
            $token = $this->getAccessToken();
            
            $payoutItem = [
                'recipient_type' => 'EMAIL',
                'amount' => [
                    'value' => $amount,
                    'currency' => $currency,
                ],
                'note' => $note,
                'receiver' => $receiverEmail,
                'sender_item_id' => uniqid('payout_'),
            ];
            
            $response = $this->client->post('/v1/payments/payouts', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'sender_batch_header' => [
                        'sender_batch_id' => uniqid('batch_'),
                        'email_subject' => 'You have a payment from ' . config('app.name'),
                        'email_message' => 'You have received a payment from ' . config('app.name') . '. Thanks for using our services!',
                    ],
                    'items' => [$payoutItem],
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal createPayout failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Create a PayPal payment for deposit
     *
     * @param float $amount Amount to pay
     * @param string $currency Currency code (default: USD)
     * @param string $returnUrl URL to return to after successful payment
     * @param string $cancelUrl URL to return to if payment is canceled
     * @param string $description Payment description
     * @return array
     */
    public function createPayment($amount, $currency = 'USD', $returnUrl, $cancelUrl, $description = 'Deposit to Wallet')
    {
        try {
            $token = $this->getAccessToken();
            
            $response = $this->client->post('/v1/payments/payment', [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'intent' => 'sale',
                    'payer' => [
                        'payment_method' => 'paypal',
                    ],
                    'transactions' => [
                        [
                            'amount' => [
                                'total' => $amount,
                                'currency' => $currency,
                            ],
                            'description' => $description,
                        ],
                    ],
                    'redirect_urls' => [
                        'return_url' => $returnUrl,
                        'cancel_url' => $cancelUrl,
                    ],
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal createPayment failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Execute an approved PayPal payment
     *
     * @param string $paymentId Payment ID from PayPal
     * @param string $payerId Payer ID from PayPal
     * @return array
     */
    public function executePayment($paymentId, $payerId)
    {
        try {
            $token = $this->getAccessToken();
            
            $response = $this->client->post("/v1/payments/payment/{$paymentId}/execute", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
                'json' => [
                    'payer_id' => $payerId,
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal executePayment failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payment details
     *
     * @param string $paymentId Payment ID from PayPal
     * @return array
     */
    public function getPaymentDetails($paymentId)
    {
        try {
            $token = $this->getAccessToken();
            
            $response = $this->client->get("/v1/payments/payment/{$paymentId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal getPaymentDetails failed: ' . $e->getMessage());
            throw $e;
        }
    }

    /**
     * Get payout details
     *
     * @param string $payoutBatchId Payout batch ID from PayPal
     * @return array
     */
    public function getPayoutDetails($payoutBatchId)
    {
        try {
            $token = $this->getAccessToken();
            
            $response = $this->client->get("/v1/payments/payouts/{$payoutBatchId}", [
                'headers' => [
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . $token,
                ],
            ]);
            
            return json_decode($response->getBody(), true);
        } catch (\Exception $e) {
            Log::error('PayPal getPayoutDetails failed: ' . $e->getMessage());
            throw $e;
        }
    }
}
