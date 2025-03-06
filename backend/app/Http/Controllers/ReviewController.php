<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('check-role:customer')->only(['store', 'customerReviews']);
        $this->middleware('check-role:merchant')->only('merchantReviews');
    }

    /**
     * List Product Reviews
     */
    public function index(Product $product)
    {
        $reviews = $product->reviews()
            ->with('user')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.index', compact('product', 'reviews'));
    }

    /**
     * Store Product Review (Customer)
     */
    public function store(Request $request, Product $product)
    {
        $customer = auth()->user();
        
        // Validate review input
        $validatedData = $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images' => 'nullable|array|max:3',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        try {
            // Check if customer has purchased the product
            $hasPurchased = $customer->orders()
                ->whereHas('items', function ($query) use ($product) {
                    $query->where('product_id', $product->id);
                })
                ->exists();

            if (!$hasPurchased) {
                return redirect()->back()
                    ->with('error', 'You can only review products you have purchased');
            }

            // Check if customer has already reviewed this product
            $existingReview = $customer->reviews()
                ->where('product_id', $product->id)
                ->first();

            if ($existingReview) {
                return redirect()->back()
                    ->with('error', 'You have already reviewed this product');
            }

            // Create review
            $review = $customer->reviews()->create([
                'product_id' => $product->id,
                'rating' => $validatedData['rating'],
                'comment' => $validatedData['comment'] ?? null
            ]);

            // Handle review images
            if ($request->hasFile('images')) {
                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('reviews', 'public');
                    $imageUrls[] = $path;
                }
                $review->images = json_encode($imageUrls);
                $review->save();
            }

            // Update product average rating
            $this->updateProductRating($product);

            return redirect()->route('products.show', $product)
                ->with('success', 'Review submitted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to submit review: ' . $e->getMessage());
        }
    }

    /**
     * Customer Reviews
     */
    public function customerReviews()
    {
        $customer = auth()->user();
        
        $reviews = $customer->reviews()
            ->with('product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.customer', compact('reviews'));
    }

    /**
     * Merchant Reviews
     */
    public function merchantReviews()
    {
        $merchant = auth()->user();
        
        $reviews = Review::whereHas('product', function ($query) use ($merchant) {
                $query->where('merchant_id', $merchant->id);
            })
            ->with('user', 'product')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('reviews.merchant', compact('reviews'));
    }

    /**
     * Update Product Average Rating
     */
    private function updateProductRating(Product $product)
    {
        $averageRating = $product->reviews()->avg('rating');
        $totalReviews = $product->reviews()->count();

        $product->update([
            'average_rating' => round($averageRating, 2),
            'total_reviews' => $totalReviews
        ]);
    }
}
