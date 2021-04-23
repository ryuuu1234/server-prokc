<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Payment;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\CoreApi;
use Midtrans\Snap;

class TransactionController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }

    public function get_all_params()
    {   
        $user = $this->auth::user();

        $data = Transaction::where('user_id', $user->id)->orderBy(request()->sortby, request()->sort)
            ->when(request()->jenis, function($items) {
                $items = $items->where('jenis', 'LIKE', '%' . request()->jenis . '%');
        })->paginate(request()->per_page);
       
        
        if ($data) {
            return response()->json(['success'=> 'true', 'data'=> $data],200);
        } else {
            return response()->json(['success'=> 'failed'],500);
        }
    }
    
    public function upload_image(Request $request){

        $user = $this->auth::user();
        
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

        $get = Transaction::where(['user_id'=>$user, 'jenis'=>$jenis])->firstOrFail();
        if ($get) {
            return response()->json($get,200);
            } else {
                return response()->json([
                    'message'       => 'Error',
                    'status_code'   => 500
                ],500);
            } 
    }

    public function get_where()
    {
        $invoice = request()->invoice;
        $data = Transaction::where('invoice', $invoice)->first();

        // if(!$data)
        //     return response()->json(['message'=>'failed']);
        return response()->json(['message'=>'success', 'data'=>$data]); 
    }

    public function pembayaran_activasi(Request $request)
    {   
        $request->validate([
            'tanggal' => 'required',
            'nominal' => 'required|numeric'
        ]);

        
        $user = $this->auth::user();
        $get_bank = Bank::find($request->bank_id);
        $charge = $this->chargeMidtrans($user, $get_bank, $request);

        // $status_code = $charge->status_code;
        // switch($status_code){
        //     case '200';
        //         $status = 3; //'SUCCESS'
        //         break;
        //     case '201';
        //         $status = 1; //PENDING
        //         break;
        //     case '202';
        //         $status = 2; //CANCEL
        //         break;
        // }

        $charge_status = $charge->transaction_status;

        $transaction = new Transaction();
        $transaction->invoice = $charge->order_id;
        $transaction->payment_token = $charge->transaction_id;
        $transaction->bank = strtolower($get_bank->name);
        $transaction->nominal = $charge->gross_amount;
        $transaction->tanggal = $charge->transaction_time;
        $transaction->status = $charge_status;
        $transaction->user_id = $user->id;
        $transaction->jenis = $request->jenis;
        $save = $transaction->save();
        if (!$save) {
            return response()->json(['code'=> 0, 'message'=> 'Transaction Failed']); exit;
        }

        return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=> $charge ], 200);
        exit; 
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

    public function chargeMidtrans($user, $get_bank, $req)
    {   

        Config::$serverKey = "SB-Mid-server-Co2LbAUKr740vzuhtgV7t6-R";

        
        $customerDetails= [
            'first_name' => $user->name,
            'email' => $user->email,
            'phone' => $user->notelp,
        ];
        $params = [
            //    'enable_payment' => Payment::PAYMENT_CHANNELS,
            'payment_type' => 'bank_transfer',
            'transaction_details'=> [
                'order_id'    => $this->invoice($req->jenis),
                'gross_amount'  => $req->nominal,
            ],
            'customer_details' => $customerDetails,
            'expiry' => [
                'start_time' => date('Y-m-d H:i:s T'),
                'unit' => Payment::EXPIRY_UNIT,
                'duration' => Payment::EXPIRY_DURATION,
            ],
            'bank_transfer' => [
                'bank' => strtolower($get_bank->name),
                'va_number' => $get_bank->acc,
            ]
        ];

        $charge = CoreApi::charge($params);
        if (!$charge) {
            return response()->json(['code'=> 0, 'message'=> 'Charge Failed']); exit;
        } 

        return $charge;
        
    }

    
}
