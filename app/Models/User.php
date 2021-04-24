<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar',
        'notelp',
        'nowhatsapp',
        'alamat',
        'provinsi',
        'kota',
        'status',
        'bidder',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function getJWTIdentifier()
    {
       return $this->getKey();
    }
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function social()
    {
        return $this->hasMany(UserSocial::class, 'user_id', 'id');
    }

    public function hasSocialLinked($service)
    {
        return (bool) $this->social->where('service', $service)->count();
    }

    // transactions
    public function transaction()
    {
        return $this->hasMany(Transaction::class, 'user_id', 'id');
    }

    public function lelang()
    {
        return $this->hasMany(Lelang::class, 'user_id', 'id');
    }

    public function bid()
    {
        return $this->hasMany(Bid::class, 'user_id', 'id');
    }

}
