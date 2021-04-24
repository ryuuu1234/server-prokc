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

class SocialLoginController extends Controller
{   
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
        //$this->middleware(['social', 'web']);
    }
    public function redirect($service)
    {
        return Socialite::driver($service)->stateless()->redirect();
    }

    public function callback($service)
    {   
        try {
            $serviceUser = Socialite::driver($service)->stateless()->user();
        } catch (\Exception $e) {
            return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?error=Unable to login using ' . $service . '. Please try again' . '&origin=login');
        }
        // $serviceUser = Socialite::driver($service)->stateless()->user();
        // dd($serviceUser);
        $email = $serviceUser->getEmail();
        if ($service != 'google') {
            $email = $serviceUser->getId() . '@' . $service . '.local';
        }

        $user = $this->getExistingUser($serviceUser, $email, $service);

        

        if (!$user) {
           
            $user = User::updateOrCreate(
                ['email' => $email,],
                [
                    'name' => $serviceUser->getName(),
                    'password' => '',
                    'avatar' => $serviceUser->getAvatar(),
                    'roles' => 'client',
                ]
            );

            // $this->saveAvatar($user, $serviceUser->getAvatar()); 
        }

        if ($this->needsToCreateSocial($user, $service)) {
            UserSocial::create([
                'user_id' => $user->id,
                'social_id' => $serviceUser->getId(),
                'service' => $service
            ]);
        }

        //dd($user);
        // return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth->fromUser($user) . '&origin=' . ($newUser ? 'register' : 'login'));
        return redirect(env('CLIENT_BASE_URL') . '/auth/social-callback?token=' . $this->auth::fromUser($user));
    }

    public function needsToCreateSocial(User $user, $service)
    {
        return !$user->hasSocialLinked($service);
    }

    public function getExistingUser($serviceUser, $email, $service)
    {
        if ($service == 'google') {
            return User::where('email', $email)->orWhereHas('social', function($q) use ($serviceUser, $service) {
                $q->where('social_id', $serviceUser->getId())->where('service', $service);
            })->first();
        } else {
            $userSocial = UserSocial::where('social_id', $serviceUser->getId())->first();
            return $userSocial ? $userSocial->user : null;
        }
    }

    // public function saveAvatar($user, $file)
    // {
    //     $fileContents = file_get_contents($file);
        
        
    //     $path = file_get_contents($file)->store('images', 'public');
    //     $user->avatar = $path; 
           
       
    //     if ($user->save()) {
    //         return response()->json($user,200);
    //     } else {
    //         return response()->json([
    //             'message'       => 'Error on Updated',
    //             'status_code'   => 500
    //         ],500);
    //     } 
    // }
}