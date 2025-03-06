<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    protected $orderService;

    public function __construct(OrderService $orderService)
    {
        $this->middleware('auth');
        $this->orderService = $orderService;
    }

    /**
     * Create Order Form (Customer)
     */
    public function create()
    {
        $customer = auth()->user();
        
        // Ensure user is a customer
        if ($customer->user_type !== 'customer') {
            abort(403, 'Unauthorized');
        }

        $cart = $customer->cart;
        $shippingAddresses = $customer->shippingAddresses;

        return view('orders.create', compact('cart', 'shippingAddresses'));
    }

    /**
     * Store Order (Customer)
     */
    public function store(Request $request)
    {
        $customer = auth()->user();
        
        // Ensure user is a customer
        if ($customer->user_type !== 'customer') {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'shipping_address_id' => 'required|exists:shipping_addresses,id',
            'payment_method' => 'required|in:wallet,card,cash_on_delivery',
            'delivery_instructions' => 'nullable|string|max:500',
            'coupon_code' => 'nullable|string|exists:coupons,code'
        ]);

        try {
            DB::beginTransaction();

            // Create order using order service
            $order = $this->orderService->createOrder(
                $customer, 
                $validatedData['shipping_address_id'], 
                $validatedData['payment_method'], 
                $validatedData['delivery_instructions'] ?? null,
                $validatedData['coupon_code'] ?? null
            );

            DB::commit();

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order placed successfully');
        } catch (\Exception $e) {
            DB::rollBack();

            return redirect()->back()
                ->with('error', 'Unable to place order: ' . $e->getMessage());
        }
    }

    /**
     * Show Order Details
     */
    public function show(Order $order)
    {
        $user = auth()->user();
        
        // Ensure the order belongs to the user or the user is a merchant
        if (
            ($order->user_id !== $user->id) && 
            !($user->user_type === 'merchant' && $order->merchant_id === $user->id)
        ) {
            abort(403, 'Unauthorized');
        }

        $order->load('items.product', 'shippingAddress', 'payments');

        return view('orders.show', compact('order'));
    }

    /**
     * Customer Order History
     */
    public function customerHistory(Request $request)
    {
        $customer = auth()->user();
        
        // Ensure user is a customer
        if ($customer->user_type !== 'customer') {
            abort(403, 'Unauthorized');
        }

        $query = $customer->orders();

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $orders = $query->with('items.product')->paginate(20);

        return view('orders.customer.history', compact('orders'));
    }

    /**
     * Merchant Order Management
     */
    public function merchantOrders(Request $request)
    {
        $merchant = auth()->user();
        
        // Ensure user is a merchant
        if ($merchant->user_type !== 'merchant') {
            abort(403, 'Unauthorized');
        }

        $query = Order::whereHas('items.product', function ($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        });

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $orders = $query->with('user', 'items.product')->paginate(20);

        return view('orders.merchant.index', compact('orders'));
    }

    /**
     * Merchant Order Details
     */
    public function merchantOrderDetails(Order $order)
    {
        $merchant = auth()->user();
        
        // Ensure user is a merchant and the order contains their products
        $merchantProducts = $order->items()->whereHas('product', function ($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        })->exists();

        if (!$merchantProducts) {
            abort(403, 'Unauthorized');
        }

        $order->load('user', 'items.product', 'shippingAddress', 'payments');

        return view('orders.merchant.details', compact('order'));
    }

    /**
     * Update Order Status (Merchant)
     */
    public function updateOrderStatus(Request $request, Order $order)
    {
        $merchant = auth()->user();
        
        // Ensure user is a merchant and the order contains their products
        $merchantProducts = $order->items()->whereHas('product', function ($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        })->exists();

        if (!$merchantProducts) {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'status' => 'required|in:processing,shipped,delivered,cancelled',
            'tracking_number' => 'nullable|string|max:255'
        ]);

        try {
            $this->orderService->updateOrderStatus(
                $order, 
                $validatedData['status'], 
                $validatedData['tracking_number'] ?? null
            );

            return redirect()->route('orders.merchant.details', $order)
                ->with('success', 'Order status updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to update order status: ' . $e->getMessage());
        }
    }
}
