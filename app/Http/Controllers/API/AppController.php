<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\App;
use Illuminate\Http\Request;

class AppController extends Controller
{
    public function get_app()
    {   
        
       $get = App::orderBy('id', 'ASC')->first();
       return response()->json(['message'=>'success', 'data'=>$get], 200);
    }

    
}
