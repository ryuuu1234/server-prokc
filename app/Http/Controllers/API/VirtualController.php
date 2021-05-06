<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Virtual;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class VirtualController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    

    public function post_data(Request $request)
    {
        $request->validate([
            'nama' => 'required',
            'desc' => 'required',
            'cara' => 'required',
        ]);

        $data = new Virtual();
        $data->nama = $request->nama;
        $data->desc = $request->desc;
        $data->cara = $request->cara;
        $save = $data->save();

        if (!$save) {
            return response()->json(['status'=>'failed'], 500);
        }
        return response()->json(['status'=>'success', 'result'=> $data], 200);

    }

    public function get_data()
    {
        $nama = request()->nama;

        $data = Virtual::where('nama', $nama)->first();

        if (!$data) {
            return response()->json(['status'=>'failed'], 500);
        }
        return response()->json(['status'=>'success', 'result'=> $data], 200);
    }

    
}
