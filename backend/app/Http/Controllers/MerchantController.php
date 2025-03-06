<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Product;
use App\Models\Message;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MerchantController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'check-role:merchant']);
    }

    /**
     * Merchant Dashboard
     */
    public function dashboard()
    {
        $merchant = auth()->user();
        
        // Fetch key metrics for merchant dashboard
        $totalProducts = $merchant->products()->count();
        $activeProducts = $merchant->products()->where('status', 'active')->count();
        $totalOrders = $merchant->orders()->count();
        $activeOrders = $merchant->orders()->where('status', 'in_progress')->count();
        $walletBalance = $merchant->wallet_balance;
        $recentOrders = $merchant->orders()->recent()->take(5)->get();
        $topSellingProducts = $merchant->products()->orderByPerformance()->take(5)->get();

        return view('merchant.dashboard', compact(
            'merchant',
            'totalProducts',
            'activeProducts', 
            'totalOrders',
            'activeOrders',
            'walletBalance',
            'recentOrders',
            'topSellingProducts'
        ));
    }

    /**
     * Merchant Analytics
     */
    public function analytics(Request $request)
    {
        $merchant = auth()->user();
        
        // Default to last 30 days
        $startDate = $request->input('start_date', now()->subDays(30));
        $endDate = $request->input('end_date', now());

        $salesData = $merchant->calculateSalesData($startDate, $endDate);
        $productPerformance = $merchant->getProductPerformance($startDate, $endDate);
        $customerInsights = $merchant->getCustomerInsights($startDate, $endDate);

        return view('merchant.analytics.index', compact(
            'salesData', 
            'productPerformance', 
            'customerInsights',
            'startDate',
            'endDate'
        ));
    }

    /**
     * Sales Report
     */
    public function salesReport(Request $request)
    {
        $merchant = auth()->user();
        
        $query = $merchant->orders();

        // Filtering options
        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        if ($request->has('start_date') && $request->has('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date, 
                $request->end_date
            ]);
        }

        $salesReport = $query->with('products')
            ->withSum('products', 'price')
            ->paginate(20);

        return view('merchant.analytics.sales', compact('salesReport'));
    }

    /**
     * Product Performance
     */
    public function productPerformance()
    {
        $merchant = auth()->user();
        
        $products = $merchant->products()
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('merchant.analytics.products', compact('products'));
    }

    /**
     * Customer Insights
     */
    public function customerInsights()
    {
        $merchant = auth()->user();
        
        $customers = $merchant->customers()
            ->withCount('orders')
            ->withSum('orders', 'total_price')
            ->orderByDesc('orders_count')
            ->paginate(20);

        return view('merchant.analytics.customers', compact('customers'));
    }

    /**
     * Merchant Messages
     */
    public function messages()
    {
        $merchant = auth()->user();
        
        $conversations = $merchant->conversations()
            ->with('lastMessage')
            ->paginate(20);

        return view('merchant.messages.index', compact('conversations'));
    }

    /**
     * Customer Messages
     */
    public function customerMessages(Request $request)
    {
        $merchant = auth()->user();
        
        $query = Message::whereHas('conversation', function ($q) use ($merchant) {
            $q->where('merchant_id', $merchant->id);
        });

        // Optional filtering
        if ($request->has('customer_id')) {
            $query->where('sender_id', $request->customer_id)
                 ->orWhere('receiver_id', $request->customer_id);
        }

        $messages = $query->with('sender', 'receiver')
            ->latest()
            ->paginate(20);

        return view('merchant.messages.customers', compact('messages'));
    }

    /**
     * Send Message
     */
    public function sendMessage(Request $request)
    {
        $merchant = auth()->user();
        
        $validatedData = $request->validate([
            'customer_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'type' => 'nullable|in:text,image'
        ]);

        try {
            $message = Message::create([
                'sender_id' => $merchant->id,
                'receiver_id' => $validatedData['customer_id'],
                'message' => $validatedData['message'],
                'type' => $validatedData['type'] ?? 'text'
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Message sent successfully',
                'data' => $message
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Unable to send message: ' . $e->getMessage()
            ], 500);
        }
    }
}
