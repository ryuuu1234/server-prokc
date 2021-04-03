<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class BankController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    public function index(Bank $bank)
    {   
        
        return response()->json([
            'success'=>true,
            'data'=>$bank->all()
        ]);
    }

    
}
