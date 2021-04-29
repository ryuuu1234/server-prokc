<?php

namespace App\Http\Controllers\API;

use App\Models\Hit;
use App\Models\Lelang;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tymon\JWTAuth\Facades\JWTAuth;

class HitController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function store(Request $req){
        $user=$this->auth::user();
        $lelang=Lelang::find($req->lelang_id);
        $count=Hit::where('lelang_id',$req->lelang_id)->first();
        // return response()->json([
        //     'count'=>$count,
        //     // 'hit'=>$hit,
        //     'user'=>$user,
        //     'lelang'=>$lelang,]); exit;
        if($count){       
            $value = $count->hits_count + 1;   
            $count->update(['hits_count'=>$value]);
        }else{
            $value=1;
            $hit=Hit::Create([
                'user_id'=>$user->id,
                'lelang_id'=>$req->lelang_id,
                'hits_count'=>$value,
                ]);
        }
        return response()->json([
            'message'=>'success',
            'user'=>$user,
            'lelang'=>$lelang,
        ],200);
    }

    public function get_hits(){
        $hit=His::where('lelang_id', request()->lelang_id)->first();
        return response()->json([
            'data'=>$hit
        ]);
    }
}
