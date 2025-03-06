<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CustomerController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:customer']);
    }

    /**
     * Customer Dashboard
     */
    public function dashboard()
    {
        $user = auth()->user();
        
        // Fetch key metrics for customer dashboard
        $activeOrders = $user->orders()->where('status', 'in_progress')->count();
        $completedOrders = $user->orders()->where('status', 'completed')->count();
        $walletBalance = $user->wallet_balance;
        $recentMessages = $user->messages()->recent()->take(5)->get();
        $recentOrders = $user->orders()->recent()->take(5)->get();

        return view('customer.dashboard', compact(
            'user', 
            'activeOrders', 
            'completedOrders', 
            'walletBalance', 
            'recentMessages', 
            'recentOrders'
        ));
    }

    /**
     * List Customer Orders
     */
    public function orders(Request $request)
    {
        $user = auth()->user();
        
        $query = $user->orders();

        // Optional filtering
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $orders = $query->paginate(20);

        return view('customer.orders.index', compact('orders'));
    }

    /**
     * Show Order Creation Form
     */
    public function createOrder()
    {
        return view('customer.orders.create');
    }

    /**
     * Store New Order
     */
    public function storeOrder(Request $request)
    {
        $user = auth()->user();

        $validatedData = $request->validate([
            'delivery_address' => 'required|string|max:500',
            'items' => 'required|array',
            'items.*.name' => 'required|string|max:255',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.price' => 'required|numeric|min:0',
            'delivery_instructions' => 'nullable|string|max:500',
            'preferred_messenger_type' => 'nullable|in:bicycle,motorcycle,car'
        ]);

        try {
            $order = $user->orders()->create([
                'delivery_address' => $validatedData['delivery_address'],
                'items' => json_encode($validatedData['items']),
                'status' => 'pending',
                'delivery_instructions' => $validatedData['delivery_instructions'] ?? null,
                'preferred_messenger_type' => $validatedData['preferred_messenger_type'] ?? null
            ]);

            return redirect()->route('customer.orders.details', $order)
                ->with('success', 'Order created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to create order: ' . $e->getMessage());
        }
    }

    /**
     * Show Order Details
     */
    public function orderDetails(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        $order->load('messenger', 'deliveries');

        return view('customer.orders.details', compact('order'));
    }

    /**
     * Cancel Order
     */
    public function cancelOrder(Order $order)
    {
        // Ensure the order belongs to the authenticated user
        if ($order->user_id !== auth()->id()) {
            abort(403, 'Unauthorized');
        }

        // Check if order can be cancelled
        if (!in_array($order->status, ['pending', 'in_progress'])) {
            return redirect()->back()
                ->with('error', 'This order cannot be cancelled at this stage');
        }

        $order->update(['status' => 'cancelled']);

        return redirect()->route('customer.orders')
            ->with('success', 'Order cancelled successfully');
    }

    /**
     * Rate Messenger
     */
    public function rateMessenger(Request $request, $messenger)
    {
        $validatedData = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'review' => 'nullable|string|max:500',
            'order_id' => 'required|exists:orders,id'
        ]);

        $user = auth()->user();

        try {
            $rating = Rating::create([
                'user_id' => $user->id,
                'messenger_id' => $messenger,
                'order_id' => $validatedData['order_id'],
                'rating' => $validatedData['rating'],
                'review' => $validatedData['review'] ?? null
            ]);

            return redirect()->back()
                ->with('success', 'Messenger rated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to submit rating: ' . $e->getMessage());
        }
    }

    /**
     * Rating History
     */
    public function ratingHistory()
    {
        $user = auth()->user();
        
        $ratings = $user->ratings()->with('messenger')->paginate(20);

        return view('customer.ratings.history', compact('ratings'));
    }
}
