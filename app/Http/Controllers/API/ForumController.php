<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Fcm\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Bid;
use App\Models\Forum;
use App\Models\Lelang;
use Illuminate\Http\Request;

use Tymon\JWTAuth\Facades\JWTAuth;

class ForumController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function add_message(Request $request){
        $user=$this->auth::user();
        $forum=Forum::create([
            'lelang_id'=>$request->lelang_id,
            'user_id'=>$user->id,
            'user_name'=>$user->name,
            'message'=>$request->message,
        ]);
        $lelang_user = Bid::select('user_id')->where('lelang_id',$request->lelang_id)->distinct()->get();
        $forum_user = Forum::select('user_id')->where('lelang_id',$request->lelang_id)->distinct()->get();
        $all=[];

        foreach($lelang_user as $key){
            $forum_user->push($key);
        }
        
        foreach ($forum_user as $key) {
                array_push($all,$key->user_id);
        }
        $finally=array_unique($all);
        $token=[];
        foreach($finally as $key){
            $get=User::find($key)->fcm_token;
            array_push($token,$get);
        }
        return response()->json([
            'forum_user'=>$forum_user,
            'lelang_user'=>$lelang_user,  
            'finally'=>$finally,  
            'all'=>$all,  
            'token'=>$token,  
            
        ]); exit;
            
        if ($forum) {
            $pesan=Forum::where('lelang_id',$request->lelang_id)->get();
            if(count($token)>=1){
                BroadcastMessage::sendMessage($user->name, 'chat baru dari forum: '. $request->message, "forum/".$request->lelang_id, $token);
            }
            
            return response()->json([
                'chat'=>$pesan
            ],200);
        } else {
            return response()->json([
                'message'       => 'Error',
                'status_code'   => 500
            ],500);
        }
    }

    public function get_by_lelang(){
        $lelang = Lelang::find(request()->lelang_id);
        $pesan = Forum::where('lelang_id',request()->lelang_id)->paginate(20);
        return response()->json([
            'chat'=>$pesan
        ]);
    }
    
    
}
