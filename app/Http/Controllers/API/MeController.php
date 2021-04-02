<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class MeController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    public function index(Request $request)
    {
        return response()->json([
            'success'=>true,
            'data'=>$request->user()
        ]);
    }

    public function update(Request $request, User $user)
    {
        
        $notelp = $request->notelp;
        if ($user) {
            return response()->json([
                'success'=>true,
                'data'=>$request->all()
            ]);
        }
       
    }

    public function upload_image(Request $request, User $user){

        
        $old_path = $user->avatar;
        Storage::delete('public/'.$old_path);
        if($request->hasFile('image')) {
            $request->validate([
                'image'=>'required|image|mimes:jpeg,png,jpg'
            ]);
            $path = $request->file('image')->store('images', 'public');
            $user->avatar = $path; 
            
        }
       
        if ($user->save()) {
            return response()->json($user,200);
        } else {
            return response()->json([
                'message'       => 'Error on Updated',
                'status_code'   => 500
            ],500);
        } 
        // return response()->json($request->all(),200);

    }

    public function logout()
    {
        $this->auth::invalidate();

        return response()->json([
            'success'=>true
        ]);
    }
}
