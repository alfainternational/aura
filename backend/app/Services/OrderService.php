<?php

namespace App\Services;

use App\Models\Order;
use App\Models\User;
use App\Models\Cart;
use App\Models\Coupon;
use App\Models\Payment;
use App\Models\ShippingAddress;
use Illuminate\Support\Facades\DB;

class OrderService
{
    /**
     * Create Order from Cart
     */
    public function createOrder(
        User $customer, 
        $shippingAddressId, 
        $paymentMethod, 
        $deliveryInstructions = null, 
        $couponCode = null
    ) {
        // Validate shipping address belongs to customer
        $shippingAddress = ShippingAddress::findOrFail($shippingAddressId);
        if ($shippingAddress->user_id !== $customer->id) {
            throw new \Exception('Invalid shipping address');
        }

        // Get customer's cart
        $cart = $customer->cart;
        if (!$cart || $cart->items->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        // Validate cart items availability and stock
        $this->validateCartItems($cart);

        // Start database transaction
        return DB::transaction(function () use (
            $customer, 
            $cart, 
            $shippingAddress, 
            $paymentMethod, 
            $deliveryInstructions, 
            $couponCode
        ) {
            // Apply coupon if provided
            $couponDiscount = 0;
            if ($couponCode) {
                $coupon = Coupon::where('code', $couponCode)
                    ->where('active', true)
                    ->first();
                
                if ($coupon) {
                    $couponDiscount = $this->calculateCouponDiscount($cart, $coupon);
                }
            }

            // Calculate total
            $subtotal = $this->calculateCartTotal($cart);
            $total = $subtotal - $couponDiscount;

            // Create order
            $order = Order::create([
                'user_id' => $customer->id,
                'shipping_address_id' => $shippingAddress->id,
                'status' => 'pending',
                'subtotal' => $subtotal,
                'discount' => $couponDiscount,
                'total' => $total,
                'payment_method' => $paymentMethod,
                'delivery_instructions' => $deliveryInstructions
            ]);

            // Create order items
            foreach ($cart->items as $cartItem) {
                $order->items()->create([
                    'product_id' => $cartItem->product_id,
                    'product_variant_id' => $cartItem->product_variant_id,
                    'quantity' => $cartItem->quantity,
                    'price' => $cartItem->product->price,
                    'merchant_id' => $cartItem->product->merchant_id
                ]);

                // Reduce product stock
                $cartItem->product->decrement('stock', $cartItem->quantity);
            }

            // Process payment
            $this->processPayment($order, $paymentMethod);

            // Clear cart
            $cart->items()->delete();

            return $order;
        });
    }

    /**
     * Validate Cart Items
     */
    private function validateCartItems(Cart $cart)
    {
        foreach ($cart->items as $cartItem) {
            $product = $cartItem->product;

            // Check product availability
            if ($product->status !== 'active') {
                throw new \Exception("Product {$product->name} is not available");
            }

            // Check stock
            if ($cartItem->quantity > $product->stock) {
                throw new \Exception("Insufficient stock for product {$product->name}");
            }
        }
    }

    /**
     * Calculate Cart Total
     */
    private function calculateCartTotal(Cart $cart)
    {
        $total = 0;
        foreach ($cart->items as $cartItem) {
            $total += $cartItem->product->price * $cartItem->quantity;
        }
        return $total;
    }

    /**
     * Calculate Coupon Discount
     */
    private function calculateCouponDiscount(Cart $cart, Coupon $coupon)
    {
        $subtotal = $this->calculateCartTotal($cart);

        // Fixed amount discount
        if ($coupon->type === 'fixed') {
            return min($coupon->value, $subtotal);
        }

        // Percentage discount
        if ($coupon->type === 'percentage') {
            return $subtotal * ($coupon->value / 100);
        }

        return 0;
    }

    /**
     * Process Payment
     */
    private function processPayment(Order $order, $paymentMethod)
    {
        $customer = $order->user;

        switch ($paymentMethod) {
            case 'wallet':
                // Deduct from user's wallet
                if ($customer->wallet_balance < $order->total) {
                    throw new \Exception('Insufficient wallet balance');
                }
                $customer->decrement('wallet_balance', $order->total);
                break;

            case 'card':
                // Process card payment via payment gateway
                $paymentGateway = new PaymentGatewayService();
                $paymentResult = $paymentGateway->processCardPayment(
                    $customer, 
                    $order->total, 
                    $order->id
                );
                break;

            case 'cash_on_delivery':
                // No immediate payment processing
                break;

            default:
                throw new \Exception('Invalid payment method');
        }

        // Record payment
        Payment::create([
            'order_id' => $order->id,
            'amount' => $order->total,
            'method' => $paymentMethod,
            'status' => 'completed'
        ]);
    }

    /**
     * Update Order Status
     */
    public function updateOrderStatus(
        Order $order, 
        $status, 
        $trackingNumber = null
    ) {
        // Validate status transition
        $this->validateStatusTransition($order, $status);

        // Update order status
        $order->update([
            'status' => $status,
            'tracking_number' => $trackingNumber
        ]);

        // Trigger notifications or other side effects based on status
        $this->handleStatusChange($order, $status);

        return $order;
    }

    /**
     * Validate Status Transition
     */
    private function validateStatusTransition(Order $order, $newStatus)
    {
        $validTransitions = [
            'pending' => ['processing', 'cancelled'],
            'processing' => ['shipped', 'cancelled'],
            'shipped' => ['delivered', 'cancelled'],
            'delivered' => [],
            'cancelled' => []
        ];

        if (!isset($validTransitions[$order->status]) || 
            !in_array($newStatus, $validTransitions[$order->status])) {
            throw new \Exception('Invalid order status transition');
        }
    }

    /**
     * Handle Status Change Side Effects
     */
    private function handleStatusChange(Order $order, $status)
    {
        switch ($status) {
            case 'shipped':
                // Send shipping notification to customer
                $this->sendShippingNotification($order);
                break;

            case 'delivered':
                // Mark order as completed, release funds to merchant
                $this->releasePaymentToMerchant($order);
                break;

            case 'cancelled':
                // Restore product stock, refund payment
                $this->handleOrderCancellation($order);
                break;
        }
    }

    /**
     * Send Shipping Notification
     */
    private function sendShippingNotification(Order $order)
    {
        // Implement notification logic
        // Could use Laravel's notification system or a dedicated service
    }

    /**
     * Release Payment to Merchant
     */
    private function releasePaymentToMerchant(Order $order)
    {
        // Calculate merchant's share of the order
        $merchantShare = $order->items->sum(function ($item) {
            return $item->price * $item->quantity;
        });

        // Credit merchant's wallet
        $merchant = $order->items->first()->product->merchant;
        $merchant->increment('wallet_balance', $merchantShare);
    }

    /**
     * Handle Order Cancellation
     */
    private function handleOrderCancellation(Order $order)
    {
        // Restore product stock
        foreach ($order->items as $orderItem) {
            $orderItem->product->increment('stock', $orderItem->quantity);
        }

        // Refund payment based on payment method
        $payment = $order->payments()->first();
        if ($payment) {
            switch ($payment->method) {
                case 'wallet':
                    $order->user->increment('wallet_balance', $order->total);
                    break;

                case 'card':
                    // Initiate refund through payment gateway
                    $paymentGateway = new PaymentGatewayService();
                    $paymentGateway->refundCardPayment($payment);
                    break;
            }
        }
    }
}
