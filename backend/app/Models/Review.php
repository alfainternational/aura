<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id',
        'product_id',
        'rating',
        'comment',
        'images',
        'verified_purchase'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'rating' => 'integer',
        'images' => 'array',
        'verified_purchase' => 'boolean'
    ];

    /**
     * Relationship with User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relationship with Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Scope for Verified Purchases
     */
    public function scopeVerified($query)
    {
        return $query->where('verified_purchase', true);
    }

    /**
     * Get Review Images
     */
    public function getImagesAttribute($value)
    {
        return $value ? json_decode($value, true) : [];
    }

    /**
     * Get First Review Image
     */
    public function getFirstImageAttribute()
    {
        $images = $this->images;
        return !empty($images) ? $images[0] : null;
    }

    /**
     * Check if Review Has Images
     */
    public function hasImages()
    {
        return !empty($this->images);
    }

    /**
     * Get Formatted Rating
     */
    public function getFormattedRatingAttribute()
    {
        return str_repeat('★', $this->rating) . str_repeat('☆', 5 - $this->rating);
    }

    /**
     * Get Relative Time
     */
    public function getRelativeTimeAttribute()
    {
        return $this->created_at->diffForHumans();
    }
}
