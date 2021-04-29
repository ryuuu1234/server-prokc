<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Hit extends Model
{
    use HasFactory;
    protected $table = 'hits';
    protected $guarded = [];
    
    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    public function lelang()
    {
        return $this->hasOne(Lelang::class, 'id', 'lelang_id');
    }
}
