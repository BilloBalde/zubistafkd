<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Facture extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    public function getCustomerNameAttribute(){
        $client = Customer::find($this->customer_id);
        return $client->customerName.'-'.$client->mark;
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
}
