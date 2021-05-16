<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use App\Mail\EmailRegistrasi;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Tymon\JWTAuth\Facades\JWTAuth; 
use Tymon\JWTAuth\Exceptions\JWTException;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '';
    protected $auth;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct(JWTAuth $auth)
    {
        $this->auth = $auth;
    }

    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse|\Illuminate\Http\JsonResponse
     */
    public function register(Request $request)
    {   
        // $request->validate([
        //     'invoice' => 'required',
        //     'bank' => 'required',
        // ]);
        $this->validator($request);
        // if (!$validator->fails()) {
        $user = $this->create($request->all());

        return response()->json([
            'success'=>true,
            'data'=>$user,
            // 'token'=>$token
        ], 200);
        exit;
        

        

    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator($request)
    {
        // return Validator::make($data, [
        //     'name' => ['required', 'string', 'max:255'],
        //     'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
        //     'password' => ['required', 'string', 'min:8'],
        //     // 'password' => ['required', 'string', 'min:8', 'confirmed'],
        // ]);
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string'],
        ]);
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {   
        $roles = '';
        if (!isset($data['roles'])) {
            $roles = 'client';
        } else {
            $roles = $data['roles'];
        }
        
        return User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'roles' => $roles,
            'status'=> 0
        ]);
    }

    public function send_email()
    {
        $email = request()->email;
        $otp = rand(10000, 90000);
        $details = [
            'title'=> 'Registrasi Success',
            'otp'=> $otp,
        ];

        Mail::to($email)->send(new EmailRegistrasi($details));
        return response()->json(['message'=> 'success', 'otp'=>$otp]);
    }

    public function update_status()
    {
        $email = request()->email;
        $upd = User::where('email', $email)->update(['status'=>1]);
        if (!$upd) {
            return response()->json(['message'=> 'failsed'], 500);
        }
        return response()->json(['message'=> 'success', 'otp'=>$upd], 200);
    }
}
