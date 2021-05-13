<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;



class DataLelang extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function get_all_with_params()
    {
        
        $lelang = Lelang::orderBy(request()->sortby, request()->sorting)
        ->when(request()->q != '', function($search){
            // coba dynamic filter by
            $i = 0;
            $column_search = ['kategori', 'judul', 'id_lelang'];
            foreach ($column_search as $item ) {
                if ($i === 0) {
                    $search->where($item, 'LIKE', '%' . request()->q . '%');
                }else{
                    $search->orWhere($item, 'LIKE', '%' . request()->q . '%');
                }
                // if($column_search->count() -1 == $i)
                $i++;
            }
            
        })
        ->when(request()->status !='', function($status){
            $status->where('status','=',request()->status);
        })
        ->paginate(request()->per_page);
        $lelang->load('media_lelang:id,lelang_id,image,status');
        $lelang->load('video_lelang:id,lelang_id,video,status');
        $lelang->load('user');
        $lelang->load('bid');

        if (!$lelang) {
            return response()->json([
                'message'=>'failed',
            ],500); exit;
        }
        return response()->json([
            'message'=>'true',
            'result'=>$lelang,
        ],200);

    }


   
}
