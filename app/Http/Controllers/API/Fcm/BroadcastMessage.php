<?php

namespace App\Http\Controllers\API\Fcm;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;

use LaravelFCM\Message\OptionsBuilder;
use LaravelFCM\Message\PayloadDataBuilder;
use LaravelFCM\Message\PayloadNotificationBuilder;
use FCM;
use Tymon\JWTAuth\Facades\JWTAuth;

class BroadcastMessage extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    public static function sendMessage($sender, $message, $link, $token)
    {   

        $optionBuilder = new OptionsBuilder();
        $optionBuilder->setTimeToLive(60*20);

        $notificationBuilder = new PayloadNotificationBuilder('From ' . $sender);
        $notificationBuilder->setBody($message)
                            ->setSound('default')
                            ->setClickAction($link);

        $dataBuilder = new PayloadDataBuilder();
        $dataBuilder->addData(['sender' => $sender, 'message'=>$message, 'click_action'=>$link]);

        $option = $optionBuilder->build();
        $notification = $notificationBuilder->build();
        $data = $dataBuilder->build();

        // $tokens = User::all()->pluck('fcm_token')->toArray();

        $downstreamResponse = FCM::sendTo($token, $option, $notification, $data);

        return $downstreamResponse->numberSuccess();
    }
}
