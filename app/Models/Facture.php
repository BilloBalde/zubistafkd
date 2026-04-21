<?php

namespace App\Models;

use App\Models\Facture;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getCustomerNameAttribute(){
        $client = Customer::find($this->customer_id);
        return $client ? $client->customerName . '-' . $client->mark : 'Client inconnu';
    }

    public function store(){
        return $this->belongsTo(Store::class);
    }

    public function user(){
        return $this->belongsTo(User::class);
    }

    public function customer(){
        return $this->belongsTo(Customer::class);
    }

    public function paiements() {
        return $this->hasMany(Payment::class);
    }
    
    // app/Models/Sale.php
    public function facture()
    {
        return $this->hasOne(Facture::class, 'numero_facture', 'numeroFacture');
    }
}
