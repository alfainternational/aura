<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\CartService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CartController extends Controller
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->middleware('auth');
        $this->middleware('check-role:customer');
        $this->cartService = $cartService;
    }

    /**
     * View Cart Contents
     */
    public function index()
    {
        $customer = auth()->user();
        
        $cart = $this->cartService->getCart($customer);
        $cartItems = $cart->items;
        $total = $this->cartService->calculateTotal($cart);
        $availableShippingMethods = $this->cartService->getAvailableShippingMethods($cart);

        return view('cart.index', compact(
            'cartItems', 
            'total', 
            'availableShippingMethods'
        ));
    }

    /**
     * Add Product to Cart
     */
    public function add(Request $request)
    {
        $customer = auth()->user();
        
        $validatedData = $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1|max:50',
            'variant_id' => 'nullable|exists:product_variants,id'
        ]);

        try {
            $product = Product::findOrFail($validatedData['product_id']);
            
            $this->cartService->addToCart(
                $customer, 
                $product, 
                $validatedData['quantity'], 
                $validatedData['variant_id'] ?? null
            );

            return response()->json([
                'success' => true, 
                'message' => 'Product added to cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Unable to add product to cart: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Update Cart Item Quantity
     */
    public function update(Request $request)
    {
        $customer = auth()->user();
        
        $validatedData = $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id',
            'quantity' => 'required|integer|min:1|max:50'
        ]);

        try {
            $this->cartService->updateCartItemQuantity(
                $customer, 
                $validatedData['cart_item_id'], 
                $validatedData['quantity']
            );

            return response()->json([
                'success' => true, 
                'message' => 'Cart item updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Unable to update cart item: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Remove Item from Cart
     */
    public function remove(Request $request)
    {
        $customer = auth()->user();
        
        $validatedData = $request->validate([
            'cart_item_id' => 'required|exists:cart_items,id'
        ]);

        try {
            $this->cartService->removeFromCart(
                $customer, 
                $validatedData['cart_item_id']
            );

            return response()->json([
                'success' => true, 
                'message' => 'Item removed from cart successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Unable to remove item from cart: ' . $e->getMessage()
            ], 400);
        }
    }

    /**
     * Clear Entire Cart
     */
    public function clear()
    {
        $customer = auth()->user();
        
        try {
            $this->cartService->clearCart($customer);

            return response()->json([
                'success' => true, 
                'message' => 'Cart cleared successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false, 
                'message' => 'Unable to clear cart: ' . $e->getMessage()
            ], 400);
        }
    }
}
