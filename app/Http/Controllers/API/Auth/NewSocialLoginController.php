<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\UserSocial;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\InvalidStateException;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Facades\Storage;
use File;
use Illuminate\Http\Request;

class NewSocialLoginController extends Controller
{   
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    public function social_login(Request $request){

        // $serviceUser = $request->all();
        $email = $request->email;
        $provider = $request->providerId;
        $user = $this->getExistingUser($request, $email, $provider);

             if (!$user) {
           
                $user = User::create(
                    [   
                        'email' => $email,
                        'name' => $request->displayName,
                        'password' => '',
                        'avatar' => $request->photoURL,
                        'roles' => 'client',
                    ]
                );
            }


            if ($this->needsToCreateSocial($user, $provider)) {
                UserSocial::create([
                    'user_id' => $user->id,
                    'social_id' => $request->uid,
                    'service' => $provider
                ]);
            }

        return $this->auth::fromUser($user);
    }

    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    public function getExistingUser($serviceUser ,$email, $provider)
    {
       
        $user = User::where('email', $email)->orWhereHas('social', function($q) use ($serviceUser, $provider) {
                $q->where('social_id', $serviceUser->uid)->where('service', $provider);
            })->first();
        
            // $userSocial = UserSocial::where('social_id', $serviceUser->getId())->first();
        return $user ? $user : null;
        
    }
}