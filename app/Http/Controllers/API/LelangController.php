<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\Bid;
use App\Models\MediaLelang;
// use App\Models\Bank;
// use App\Models\Transaction;
use App\Models\User;
use App\Models\VideoLelang;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Carbon\Carbon;

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
        $data->load('bid');
        return response()->json($data,200); 
    }

    public function status_lelang(Request $request){
        $lelang = Lelang::find($request->id);
        // $lelang = Lelang::find(1);
        $now = strtotime(Carbon::now());
        $berakhir= strtotime($lelang->berakhir);
        $diff = $berakhir-$now;

        if($lelang->status==1 && $diff<=0){
            $bid=Bid::where('lelang_id',$lelang->id)->orderBy('created_at','DESC')->get();
            $ada=count($bid);
            if($ada>0){
                $lelang->update(['winner_id'=>$bid[0]->user_id, 'status'=>3]);
            }else{
                $lelang->update(['status'=>2]);
            }

            return response()->json([
                'data'=>$lelang,
                'id'=>$lelang->id,
                'now'=>$now,
                'berakhir'=>$berakhir,
                'diff'=>$diff,
                'bid'=>$bid,
                'ada'=>$ada,
            ]);
        }else{
            return response()->json([
                'data'=>$lelang,
                'id'=>$lelang->id,
                'now'=>$now,
                'berakhir'=>$berakhir,
                'diff'=>$diff,
                
            ]);

        }


    }

    public function data_by($lelang_id)
    {
        $get = Lelang::find($lelang_id);
        $get->load('media_lelang:id,lelang_id,image,status');
        $get->load('video_lelang:id,lelang_id,video,status');
        $get->load('user');
        $get->load('bid.bidder');

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
        // hapuus foto
        $media = MediaLelang::where('lelang_id', $id_lelang)->get();
        foreach ($media as $key) {
            Storage::delete('public/'.$key->image);
        }
        MediaLelang::where('lelang_id', $id_lelang)->delete();

        // hapus video
        $video = VideoLelang::where('lelang_id', $id_lelang)->get();
        foreach ($video as $row) {
            Storage::delete('public/'.$row->video);
        }
        VideoLelang::where('lelang_id', $id_lelang)->delete();

        // hapus lelang
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
        $get->load('bid.bidder');
        $get->load('hit');
        $get->load('winner');
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
    public function get_all_params()
    {   
        // $date1 = request()->date1;
        // $date2 = request()->date2;

        // $lelang = Lelang::whereRaw(
        //     "(berakhir >= ? AND berakhir <= ?)", [$date1, $date2]
        //     )
        //     ->where('status', '>=', 1) // 1:publish
        //     // ->with( ['detail_order_one.product:id,name', 'details_bubuk', 'details_bubuk.bubuk:id,nama'])
        //     ->orderBy('berakhir', 'ASC')
        //     ->get();

        $lelang = Lelang::where('status', '>=', 1)->orderBy(request()->sortby, request()->sort)
            ->when(request()->q, function($items) {
                $items = $items->where('kategori_id', 'LIKE', '%' . request()->q . '%');
        })->paginate(request()->per_page);
        $lelang->load('media_lelang:id,lelang_id,image,status');
        $lelang->load('video_lelang:id,lelang_id,video,status');
        $lelang->load('user');
        $lelang->load('bid');
        
        if ($lelang) {
            return response()->json(['success'=> 'true', 'data'=> $lelang],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
    }
    
}
