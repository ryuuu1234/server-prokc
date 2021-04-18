<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\MediaLelang;
use App\Models\VideoLelang;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class VideoLelangController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    
    public function hapus_video($id)
    {   
        $media_lelang = VideoLelang::find($id);
        $old_path = $media_lelang->image;
        Storage::delete('public/'.$old_path);
        $del = VideoLelang::where('id', $id)->delete();
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
        VideoLelang::where('id', $id)->update(['status'=>1]);
        
        return response()->json([
            'message' => 'success',
            'status_code' => 200,
        ], 200);
    }

    public function upload_video(Request $request){

        // return response()->json($request->hasFile('video'),200);
        // $old_path = $user->avatar;
        // Storage::delete('public/'.$old_path);
        
        if($request->hasFile('video')) {
            $request->validate([
                'video'=>'required|mimes:mp4,webm'
            ]);
            $path = $request->file('video')->store('videos', 'public');

            $media = VideoLelang::create([
                'lelang_id'=>$request->lelang_id,
                'video'=>$path,
            ]);

            if ($media) {
                return response()->json(['success'=> 'true'],200);
            } else {
                return response()->json(['success'=> 'failed'],500);
            }
            
        }

    }

    
}
