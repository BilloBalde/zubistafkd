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
        return $this->customer
            ? $this->customer->customerName . '-' . $this->customer->mark
            : 'Client inconnu';
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

    public function order()
    {
        return $this->belongsTo(Order::class, 'numero_facture', 'invoice_number');
    }
}
