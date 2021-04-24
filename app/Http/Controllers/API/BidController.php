<?php

namespace App\Http\Controllers\API;

use App\Models\Bid;
use App\Models\User;
use App\Models\Lelang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\Fcm\BroadcastMessage;

class BidController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function store_bid(Request $request){
        $user = $this->auth::user();
        $bid = Bid::create([
            'lelang_id'=>$request->lelang_id,
            'user_id'=>$user->id,
            'nominal'=>$request->nominal
        ]);
        if(!$bid){
            return response()->json([
                'status'=>'failed',
                'message'=>'terjadi kesalahan'
            ]);
        }
        return response()->json([
            'message'=>'sukses Tersimpan'
        ], 200);
    }

    
}
