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
        'description',
    ];

    protected $hidden = [
        'password',
        'token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /** Administrateur (back-office complet). */
    public const ROLE_ADMIN = 1;

    /** Manager / superuser plateforme. */
    public const ROLE_MANAGER = 2;

    /** Gérant de boutique (tableau de bord magasin). */
    public const ROLE_STORE_MANAGER = 3;

    /** Client e-commerce (boutique en ligne uniquement). */
    public const ROLE_CUSTOMER = 4;

    public function isStaff(): bool
    {
        return in_array((int) $this->role_id, [self::ROLE_ADMIN, self::ROLE_MANAGER, self::ROLE_STORE_MANAGER], true);
    }

    public function isCustomer(): bool
    {
        return (int) $this->role_id === self::ROLE_CUSTOMER;
    }

    public function getRoleAttribute(){
        return $this->roleRelation?->slug ?? '';
    }

    public function roleRelation(){
        return $this->belongsTo(Role::class, 'role_id');
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
