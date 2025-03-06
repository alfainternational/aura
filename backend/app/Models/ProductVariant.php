<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ProductVariant extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'product_id',
        'name',
        'sku',
        'price',
        'stock',
        'weight',
        'dimensions',
        'color',
        'size',
        'image'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'weight' => 'decimal:2',
        'dimensions' => 'array'
    ];

    /**
     * Boot method for generating SKU
     */
    protected static function boot()
    {
        parent::boot();

        // Generate SKU when creating
        static::creating(function ($variant) {
            if (!$variant->sku) {
                $variant->sku = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
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
     * Scope for Available Variants
     */
    public function scopeAvailable($query)
    {
        return $query->where('stock', '>', 0);
    }

    /**
     * Check Variant Availability
     */
    public function isAvailableAttribute()
    {
        return $this->stock > 0;
    }

    /**
     * Get Formatted Price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' SDG';
    }

    /**
     * Get Variant Display Name
     */
    public function getDisplayNameAttribute()
    {
        $displayParts = [];

        if ($this->color) {
            $displayParts[] = $this->color;
        }

        if ($this->size) {
            $displayParts[] = $this->size;
        }

        return $this->name . ($displayParts ? ' (' . implode(', ', $displayParts) . ')' : '');
    }

    /**
     * Get Variant Image URL
     */
    public function getImageUrlAttribute()
    {
        return $this->image ? url('storage/' . $this->image) : null;
    }

    /**
     * Get Dimensions Formatted
     */
    public function getFormattedDimensionsAttribute()
    {
        if (!$this->dimensions) {
            return null;
        }

        $dims = $this->dimensions;
        return sprintf(
            "%s x %s x %s cm", 
            $dims['length'] ?? 0, 
            $dims['width'] ?? 0, 
            $dims['height'] ?? 0
        );
    }
}
