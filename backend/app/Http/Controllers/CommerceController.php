<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\Category;
use App\Models\Order;

class CommerceController extends Controller
{
    /**
     * Display the main commerce services page
     */
    public function index()
    {
        return view('services.commerce.index');
    }

    /**
     * List available products
     */
    public function products()
    {
        // Fetch products with pagination
        $products = Product::with('category')
            ->where('status', 'active')
            ->paginate(12);

        // Fetch all categories for filtering
        $categories = Category::where('status', 'active')->get();

        return view('services.commerce.products', compact('products', 'categories'));
    }

    /**
     * List product categories
     */
    public function categories()
    {
        $categories = Category::where('status', 'active')
            ->withCount('products')
            ->paginate(12);

        return view('services.commerce.categories', compact('categories'));
    }

    /**
     * List user's orders
     */
    public function orders()
    {
        // Fetch user's orders with pagination
        $orders = Order::where('user_id', auth()->id())
            ->with('products')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('services.commerce.orders', compact('orders'));
    }

    /**
     * Display product details
     */
    public function productDetails($id)
    {
        $product = Product::with(['category', 'reviews'])
            ->findOrFail($id);

        return view('services.commerce.product-details', compact('product'));
    }
}
