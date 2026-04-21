<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;
    


    // app/Models/Sale.php
    protected $fillable = [
        'numeroFacture',
        'product_id',
        'store_id',
        'quantity',
        'prix',
        'prixtotal',
        'interet',
    ];

    public function getProduitAttribute(){
        $c = Product::find($this->product_id);
        return $c->libelle ?? 'Produit inconnu';
    }

    public function getProduitImageAttribute(){
        $p = Product::find($this->product_id);
        return $p->image ?? 'default.png';
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
    
    public function store(){
        return $this->belongsTo(Store::class);
    }

}
