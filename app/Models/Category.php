<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $fillable = ['slug', 'category_type', 'description', 'image'];

    public function getNameAttribute(): string
    {
        return $this->category_type ?? $this->slug;
    }

    public function products()
    {
        return $this->belongsToMany(Product::class, 'category_products');
    }
     // Define the relationship with Logistic model
      public function logistics()
      {
          return $this->hasMany(Logistic::class);
      }
}
