<?php

namespace App\Services\Payment\Gateways;

use App\Models\Ecommerce\Order;
use App\Models\Ecommerce\Payment;
use App\Services\Payment\Contracts\PaymentGatewayInterface;
use App\Models\Wallet\Transaction;
use App\Models\Wallet\Wallet;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AuraWalletGateway implements PaymentGatewayInterface
{
    /**
     * {@inheritdoc}
     */
    public function createTransaction(Order $order, array $paymentData = []): array
    {
        try {
            $userId = $order->user_id;
            $amount = $order->total;
            $currency = $order->currency;
            
            // التحقق من رصيد المحفظة
            $wallet = Wallet::where('user_id', $userId)->first();
            
            if (!$wallet) {
                return [
                    'success' => false,
                    'error' => 'لم يتم العثور على محفظة لهذا المستخدم',
                ];
            }
            
            if ($wallet->balance < $amount) {
                return [
                    'success' => false,
                    'error' => 'رصيد المحفظة غير كافٍ لإتمام عملية الدفع',
                ];
            }
            
            // إنشاء معرف المعاملة
            $transactionId = 'aura-wallet-' . uniqid();
            
            return [
                'success' => true,
                'transaction_id' => $transactionId,
                'amount' => $amount,
                'currency' => $currency,
                'wallet_id' => $wallet->id,
                'wallet_balance' => $wallet->balance,
            ];
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment creation failed', [
                'order_id' => $order->id,
                'error' => $e->getMessage(),
            ]);
            
            return [
                'success' => false,
                'error' => 'فشل في إنشاء معاملة الدفع: ' . $e->getMessage(),
            ];
        }
    }
    
    /**
     * {@inheritdoc}
     */
    public function checkTransactionStatus(string $transactionId): array
    {
        try {
            // التحقق من حالة المعاملة في سجلات المعاملات
            $transaction = Transaction::where('reference_id', $transactionId)->first();
            
            if ($transaction) {
                return [
                    'success' => true,
                    'status' => $transaction->status,
                    'transaction_id' => $transactionId,
                    'amount' => $transaction->amount,
                    'currency' => $transaction->currency,
                    'payment_method' => 'aura_wallet',
                    'metadata' => [
                        'wallet_id' => $transaction->wallet_id,
                        'transaction_type' => $transaction->type,
                    ],
                ];
            }
            
            // إذا لم يتم العثور على المعاملة، فهي لا تزال معلقة
            return [
                'success' => true,
                'status' => 'pending',
                'transaction_id' => $transactionId,
            ];
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment status check failed', [
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
            
            // بدء معاملة قاعدة البيانات
            return DB::transaction(function () use ($payment) {
                $order = $payment->order;
                $userId = $order->user_id;
                $amount = $payment->amount;
                
                // العثور على محفظة المستخدم
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
                
                if (!$wallet || $wallet->balance < $amount) {
                    throw new \Exception('رصيد غير كافٍ في المحفظة');
                }
                
                // خصم المبلغ من المحفظة
                $wallet->balance -= $amount;
                $wallet->save();
                
                // إنشاء سجل معاملة
                $transaction = new Transaction([
                    'wallet_id' => $wallet->id,
                    'type' => 'payment',
                    'amount' => $amount,
                    'currency' => $payment->currency,
                    'reference_id' => $payment->transaction_id,
                    'reference_type' => 'order_payment',
                    'status' => 'completed',
                    'metadata' => [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                    ],
                    'description' => 'دفع الطلب رقم #' . $order->id,
                ]);
                
                $transaction->save();
                
                // تحديث حالة الدفع
                $payment->markAsCompleted($payment->transaction_id, [
                    'wallet_transaction_id' => $transaction->id,
                    'wallet_id' => $wallet->id,
                    'wallet_balance' => $wallet->balance,
                    'status' => 'completed',
                ]);
                
                return true;
            });
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment confirmation failed', [
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
            
            // تحديث حالة الدفع
            $payment->status = 'cancelled';
            $payment->gateway_response = array_merge($payment->gateway_response ?? [], [
                'cancellation_date' => now()->toIso8601String(),
                'cancellation_reason' => 'تم إلغاء الدفع من قبل المستخدم',
            ]);
            $payment->save();
            
            return true;
        } catch (\Exception $e) {
            Log::error('Aura Wallet payment cancellation failed', [
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
                    'error' => 'لا يمكن استرداد هذا الدفع',
                ];
            }
            
            // تحديد مبلغ الاسترداد
            $refundAmount = $amount ?? $payment->amount;
            
            // بدء معاملة قاعدة البيانات
            DB::transaction(function () use ($payment, $refundAmount, $reason) {
                $order = $payment->order;
                $userId = $order->user_id;
                
                // العثور على محفظة المستخدم
                $wallet = Wallet::where('user_id', $userId)->lockForUpdate()->first();
                
                if (!$wallet) {
                    throw new \Exception('لم يتم العثور على محفظة المستخدم');
                }
                
                // إضافة المبلغ المسترد إلى رصيد المحفظة
                $wallet->balance += $refundAmount;
                $wallet->save();
                
                // إنشاء سجل معاملة استرداد
                $transaction = new Transaction([
                    'wallet_id' => $wallet->id,
                    'type' => 'refund',
                    'amount' => $refundAmount,
                    'currency' => $payment->currency,
                    'reference_id' => $payment->transaction_id . '-refund',
                    'reference_type' => 'order_refund',
                    'status' => 'completed',
                    'metadata' => [
                        'order_id' => $order->id,
                        'payment_id' => $payment->id,
                        'original_transaction_id' => $payment->transaction_id,
                        'reason' => $reason,
                    ],
                    'description' => 'استرداد مبلغ الطلب رقم #' . $order->id . ($reason ? ': ' . $reason : ''),
                ]);
                
                $transaction->save();
                
                // تحديث حالة الدفع
                $payment->processRefund($refundAmount, $reason);
            });
            
            return [
                'success' => true,
                'refund_id' => $payment->transaction_id . '-refund',
                'amount' => $refundAmount,
                'currency' => $payment->currency,
                'status' => 'completed',
            ];
        } catch (\Exception $e) {
            Log::error('Aura Wallet refund failed', [
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
        // ليس مطلوبًا للمحفظة الداخلية
        return [
            'success' => true,
            'event_type' => 'no_webhook',
            'action' => 'no_action',
        ];
    }
    
    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return 'محفظة أورا';
    }
    
    /**
     * {@inheritdoc}
     */
    public function getPaymentOptions(): array
    {
        return [
            'wallet_balance' => 'الدفع من رصيد المحفظة',
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
}
