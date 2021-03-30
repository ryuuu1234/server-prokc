<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Laravel\Socialite\Facades\Socialite;


class SocialLoginController extends Controller
{
    public function redirect($service)
    {
        return Socialite::driver($service)->stateless()->redirect();
    }

    public function callback($service)
    {
        $serviceUser = Socialite::driver($service)->stateless()->user();
        dd($serviceUser);
    }
}