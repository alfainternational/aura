<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Category extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'description',
        'slug',
        'parent_id',
        'icon',
        'is_active'
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Boot method for generating slug
     */
    protected static function boot()
    {
        parent::boot();

        // Generate slug when creating or updating
        static::saving(function ($category) {
            $category->slug = Str::slug($category->name);
        });
    }

    /**
     * Relationship with Parent Category
     */
    public function parent()
    {
        return $this->belongsTo(Category::class, 'parent_id');
    }

    /**
     * Relationship with Child Categories
     */
    public function children()
    {
        return $this->hasMany(Category::class, 'parent_id');
    }

    /**
     * Relationship with Products
     */
    public function products()
    {
        return $this->hasMany(Product::class);
    }

    /**
     * Scope for Active Categories
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope for Top-Level Categories
     */
    public function scopeTopLevel($query)
    {
        return $query->whereNull('parent_id');
    }

    /**
     * Get Category Breadcrumbs
     */
    public function getBreadcrumbsAttribute()
    {
        $breadcrumbs = collect();
        $category = $this;

        while ($category) {
            $breadcrumbs->prepend($category);
            $category = $category->parent;
        }

        return $breadcrumbs;
    }

    /**
     * Get Total Product Count (Recursive)
     */
    public function getTotalProductCountAttribute()
    {
        $count = $this->products()->count();

        // Add products from child categories
        foreach ($this->children as $childCategory) {
            $count += $childCategory->total_product_count;
        }

        return $count;
    }

    /**
     * Get Category Icon URL
     */
    public function getIconUrlAttribute()
    {
        return $this->icon ? url('storage/' . $this->icon) : null;
    }
}
