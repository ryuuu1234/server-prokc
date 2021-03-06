<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Forum extends Model
{
    use HasFactory;
    protected $table = 'forums';
    protected $guarded = [];


    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

    
}
