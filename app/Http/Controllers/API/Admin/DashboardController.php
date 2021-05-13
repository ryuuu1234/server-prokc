<?php

namespace App\Http\Controllers\API\Admin;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use App\Models\Lelang;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Facades\JWTAuth;



class DashboardController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function data_widget()
    {
        return response()->json([
            'jml_user' =>self::user_count_all(),
            'jml_bidder' =>self::user_count_bidder(),
            'jml_lelang' =>self::lelang_count_all(),
            'jml_transaksi' =>self::transaction_count_all(),
        ]);
    }


    public static function user_count_all()
    {   
        $count=0;
        $count = User::count();
        return $count;
    }

    public static function user_count_bidder()
    {   
        $count=0;
        $count = User::where('bidder', 1)->count();
        return $count;
    }

    public static function lelang_count_all()
    {   
        $count=0;
        $count = Lelang::where('status','>=', 1)->count();
        return $count;
    }

    public static function transaction_count_all()
    {   
        $count=0;
        $count = Transaction::count();
        return $count;
    }
}
