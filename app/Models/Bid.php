<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Bid extends Model
{
    use HasFactory;

    protected $table = 'bids';

    protected $guarded = [];

    public function lelang()
    {
        return $this->hasOne(Lelang::class, 'id', 'lelang_id');
    }
    public function bidder()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }
} 

