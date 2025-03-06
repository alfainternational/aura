<?php

namespace App\Services;

use App\Models\User;
use App\Models\Product;
use App\Models\Cart;
use App\Models\CartItem;
use Illuminate\Support\Facades\DB;

class CartService
{
    /**
     * Get or Create User's Cart
     */
    public function getCart(User $customer)
    {
        return Cart::firstOrCreate([
            'user_id' => $customer->id
        ]);
    }

    /**
     * Add Product to Cart
     */
    public function addToCart(
        User $customer, 
        Product $product, 
        int $quantity = 1, 
        $variantId = null
    ) {
        // Validate product availability and stock
        $this->validateProductForCart($product, $quantity);

        return DB::transaction(function () use (
            $customer, 
            $product, 
            $quantity, 
            $variantId
        ) {
            // Get or create cart
            $cart = $this->getCart($customer);

            // Check if product is already in cart
            $existingCartItem = $cart->items()
                ->where('product_id', $product->id)
                ->when($variantId, function ($query) use ($variantId) {
                    return $query->where('product_variant_id', $variantId);
                })
                ->first();

            if ($existingCartItem) {
                // Update quantity
                $newQuantity = $existingCartItem->quantity + $quantity;
                $this->validateProductForCart($product, $newQuantity);
                
                $existingCartItem->update([
                    'quantity' => $newQuantity
                ]);

                return $existingCartItem;
            }

            // Create new cart item
            return $cart->items()->create([
                'product_id' => $product->id,
                'product_variant_id' => $variantId,
                'quantity' => $quantity
            ]);
        });
    }

    /**
     * Update Cart Item Quantity
     */
    public function updateCartItemQuantity(
        User $customer, 
        $cartItemId, 
        int $quantity
    ) {
        return DB::transaction(function () use (
            $customer, 
            $cartItemId, 
            $quantity
        ) {
            // Find cart item
            $cartItem = CartItem::whereHas('cart', function ($query) use ($customer) {
                $query->where('user_id', $customer->id);
            })->findOrFail($cartItemId);

            // Validate product availability and stock
            $this->validateProductForCart($cartItem->product, $quantity);

            // Update quantity
            $cartItem->update([
                'quantity' => $quantity
            ]);

            return $cartItem;
        });
    }

    /**
     * Remove Item from Cart
     */
    public function removeFromCart(
        User $customer, 
        $cartItemId
    ) {
        return DB::transaction(function () use (
            $customer, 
            $cartItemId
        ) {
            // Find cart item
            $cartItem = CartItem::whereHas('cart', function ($query) use ($customer) {
                $query->where('user_id', $customer->id);
            })->findOrFail($cartItemId);

            // Delete cart item
            $cartItem->delete();

            return true;
        });
    }

    /**
     * Clear Entire Cart
     */
    public function clearCart(User $customer)
    {
        return DB::transaction(function () use ($customer) {
            // Find cart
            $cart = $this->getCart($customer);

            // Delete all cart items
            $cart->items()->delete();

            return true;
        });
    }

    /**
     * Calculate Cart Total
     */
    public function calculateTotal(Cart $cart)
    {
        return $cart->items->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });
    }

    /**
     * Get Available Shipping Methods
     */
    public function getAvailableShippingMethods(Cart $cart)
    {
        // Determine shipping methods based on cart contents
        $totalWeight = $cart->items->sum(function ($item) {
            return $item->product->weight * $item->quantity;
        });

        $shippingMethods = [
            'standard' => [
                'name' => 'Standard Shipping',
                'price' => $this->calculateStandardShipping($totalWeight),
                'estimated_days' => 3
            ],
            'express' => [
                'name' => 'Express Shipping',
                'price' => $this->calculateExpressShipping($totalWeight),
                'estimated_days' => 1
            ]
        ];

        return $shippingMethods;
    }

    /**
     * Validate Product for Cart
     */
    private function validateProductForCart(Product $product, int $quantity)
    {
        // Check product availability
        if ($product->status !== 'active') {
            throw new \Exception("Product {$product->name} is not available");
        }

        // Check stock
        if ($quantity > $product->stock) {
            throw new \Exception("Insufficient stock for product {$product->name}");
        }
    }

    /**
     * Calculate Standard Shipping
     */
    private function calculateStandardShipping($totalWeight)
    {
        // Example pricing logic
        $baseRate = 20; // SDG
        $weightRate = 5; // SDG per kg

        return $baseRate + ($totalWeight * $weightRate);
    }

    /**
     * Calculate Express Shipping
     */
    private function calculateExpressShipping($totalWeight)
    {
        // Example pricing logic
        $baseRate = 50; // SDG
        $weightRate = 10; // SDG per kg

        return $baseRate + ($totalWeight * $weightRate);
    }
}
