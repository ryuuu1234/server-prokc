<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MediaLelang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class MediaLelangController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    
    public function hapus_image($id)
    {   
        $media_lelang = MediaLelang::find($id);
        $old_path = $media_lelang->image;
        Storage::delete('public/'.$old_path);
        $del = MediaLelang::where('id', $id)->delete();
        if ($del) {
            return response()->json([
                'message' => 'delete category successfully',
                'status_code' => 200,
            ], 200);
        } 
    }

    public function update_status()
    {
        $id= request()->id;
        $lelang_id= request()->lelang_id;

        MediaLelang::where('lelang_id', $lelang_id)->update(['status'=>0]);
        MediaLelang::where('id', $id)->update(['status'=>1]);
        
        return response()->json([
            'message' => 'success',
            'status_code' => 200,
        ], 200);
    }

    
}
