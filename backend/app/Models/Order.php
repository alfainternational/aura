<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'shipping_address_id',
        'status',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'delivery_instructions',
        'tracking_number'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'subtotal' => 'decimal:2',
        'discount' => 'decimal:2',
        'total' => 'decimal:2'
    ];

    /**
     * Boot method for generating order number
     */
    protected static function boot()
    {
        parent::boot();

        // Generate unique order number
        static::creating(function ($order) {
            $order->order_number = 'AUR-' . strtoupper(Str::random(8));
        });
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Shipping Address
     */
    public function shippingAddress()
    {
        return $this->belongsTo(ShippingAddress::class);
    }

    /**
     * Relationship with Order Items
     */
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    /**
     * Relationship with Payments
     */
    public function payments()
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Scope for Specific Order Status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Get Merchant-Specific Order Items
     */
    public function getMerchantItemsAttribute($merchantId)
    {
        return $this->items()->whereHas('product', function ($query) use ($merchantId) {
            $query->where('merchant_id', $merchantId);
        })->get();
    }

    /**
     * Check if Order is Cancellable
     */
    public function isCancellableAttribute()
    {
        $cancellableStatuses = ['pending', 'processing'];
        return in_array($this->status, $cancellableStatuses);
    }

    /**
     * Get Formatted Total
     */
    public function getFormattedTotalAttribute()
    {
        return number_format($this->total, 2) . ' SDG';
    }

    /**
     * Get Order Status Label
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'Pending',
            'processing' => 'Processing',
            'shipped' => 'Shipped',
            'delivered' => 'Delivered',
            'cancelled' => 'Cancelled'
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Get Estimated Delivery Date
     */
    public function getEstimatedDeliveryDateAttribute()
    {
        if ($this->status === 'shipped') {
            return $this->created_at->addDays(3)->format('Y-m-d');
        }

        return null;
    }
}
