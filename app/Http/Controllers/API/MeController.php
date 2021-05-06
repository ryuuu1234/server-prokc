<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
        if ($user) {
            $user->notelp = $request->notelp;
            $user->nowhatsapp = $request->nowhatsapp;
            $user->alamat = $request->alamat;
            $user->provinsi = $request->provinsi;
            $user->kota = $request->kota;
            if ($user->save()) {
                return response()->json($user,200);
            } else {
                return response()->json([
                    'status'       => 'Error on Updated',
                    'status_code'   => 500
                ],500);
            } 
        } else {
            return response()->json([
                'status'       => 'User tidak ditemukan',
                'status_code'   => 500
            ],500);
        }
       
    }

    public function update_bidder()
    { 
        $user = $this->auth::user();
        $update = User::find($user->id)->update(['bidder'=> 1]);
        if (!$update) {
            return response()->json(['code'=> 0, 'message'=> 'update Failed']); exit;
        }

        return response()->json(['code'=> 1, 'message'=> 'success'], 200);
       
    }

    public function upload_image(Request $request){

        $user = $this->auth::user();
        $old_path = $user->avatar;
        Storage::delete('public/'.$old_path);
        if($request->hasFile('image')) {
            $request->validate([
                'image'=>'required|image|mimes:jpeg,png,jpg'
            ]);
            $path = $request->file('image')->store('images', 'public');
            
            $save = User::find($user->id)->update([
                'avatar'=>$path,
            ]);
            
        
       
            if ($save) {
                return response()->json($user,200);
            } else {
                return response()->json([
                    'message'       => 'Error on upload',
                ],500);
            } 

            exit;
        }

        return response()->json([
            'message'       => 'Error on Updated',
        ],500);

        // return response()->json([
        //     'old_path' => $old_path,
        //     'user' => $user,
        //     'file' => $request->file('image')
        // ],200);

        

    }

    public function swToken(Request $request){
        
        $user = User::find($request->id);
        if($user->fcm_token != $request->token){
            DB::beginTransaction();
            try{
                $save=User::where('id',$request->id)->update(['fcm_token'=>$request->token]);

                DB::commit();
                return response()->json(['status'=>'sukses'], 200);
            }catch (\Exception $e){
            DB::rollback();
                return response()->json([
                    'status'=>'failse',
                    'message'=> $e->getMessage()
                ],400);
        }
        }else{
            return response()->json([
                
                'message'=>'no data need to update',
            ],200);

        }
    }

    public function logout()
    {
        $this->auth::invalidate();

        return response()->json([
            'success'=>true
        ]);
    }
}
