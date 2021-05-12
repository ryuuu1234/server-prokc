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
        $result = Lelang::orderBy(request()->sortby, request()->sorting)
        ->when(request()->q, function($search){
            $search = $search->where('kategori', 'LIKE', '%' . request()->q . '%');
        })
        ->paginate(request()->per_page);

        if (!$result) {
            return response()->json([
                'message'=>'failed',
            ],500); exit;
        }
        return response()->json([
            'message'=>'true',
            'result'=>$result,
        ],200);

    }


   
}
