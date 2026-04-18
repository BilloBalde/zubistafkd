<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;


    protected $fillable = [
        'name',
        'email',
        'password',
        'username',
        'profilePic',
        'role_id',
        'phone',
        'status',
        'token',
        'motdepasse',
        'description',
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getRoleAttribute(){

        $c = Role::find($this->role_id);
        return $c->slug;
    }

    public function factures(){
        return $this->hasMany(Facture::class);
    }

    public function addresses()
    {
        return $this->hasMany(DeliveryAddress::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function company()
{
    return $this->belongsTo(Company::class);
}
   
}
