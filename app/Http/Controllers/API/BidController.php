<?php

namespace App\Http\Controllers\API;

use App\Models\Bid;
use App\Models\User;
use App\Models\Lelang;
use DB;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Controllers\API\Fcm\BroadcastMessage;

use Tymon\JWTAuth\Facades\JWTAuth;

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

    public function get_bid(){
        // array of this user bids
        $user_bids=[];
        $data=[];
        
        //get bids that this user involved
        $user=$this->auth::user();
        // $bids=Bid::where('user_id',$user->id)->get();
        
        $single_bids=Bid::where('user_id',$user->id)
            ->select('lelang_id', DB::raw('count("lelang_id") as occurance'))
            ->groupBy('lelang_id')
            ->having('occurance','=',1)
            ->get();
        $multiple_bids=Bid::where('user_id',$user->id)
            ->select('lelang_id', DB::raw('count("lelang_id") as occurance'))
            ->groupBy('lelang_id')
            ->having('occurance','>',1)
            ->get();
        
            foreach ($single_bids as $key) {
                array_push($user_bids,$key->lelang_id);
            };
            foreach ($multiple_bids as $key) {
                array_push($user_bids,$key->lelang_id);
            };
            foreach ($user_bids as $key) {
                $lelang=Lelang::find($key);
                $lelang->load('bid.bidder');
                $lelang->load('hit');
                $lelang->load('winner');
                $lelang->load('media_lelang:id,lelang_id,image,status');
                $lelang->load('video_lelang:id,lelang_id,video,status');
                array_push($data,$lelang);
            };





        // $data=$user_bids;
        return response()->json([
            'data'=>$data
        ]);
    }
}
