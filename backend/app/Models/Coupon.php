<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Coupon extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'code',
        'description',
        'type',
        'value',
        'min_purchase',
        'max_discount',
        'start_date',
        'end_date',
        'usage_limit',
        'usage_count',
        'active',
        'applies_to'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'value' => 'decimal:2',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'active' => 'boolean',
        'applies_to' => 'array'
    ];

    /**
     * Boot method for generating coupon code
     */
    protected static function boot()
    {
        parent::boot();

        // Generate coupon code if not provided
        static::creating(function ($coupon) {
            if (!$coupon->code) {
                $coupon->code = strtoupper(Str::random(8));
            }
        });
    }

    /**
     * Scope for Active Coupons
     */
    public function scopeActive($query)
    {
        return $query->where('active', true)
            ->where('start_date', '<=', now())
            ->where('end_date', '>=', now());
    }

    /**
     * Check Coupon Validity
     */
    public function isValidAttribute()
    {
        // Check if coupon is active
        if (!$this->active) {
            return false;
        }

        // Check date range
        $now = Carbon::now();
        if ($now < $this->start_date || $now > $this->end_date) {
            return false;
        }

        // Check usage limit
        if ($this->usage_limit && $this->usage_count >= $this->usage_limit) {
            return false;
        }

        return true;
    }

    /**
     * Calculate Discount
     */
    public function calculateDiscount($subtotal)
    {
        // Check minimum purchase requirement
        if ($this->min_purchase && $subtotal < $this->min_purchase) {
            return 0;
        }

        // Calculate discount based on type
        $discount = 0;
        switch ($this->type) {
            case 'fixed':
                $discount = min($this->value, $subtotal);
                break;

            case 'percentage':
                $discount = $subtotal * ($this->value / 100);
                break;
        }

        // Apply max discount limit if set
        if ($this->max_discount) {
            $discount = min($discount, $this->max_discount);
        }

        return $discount;
    }

    /**
     * Check if Coupon Applies to Product
     */
    public function appliesToProduct(Product $product)
    {
        // If no specific applies_to is set, coupon applies to all products
        if (!$this->applies_to) {
            return true;
        }

        // Check if product matches any of the specified criteria
        $applies = false;
        foreach ($this->applies_to as $criteria) {
            switch ($criteria['type']) {
                case 'category':
                    $applies = $applies || $product->category_id == $criteria['value'];
                    break;

                case 'product':
                    $applies = $applies || $product->id == $criteria['value'];
                    break;

                case 'merchant':
                    $applies = $applies || $product->merchant_id == $criteria['value'];
                    break;
            }
        }

        return $applies;
    }

    /**
     * Get Coupon Type Label
     */
    public function getTypeLabelAttribute()
    {
        $typeLabels = [
            'fixed' => 'Fixed Amount',
            'percentage' => 'Percentage'
        ];

        return $typeLabels[$this->type] ?? $this->type;
    }

    /**
     * Get Formatted Value
     */
    public function getFormattedValueAttribute()
    {
        return $this->type === 'percentage' 
            ? $this->value . '%' 
            : number_format($this->value, 2) . ' SDG';
    }
}
