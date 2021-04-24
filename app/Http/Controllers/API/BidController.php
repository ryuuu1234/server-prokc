<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Fcm\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bid;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;

class BidController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

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
    
    
}
