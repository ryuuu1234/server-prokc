<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Fcm\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

// use LaravelFCM\Message\OptionsBuilder;
// use LaravelFCM\Message\PayloadDataBuilder;
// use LaravelFCM\Message\PayloadNotificationBuilder;
// use FCM;



class NotificationController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function post_to_midtrans(Request $request)
    {
        try {
            $notification_body = json_decode($request->getContent(), true);
            $invoice = $notification_body['order_id'];
            $transaction_id_mistrans = $notification_body['transaction_id'];

            $transaction = Transaction::where([
                'invoice'=> $invoice,
                'payment_token' => $transaction_id_mistrans 
            ])->firstOrFail();

                    
            if (!$transaction) {
                
                return response()->json(['message'=>'Terjadi Kesalahan']);
            }

            $status = $notification_body['transaction_status'];
            $transaction->status = $status;
            $transaction->save();

            // $topik = '{"type":"transaction","id":'.$transaction->id.'}';
            $topik = [
                'type'=> 'transaction',
                'id'=> $transaction->id,
            ];

            Notification::create([
                'user_id'=>$transaction->user_id,
                'sender'=> 'admin', 
                'title'=> 'Transaksi Anda', 
                'message'=> $status, 
                'link'=> 'transaksi', 
                'topik'=> json_encode($topik)
            ]);

            $token = [];
            $get_token = User::find($transaction->user_id)->fcm_token;
            array_push($token, $get_token);

            // $token = User::find($transaction->user_id)->pluck('fcm_token')->toArray();

            BroadcastMessage::sendMessage('Admin', 'Transaksi '.$invoice, 'detail.transaksi/'.$transaction->id, $token);

            return response()->json('Ok', 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            dd($e);
            return response()->json('Error', 404)->header('Content-Type', 'text/plain');
        }
    }

    public function post_from_client(Request $request)
    {   
        $body = json_decode($request->getContent() , true);
        $topik = json_encode($body['topik']);
        // $save = Notification::firstOrcreate(['topik'=>json_encode($coba)]);
        $to = $request->to;
        if ($to == 'client' || $to=='admin') {
            $user = User::where('roles', $to)->get();
            $token = User::where('roles', $to)->pluck('fcm_token')->toArray();
        } else {
            $user = User::whereIn('id', $request->to)->get();
            $token = User::whereIn('id', $to)->pluck('fcm_token')->toArray();
        }

        try {
            $token = [];
            foreach ($user as $key) {
                Notification::create([
                    'user_id'=>$key->id,
                    'sender'=> $this->auth::user()->name, 
                    'title'=> $request->title, 
                    'message'=> $request->message, 
                    'link'=> $request->link, 
                    'topik'=> $topik
                ]);
                $get_token = User::find($key->user_id)->fcm_token;
                array_push($token, $get_token);    
            }
            BroadcastMessage::sendMessage($this->auth::user()->name, $request->message, $request->link, $token);
            return response()->json(['message'=>'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message'=>'failed', 'result'=>$e]);
        }
    }

    public function get_notif_by_id(){
        $user = User::find(request()->id);
        $notif = Notification::orderBy('id','DESC')->where('user_id', $user->id)->paginate(20);
        return response()->json([
            'user'=>$user,
            'notifications'=>$notif
        ]);
    }

    public function get_notif_by_current_id(){
        $user = User::find($this->auth::user()->id);
        $notif = Notification::orderBy('id','DESC')->where('user_id', $user->id)->paginate(20);
        return response()->json([
            'user'=>$user,
            'notifications'=>$notif
        ]);
    }

    public function mark_as_read(Request $request){
        
        try{
            $update = Notification::where('id', $request->id)->update(['readed'=> 1]);
            if (!$update) {
                return response()->json(['satus'=>'failed', 'message'=>'update salah, coba ulangi']);
            }
            $notif = Notification::orderBy('id','DESC')->where('readed', 0)->where('user_id', $request->user_id)->get();
            return response()->json(['status'=>'Success', 'notifications' => $notif], 200);
        } catch (\Exception $e){
            return response()->json([
                'status'=>'failed',
                'message'=> $e->getMessage()
            ],400);
        }
    }

    public function mark_as_read_all(Request $request){
        
        $user = $this->auth::user();
        try{
            $update = Notification::where('user_id', $user->id)->update(['readed'=> 1]);
            if (!$update) {
                return response()->json(['satus'=>'failed', 'message'=>'update salah, coba ulangi']);
            }
            $notif = Notification::orderBy('id','DESC')->where('readed', 0)->where('user_id', $request->user_id)->get();
            return response()->json(['status'=>'Success', 'notifications' => $notif], 200);
        } catch (\Exception $e){
            return response()->json([
                'status'=>'failed',
                'message'=> $e->getMessage()
            ],400);
        }
    }
    

    
}
