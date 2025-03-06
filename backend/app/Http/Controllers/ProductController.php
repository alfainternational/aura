<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProductController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * List Products
     */
    public function index(Request $request)
    {
        $query = Product::query();

        // Filtering options
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        if ($request->has('merchant_id')) {
            $query->where('merchant_id', $request->merchant_id);
        }

        if ($request->has('min_price')) {
            $query->where('price', '>=', $request->min_price);
        }

        if ($request->has('max_price')) {
            $query->where('price', '<=', $request->max_price);
        }

        // Sorting
        $sortBy = $request->input('sort_by', 'created_at');
        $sortDirection = $request->input('sort_direction', 'desc');
        $query->orderBy($sortBy, $sortDirection);

        $products = $query->paginate(20);
        $categories = Category::all();

        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Search Products
     */
    public function search(Request $request)
    {
        $query = Product::query();

        // Search by name or description
        if ($request->has('q')) {
            $searchTerm = $request->q;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'LIKE', "%{$searchTerm}%")
                  ->orWhere('description', 'LIKE', "%{$searchTerm}%");
            });
        }

        // Additional filtering options
        if ($request->has('category')) {
            $query->whereHas('category', function ($q) use ($request) {
                $q->where('slug', $request->category);
            });
        }

        $products = $query->paginate(20);
        $categories = Category::all();

        return view('products.search', compact('products', 'categories'));
    }

    /**
     * Show Product Details
     */
    public function show(Product $product)
    {
        $product->load('merchant', 'category', 'reviews');
        $relatedProducts = Product::where('category_id', $product->category_id)
            ->where('id', '!=', $product->id)
            ->take(4)
            ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }

    /**
     * Create Product Form (Merchant)
     */
    public function create()
    {
        $merchant = auth()->user();
        
        // Ensure user is a merchant
        if ($merchant->user_type !== 'merchant') {
            abort(403, 'Unauthorized');
        }

        $categories = Category::all();

        return view('products.create', compact('categories'));
    }

    /**
     * Store New Product (Merchant)
     */
    public function store(Request $request)
    {
        $merchant = auth()->user();
        
        // Ensure user is a merchant
        if ($merchant->user_type !== 'merchant') {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'attributes' => 'nullable|array'
        ]);

        try {
            // Create product
            $product = $merchant->products()->create([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'category_id' => $validatedData['category_id'],
                'price' => $validatedData['price'],
                'stock' => $validatedData['stock'],
                'tags' => json_encode($validatedData['tags'] ?? []),
                'attributes' => json_encode($validatedData['attributes'] ?? []),
                'status' => 'active'
            ]);

            // Handle product images
            if ($request->hasFile('images')) {
                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $imageUrls[] = $path;
                }
                $product->images = json_encode($imageUrls);
                $product->save();
            }

            return redirect()->route('merchant.products.show', $product)
                ->with('success', 'Product created successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to create product: ' . $e->getMessage());
        }
    }

    /**
     * Edit Product Form (Merchant)
     */
    public function edit(Product $product)
    {
        $merchant = auth()->user();
        
        // Ensure the product belongs to the merchant
        if ($product->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized');
        }

        $categories = Category::all();

        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update Product (Merchant)
     */
    public function update(Request $request, Product $product)
    {
        $merchant = auth()->user();
        
        // Ensure the product belongs to the merchant
        if ($product->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized');
        }

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'images' => 'nullable|array|max:5',
            'images.*' => 'image|mimes:jpeg,png,jpg,gif|max:2048',
            'tags' => 'nullable|array',
            'attributes' => 'nullable|array',
            'status' => 'required|in:active,inactive,out_of_stock'
        ]);

        try {
            // Update product details
            $product->update([
                'name' => $validatedData['name'],
                'description' => $validatedData['description'],
                'category_id' => $validatedData['category_id'],
                'price' => $validatedData['price'],
                'stock' => $validatedData['stock'],
                'tags' => json_encode($validatedData['tags'] ?? []),
                'attributes' => json_encode($validatedData['attributes'] ?? []),
                'status' => $validatedData['status']
            ]);

            // Handle product images
            if ($request->hasFile('images')) {
                // Remove old images
                if ($product->images) {
                    $oldImages = json_decode($product->images, true);
                    foreach ($oldImages as $oldImage) {
                        Storage::disk('public')->delete($oldImage);
                    }
                }

                // Store new images
                $imageUrls = [];
                foreach ($request->file('images') as $image) {
                    $path = $image->store('products', 'public');
                    $imageUrls[] = $path;
                }
                $product->images = json_encode($imageUrls);
                $product->save();
            }

            return redirect()->route('merchant.products.show', $product)
                ->with('success', 'Product updated successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to update product: ' . $e->getMessage());
        }
    }

    /**
     * Delete Product (Merchant)
     */
    public function destroy(Product $product)
    {
        $merchant = auth()->user();
        
        // Ensure the product belongs to the merchant
        if ($product->merchant_id !== $merchant->id) {
            abort(403, 'Unauthorized');
        }

        try {
            // Remove product images
            if ($product->images) {
                $images = json_decode($product->images, true);
                foreach ($images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }

            // Delete the product
            $product->delete();

            return redirect()->route('merchant.products')
                ->with('success', 'Product deleted successfully');
        } catch (\Exception $e) {
            return redirect()->back()
                ->with('error', 'Unable to delete product: ' . $e->getMessage());
        }
    }
}
