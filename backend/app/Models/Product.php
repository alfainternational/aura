<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'merchant_id',
        'category_id',
        'name',
        'description',
        'price',
        'stock',
        'images',
        'tags',
        'attributes',
        'status',
        'average_rating',
        'total_reviews',
        'weight'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'tags' => 'array',
        'attributes' => 'array',
        'images' => 'array',
        'average_rating' => 'decimal:2',
        'total_reviews' => 'integer',
        'weight' => 'decimal:2'
    ];

    /**
     * Boot method for generating slug
     */
    protected static function boot()
    {
        parent::boot();

        // Generate slug when creating or updating
        static::saving(function ($product) {
            $product->slug = Str::slug($product->name);
        });
    }

    /**
     * Relationship with Merchant
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Relationship with Category
     */
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Relationship with Reviews
     */
    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    /**
     * Relationship with Cart Items
     */
    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * Relationship with Order Items
     */
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship with Product Variants
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * Scope for Active Products
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    /**
     * Scope for Featured Products
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Check Product Availability
     */
    public function isAvailable()
    {
        return $this->status === 'active' && $this->stock > 0;
    }

    /**
     * Get Product Images
     */
    public function getImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Get First Product Image
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images;
        return !empty($images) ? $images[0] : null;
    }

    /**
     * Calculate Discount Percentage
     */
    public function getDiscountPercentageAttribute()
    {
        if (!$this->original_price || !$this->price) {
            return 0;
        }

        return round((($this->original_price - $this->price) / $this->original_price) * 100);
    }

    /**
     * Formatted Price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' SDG';
    }
}
