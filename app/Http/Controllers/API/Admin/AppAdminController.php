<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\App;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;



class AppAdminController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

   public function get_data()
   {
       $data = App::find(1);
       return response()->json(['message'=>'success', 'result'=>$data]);
   }

   public function update_data(Request $request)
   {
        $request->validate([
            'name'=>'required',
            'alamat'=>'required',
        ]);

        $app = App::find(1);

        $update = $app->update([
            'name'=>$request->name,
            'alamat'=>$request->alamat,
            'no_cs'=>$request->no_cs,
            'wa_cs'=>$request->wa_cs,
        ]);

        if (!$update) {
            return response()->json(['message'=>'failed'], 500);
        }
        return response()->json(['messaage'=>'success', 'result'=>$app]);
   }

   public function upload_image(Request $request)
   {
        $app = App::find(1);

        $old_path = $app->icon;
        Storage::delete('public/'.$old_path);
        if($request->hasFile('image')) {
            $request->validate([
                'image'=>'required|image|mimes:jpeg,png,jpg'
            ]);
            $path = $request->file('image')->store('images', 'public');
            
            $save = $app->update([
                'icon'=>$path,
            ]);
            
        
       
            if ($save) {
                return response()->json(['message'=>'success', 'result'=>$app],200);exit;
            } else {
                return response()->json([
                    'message'       => 'Error on upload',
                ],500);exit;
            } 
            
        }
        return response()->json([
            'message'       => 'Error on Updated',
        ],500);
   }



}
