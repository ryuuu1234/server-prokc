<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VideoLelang extends Model
{
    use HasFactory;

    protected $table="video_lelangs";
    protected $guarded = [];


    public function lelang()
    {
        return $this->hasOne(Lelang::class, 'id', 'lelang_id');
    }
}
