<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Notification;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;

use App\Jobs\BroacastMessage;

class NotificationController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    //    $this->middleware('jwt.auth', ['except' => ['post_to_midtrans']]); 
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
            ])->first();


            if (!$transaction) {
                return response()->json(['message'=>'Terjadi Kesalahan']);
            }

            $status = $notification_body['transaction_status'];
            $transaction->status = $status;

            // switch($status_code){
            //     case '200';
            //         $transaction->status = 3; //'SUCCESS'
            //         break;
            //     case '201';
            //         $transaction->status = 1; //PENDING
            //         break;
            //     case '202';
            //         $transaction->status = 2; //CANCEL
            //         break;
            // }

            $transaction->save();

            return response()->json('Ok', 200)->header('Content-Type', 'text/plain');
        } catch (\Exception $e) {
            return response()->json('Error', 404)->header('Content-Type', 'text/plain');
        }
    }

    public function post_from_client(Request $request)
    {   
        $body = json_decode($request->getContent() , true);
        $topik = json_encode($body['topik']) ;
        // $save = Notification::firstOrcreate(['topik'=>json_encode($coba)]);
        $to = $request->to;
        if ($to == 'client' || $to=='admin') {
            $user = User::where('roles', $to)->get();
            $token = User::where('roles', $to)->pluck('fcm_token')->toArray();
        } else {
            $user = User::whereIn('id', $request->to)->get();
            $token = User::whereIn('id', $to)->pluck('fcm_token')->toArray();
        }

        // $this->broadcastMessage($this->auth::user()->name, $request->message, $request->link, $token); 
        try {
            foreach ($user as $key) {
                Notification::create([
                    'user_id'=>$key->id,
                    'sender'=> $this->auth::user()->name, 
                    'title'=> 'percobaan', 
                    'message'=> $request->message, 
                    'link'=> $request->message, 
                    'topik'=> $topik
                    ]);
            }

            $this->broadcastMessage($this->auth::user()->name, $request->message, $request->link, $token);
            $notification = json([
                'sender' => $this->auth::user()->name,
                'message'=>$request->message,
                'link'=>$request->link,
                'token'=>$token 
                ]);
            return response()->json(['message'=>'success'], 200);
        } catch (\Exception $e) {
            return response()->json(['message'=>'failed', 'result'=>$e]);
        }
    }

    private function broadcastMessage($sender, $message, $link, $token){
        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('From ' . $sender);
        $notificationBuilder->setBody($message)
                            ->setSound('default')
                            ->setClickAction($link);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['sender' => $sender, 'message'=>$message]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // $tokens = User::all()->pluck('fcm_token')->toArray();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        return $downstreamResponse->numberSuccess();

    }

    
    

    
}