<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\Deposit\DepositTotal;
use App\Http\Controllers\API\Fcm\BroadcastMessage;
use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Payment;
use App\Models\Penarikan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

    public function get_total()
    {   
        $user = $this->auth::user();

        // try {
        //     $jumlah = 0;
        //     $tambahDeposit = DB::table('transactions')
        //         ->where('jenis', '=', 'pembayaran_deposit')
        //         ->where('user_id', '=', $user->id)
        //         ->where('status', '=', 'settlement')
        //         ->sum('transactions.nominal');
    
        //     $penarikanDeposit = DB::table('transactions')
        //         ->where('jenis', '=', 'penarikan_deposit')
        //         ->where('user_id', '=', $user->id)
        //         ->where('status', '=', 'settlement')
        //         ->sum('transactions.nominal');
    
        //     $jumlah = $tambahDeposit - $penarikanDeposit;
        //     return response()->json(['success'=> 'true', 'data'=> $jumlah],200);
        // } catch (\Exception $e) {
        //     return response()->json(['success'=> 'failed', 'error'=> $e],500);
        // }      
        $total = DepositTotal::totalDeposit($user);
        return response()->json(['success'=> 'true', 'data'=> $total],200);
       
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

    public function get_where()
    {
        $invoice = request()->invoice;
        $user = $this->auth::user();


        $data = Transaction::where(['invoice'=>$invoice, 'user_id'=>$user->id])
                ->orderBy('id', 'DESC')->first();
        return response()->json(['message'=>'success', 'data'=>$data], 200); 
    }

    public function pembayaran_activasi(Request $request)
    {   
        $request->validate([
            'tanggal' => 'required',
            'nominal' => 'required|numeric',
            'jenis' => 'required',
        ]);

        
        $user = $this->auth::user();
        $invoice = $this->invoice($request->jenis);
        // $get_bank = Bank::find($request->bank_id);
        // $charge = $this->chargeMidtrans($user, $get_bank, $request);

        // $charge_status = $charge->transaction_status;

        $transaction = new Transaction();
        $transaction->invoice = $invoice;
        $transaction->nominal = $request->nominal;
        $transaction->status = '1';
        $transaction->user_id = $user->id;
        $transaction->jenis = $request->jenis;
        $save = $transaction->save();
        if (!$save) {
            return response()->json(['code'=> 0, 'message'=> 'Transaction Failed']); exit;
        }
        return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=> $transaction ], 200);
        exit; 
    }

    public function penarikan_deposit(Request $request)
    {
        $request->validate([
            'nominal' => 'required|numeric',
            'biaya' => 'required|numeric',
        ]);

        $invoice = $this->invoice('penarikan_deposit');
        $user = $this->auth::user();

        $transaction = new Transaction();
        $transaction->invoice = $invoice;
        $transaction->nominal = $request->nominal;
        $transaction->status = 'pending'; //sedang proses
        $transaction->user_id = $user->id;
        $transaction->jenis = 'penarikan_deposit';
        
        if ($transaction->save()) {
            $penarikan = new Penarikan();
            $penarikan->transaction_id = $transaction->id;
            $penarikan->user_id = $user->id;
            $penarikan->rekening = $request->rekening;
            $penarikan->atas_nama = $request->atas_nama;
            $penarikan->nama_bank = $request->nama_bank;
            $penarikan->nominal = $request->nominal;
            $penarikan->biaya = $request->biaya;

            $save = $penarikan->save();

            if (!$save) {
                return response()->json(['code'=> 0, 'message'=> 'Penarikan Failed']); exit;
            }

            $token = [];
            $get_token = User::find($transaction->user_id)->fcm_token;
            array_push($token, $get_token);

            BroadcastMessage::sendMessage('Admin', 'Transaksi '.$invoice, 'detail.transaksi/'.$transaction->id, $token);
            return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=> $transaction ], 200);
            exit; 
        }

        return response()->json(['code'=> 0, 'message'=> 'Transaction Failed']); exit;

    }

    public function postCharge(Request $request)
    {
        $request->validate([
            'invoice' => 'required',
            'bank' => 'required',
        ]);

       $invoice = $request->invoice;
       $bank = $request->bank;
       $user = $this->auth::user();
       $get_trans = Transaction::where('invoice', $invoice)->where('user_id', $user->id)->first();


        // ini transfer bank
       $charge = $this->chargeMidtrans($user, $bank, $get_trans);
        
       $va_number_client = null;
       if ($bank == 'permata') {
           $va_number_client = $charge->permata_va_number;
       } else {
            $va_number_client = $charge->va_numbers[0]->va_number;
       }

       $update = Transaction::where(['user_id'=>$user->id, 'invoice' => $invoice])->update([
                    'payment_token'=>$charge->transaction_id,
                    'nominal'=>$charge->gross_amount,
                    'tanggal'=>$charge->transaction_time,
                    'status'=>$charge->transaction_status,
                    'va_number_client'=>$va_number_client,
                    'bank'=>$request->bank,
                ]);
                    
        
        if (!$update) {
            return response()->json(['code'=> 0, 'message'=> 'Transaction Failed']); exit;
        }
        return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=> $get_trans ], 200);
        exit; 

    }

    

    public function invoice($jenis)
    {   
        $inv = '';
        if ($jenis == 'pembayaran_activasi') {
            $inv = 'ACT-';
        } elseif($jenis == 'pembayaran_deposit' || $jenis == 'penarikan_deposit') {
            $inv = 'DEP';
        } else {
            $inv = 'LEL';
        }

        $random = Str::random(10);
        $invoice = $inv.$random;
        return $invoice;

    }


    // ini kemidtrans

    public function chargeMidtrans($user, $bank, $req)
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
                'order_id'    => $req->invoice,
                'gross_amount'  => $req->nominal,
            ],
            'customer_details' => $customerDetails,
            // 'expiry' => [
            //     'start_time' => date('Y-m-d H:i:s T'),
            //     'unit' => Payment::EXPIRY_UNIT,
            //     'duration' => Payment::EXPIRY_DURATION,
            // ],
            'bank_transfer' => [
                'bank' => $bank,
                'va_number' => '111111' //dari sononya
                // 'va_number' => $get_bank->acc,
            ]
        ];

        $charge = CoreApi::charge($params);
        if (!$charge) {
            return response()->json(['code'=> 0, 'message'=> 'Charge Failed']); exit;
        } 

        return $charge;
        
    }

    
}
