<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\MediaLelang;
// use App\Models\Bank;
// use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class LelangController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    
    public function add_data()
    {       
        $cek = Lelang::count();
        $user_id = $this->auth::user()->id;
        $id_lelang = $this->generateId($cek);

        $save = Lelang::create([
            'user_id'=>$user_id,
            'id_lelang'=>$id_lelang,
        ]);

        if ($save) {
            return response()->json($save,200);
        } else {
            return response()->json([
                'message'       => 'Error',
                'status_code'   => 500
            ],500);
        }

    }

    public function data_last()
    {
        $user_id = $this->auth::user()->id;
        $data = Lelang::where('user_id', $user_id)->orderBy('id', 'DESC')->first();
        return response()->json($data,200); 
    }

    public function data_by($lelang_id)
    {
        $get = Lelang::find($lelang_id);
        $get->load('media_lelang:id,lelang_id,image,status');
        $get->load('video_lelang:id,lelang_id,video,status');
        $get->load('user');

        return response()->json($get,200);
    }

    public function update(Request $request, $id_lelang)
    {
        $update = Lelang::where('id', $id_lelang)->update([
            'judul' =>$request->judul,
            'kategori' =>$request->kategori,
            'berakhir' =>$request->berakhir,
            'harga_pembuka' =>$request->harga_pembuka,
            'kelipatan' =>$request->kelipatan,
            'deskripsi' =>$request->deskripsi,
        ]);

        if ($update) {
            return response()->json($update,200);
        }
    }

    public function hapus_by($id_lelang)
    {   
        $media = MediaLelang::where('lelang_id', $id_lelang)->get();
        foreach ($media as $key) {
            Storage::delete('public/'.$key->image);
        }
        MediaLelang::where('lelang_id', $id_lelang)->delete();
        $del = Lelang::where('id', $id_lelang)->delete();
        if ($del) {
            return response()->json(['success'=> 'true'],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
        
    }

    public function generateId($cek)
    {
        $tgl = date('mdy');
        $id_abal = $tgl.($cek + 1);
        return $id_abal;
    }

    public function get_by_id()
    {
        $id = $this->auth::user()->id;
        $get = Lelang::where('user_id', $id)->orderBy('id', 'DESC')->get();
        $get->load('media_lelang:id,lelang_id,image,status');
        $get->load('video_lelang:id,lelang_id,video,status');
        if ($get) {
            return response()->json(['success'=> 'true', 'data'=>$get],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
    }

    public function upload_image(Request $request){

        
        // $old_path = $user->avatar;
        // Storage::delete('public/'.$old_path);
        
        if($request->hasFile('image')) {
            $request->validate([
                'image'=>'required|image|mimes:jpeg,png,jpg'
            ]);
            $path = $request->file('image')->store('images', 'public');

            $media = MediaLelang::create([
                'lelang_id'=>$request->lelang_id,
                'image'=>$path,
            ]);

            if ($media) {
                return response()->json(['success'=> 'true'],200);
            } else {
                return response()->json(['success'=> 'failed'],500);
            }
            
        }

    }

    public function publish($id)
    {
        $publish = Lelang::where('id',$id)->update(['status'=>1]);
        if ($publish) {
            return response()->json(['success'=> 'true'],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
    }
    public function get_hampir()
    {   
        $date1 = request()->date1;
        $date2 = request()->date2;

        $lelang = Lelang::whereRaw(
            "(berakhir >= ? AND berakhir <= ?)", [$date1, $date2]
            )
            ->where('status', '>=', 1) // 1:publish
            // ->with( ['detail_order_one.product:id,name', 'details_bubuk', 'details_bubuk.bubuk:id,nama'])
            ->orderBy('berakhir', 'ASC')
            ->get();
        if ($lelang) {
            return response()->json(['success'=> 'true', 'data'=> $lelang],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
    }
    
}
