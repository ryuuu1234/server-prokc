<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MediaLelang extends Model
{
    use HasFactory;

    protected $table="media_lelangs";
    protected $guarded = [];


    public function lelang()
    {
        return $this->hasOne(Lelang::class, 'id', 'lelang_id');
    }
}
