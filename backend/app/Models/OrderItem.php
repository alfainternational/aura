<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'product_id',
        'product_variant_id',
        'merchant_id',
        'quantity',
        'price'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'quantity' => 'integer',
        'price' => 'decimal:2'
    ];

    /**
     * Relationship with Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
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
     * Relationship with Merchant
     */
    public function merchant()
    {
        return $this->belongsTo(User::class, 'merchant_id');
    }

    /**
     * Calculate Item Total
     */
    public function getTotalAttribute()
    {
        return $this->price * $this->quantity;
    }

    /**
     * Get Item Details with Variant
     */
    public function getDetailsAttribute()
    {
        $details = [
            'product' => $this->product,
            'quantity' => $this->quantity,
            'price' => $this->price,
            'total' => $this->total
        ];

        if ($this->variant) {
            $details['variant'] = $this->variant;
        }

        return $details;
    }

    /**
     * Get Formatted Price
     */
    public function getFormattedPriceAttribute()
    {
        return number_format($this->price, 2) . ' SDG';
    }

    /**
     * Get Formatted Total
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' SDG';
    }
}
