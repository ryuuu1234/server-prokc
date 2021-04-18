<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lelang extends Model
{
    use HasFactory;

    protected $table = 'lelangs';
    protected $guarded = [];

    public function media_lelang()
    {
        return $this->hasMany(MediaLelang::class, 'lelang_id', 'id');
    }

    public function video_lelang()
    {
        return $this->hasMany(VideoLelang::class, 'lelang_id', 'id');
    }

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id');
    }

}
