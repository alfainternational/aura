<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'order_id',
        'amount',
        'method',
        'status',
        'external_id',
        'details'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'details' => 'array'
    ];

    /**
     * Relationship with Order
     */
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for Specific Payment Status
     */
    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    /**
     * Scope for Payment Method
     */
    public function scopeMethod($query, $method)
    {
        return $query->where('method', $method);
    }

    /**
     * Get Formatted Amount
     */
    public function getFormattedAmountAttribute()
    {
        return number_format($this->amount, 2) . ' SDG';
    }

    /**
     * Get Payment Status Label
     */
    public function getStatusLabelAttribute()
    {
        $statusLabels = [
            'pending' => 'Pending',
            'completed' => 'Completed',
            'failed' => 'Failed',
            'refunded' => 'Refunded'
        ];

        return $statusLabels[$this->status] ?? $this->status;
    }

    /**
     * Get Payment Method Label
     */
    public function getMethodLabelAttribute()
    {
        $methodLabels = [
            'wallet' => 'Wallet',
            'card' => 'Credit/Debit Card',
            'cash_on_delivery' => 'Cash on Delivery'
        ];

        return $methodLabels[$this->method] ?? $this->method;
    }

    /**
     * Check if Payment is Refundable
     */
    public function isRefundableAttribute()
    {
        $refundableStatuses = ['completed', 'pending'];
        return in_array($this->status, $refundableStatuses);
    }
}
