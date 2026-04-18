<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Logistic extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function store(){
        return $this->belongsTo(Store::class);
    }
    // Define the inverse of the relationship with Category model
    // public function category()
    // {
    //     return $this->belongsTo(Category::class);
    // }
}
