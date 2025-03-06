<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'product_variant_id',
        'quantity'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer'
    ];

    /**
     * Relationship with Cart
     */
    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relationship with Product Variant
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /**
     * Calculate Item Total
     */
    public function getTotalAttribute()
    {
        return $this->product->price * $this->quantity;
    }

    /**
     * Get Item Details with Variant
     */
    public function getDetailsAttribute()
    {
        $details = [
            'product' => $this->product,
            'quantity' => $this->quantity,
            'total' => $this->total
        ];

        if ($this->variant) {
            $details['variant'] = $this->variant;
        }

        return $details;
    }

    /**
     * Check Product Availability
     */
    public function isAvailableAttribute()
    {
        return $this->product->isAvailable() && 
               $this->quantity <= $this->product->stock;
    }
}
