<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $casts = [
        'promo_ends_at' => 'datetime',
        'is_promo'      => 'boolean',
        'is_best'       => 'boolean',
    ];

    public function scopeActivePromo($query)
    {
        return $query->whereNotNull('promo_price')
            ->where(function ($q) {
                $q->whereNull('promo_ends_at')
                  ->orWhere('promo_ends_at', '>', now());
            });
    }

    public function categories()
    {
        return $this->belongsToMany(Category::class, 'category_products');
    }

    public function stores()
    {
        return $this->belongsToMany(Store::class, 'store_products')->withPivot('quantity');
    }

    // app/Models/Product.php
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

}
