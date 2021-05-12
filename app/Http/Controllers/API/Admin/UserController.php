<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;



class UserController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function get_all_with_params()
    {
        $result = User::orderBy(request()->sortby, request()->sorting)
        ->when(request()->q, function($search){
            $search = $search->where('name', 'LIKE', '%' . request()->q . '%');
        })
        ->when(request()->status, function($status){
            $status = $status->where('status', '=' ,request()->status);
        })
        ->when(request()->bidder, function($bidder){
            $bidder = $bidder->where('bidder','=',request()->bidder);
        })
        ->paginate(request()->per_page);

        if (!$result) {
            return response()->json([
                'success'=>'failed',
            ],500); exit;
        }
        return response()->json([
            'success'=>'true',
            'result'=>$result,
        ],200);

    }


   
}
