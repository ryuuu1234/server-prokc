<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;



class DataTransaksi extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function get_all_with_params()
    {
        
        $data = Transaction::orderBy(request()->sortby, request()->sorting)
        ->when(request()->q != '', function($search){
            $i = 0;
            $column_search = ['invoice', 'status', 'jenis'];
            foreach ($column_search as $item ) {
                if ($i === 0) {
                    $search->where($item, 'LIKE', '%' . request()->q . '%');
                }else{
                    $search->orWhere($item, 'LIKE', '%' . request()->q . '%');
                }
                $i++;
            }
            
        })
        ->paginate(request()->per_page);
        $data->load('user');
        $data->load('penarikan');

        if (!$data) {
            return response()->json([
                'message'=>'failed',
            ],500); exit;
        }
        return response()->json([
            'message'=>'true',
            'result'=>$data,
        ],200);

    }


   
}
