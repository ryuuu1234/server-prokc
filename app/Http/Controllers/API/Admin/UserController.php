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
        ->when(request()->status != '', function($status){
            $status->where('status', '=' ,request()->status);
        })
        ->when(request()->bidder !='', function($bidder){
            $bidder->where('bidder','=',request()->bidder);
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

    public function kick_back_user(Request $request)
    {   
        $id = $request->id;
        $user = User::where('id',$id)->first();
        $status = $user->status;
        try {
            if ($status == 1) {
                User::where('id', $id)->update([
                    'status'=>0
                ]);
            } else {
                User::where('id', $id)->update([
                    'status'=>1
                ]);
            }
            return response()->json(['success'=>true, 'result'=>$user],200);
        } catch (\Exception $e) {
            return response()->json(['success'=>false, 'message'=>$e],500);
        }

    }


   
}
