<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getProduitAttribute(){

        $c = Product::find($this->product_id);
        return $c->libelle;
    }

    public function getProduitImageAttribute(){
        $p = Product::find($this->product_id);
        return $p->image;
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function store(){
        return $this->belongsTo(Store::class);
    }

}
