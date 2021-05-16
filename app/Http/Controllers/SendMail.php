<?php

namespace App\Http\Controllers;

use App\Mail\EmailRegistrasi;
use App\Mail\SendEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class SendMail extends Controller
{
    public function index()
    {   
        $email='prokoiclub@gmail.com';
        $otp = 00456;
        $details = [
            'title'=> 'Registrasi Success',
            'otp'=> $otp,
        ];

        Mail::to($email)->send(new EmailRegistrasi($details));
        return "Email Sent";
    }
}
