<?php

namespace App\Http\Controllers\API;

<<<<<<< HEAD
use App\Models\Bid;
use App\Models\User;
use App\Models\Lelang;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;
use App\Http\Controllers\API\Fcm\BroadcastMessage;
=======
use App\Http\Controllers\API\Fcm\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bid;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;
>>>>>>> c8f956934706dbdb14a5ad464e2001c6a9e1d3ed

class BidController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

<<<<<<< HEAD
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

=======
    public function store_bid(Request $request)
    {
        $user = $this->auth::user();
        
        $bid = Bid::create([
            'lelang_id' => $request->lelang_id,
            'user_id' => $user->id,
            'nominal' => $request->nominal,
        ]);

        if (!$bid) {
            return response()->json(['status'=>'Failed', 'message'=>'Terjadi kesalahan']);
        }
        return response()->json(['status'=>'Success', 'message'=>'Success tersimpan'], 200);
    }
    
>>>>>>> c8f956934706dbdb14a5ad464e2001c6a9e1d3ed
    
}
