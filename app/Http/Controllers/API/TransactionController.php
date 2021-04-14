<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;

class TransactionController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    
    public function upload_image(Request $request){

        $user = $this->auth::user();
        // Transaction::firstOrCreate([
        //     'user_id' => $user->id
        // ]);
        
        $transaction = Transaction::where(['user_id'=>$user->id, 'jenis'=>'pembayaran_activasi'])->first();

        if ($transaction) {
            $old_path = $transaction->image;
            Storage::delete('public/'.$old_path);
        }
        
        if($request->hasFile('image')) {
            $request->validate([
                'image'=>'required|image|mimes:jpeg,png,jpg'
            ]);
            $path = $request->file('image')->store('images', 'public');
            // firstOrCreate
            Transaction::firstOrCreate([
                'user_id' => $user->id,
                'jenis' => 'pembayaran_activasi'
            ]);
            $transaction->image = $path;

            if ($transaction->save()) {
                return response()->json($transaction,200);
            } else {
                return response()->json([
                    'message'       => 'Error on Updated',
                    'status_code'   => 500
                ],500);
            } 
            
        }

    }

    public function get_trans()
    {
        $user = $this->auth::user()->id;
        $jenis = request()->jenis;

        $get = Transaction::where(['user_id'=>$user, 'jenis'=>$jenis])->first();
        if ($get) {
            return response()->json($get,200);
            } else {
                return response()->json([
                    'message'       => 'Error',
                    'status_code'   => 500
                ],500);
            } 
    }

    public function konfirmasi(Request $request)
    {   
        $request->validate([
            'tanggal' => 'required',
            'nominal' => 'required|numeric'
        ]);
        $user = $this->auth::user()->id;
        $transaction = Transaction::updateOrCreate(['user_id'=> $user, 'jenis' => $request->jenis],
            [   
                'invoice'=> $this->invoice($request->jenis),
                'bank_id'=>$request->bank_id,
                'tanggal'=>$request->tanggal,
                'nominal'=>$request->nominal,
                'status'=>1, //menunggu validasi admin
            ]
        );

        if ($transaction) {
            return response()->json($transaction,200);
        } else {
            return response()->json([
                'message'       => 'Error',
                'status_code'   => 500
            ],500);
        }

    }

    public function invoice($jenis)
    {   
        $inv = '';
        if ($jenis == 'pembayaran_activasi') {
            $inv = 'ACT-';
        }

        $random = Str::random(10);

        $invoice = $inv.$random;

        return $invoice;

    }

    
}
