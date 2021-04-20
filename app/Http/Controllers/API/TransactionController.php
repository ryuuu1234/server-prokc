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
                // 'status'=>1, //menunggu validasi admin
            ]
        );

        try {
            Config::$serverKey = "SB-Mid-server-Co2LbAUKr740vzuhtgV7t6-R";

            $customerDetails= [
                'first_name' => $this->auth::user()->name,
                'email' => $this->auth::user()->email,
                'phone' => $this->auth::user()->notelp,
            ];

            $params = [
                //    'enable_payment' => Payment::PAYMENT_CHANNELS,
                'payment_type' => 'bank_transfer',
                'transaction_details'=> [
                        'order_id'    => $transaction->invoice,
                        'gross_amount'  => $transaction->nominal,
                ],
                'customer_details' => $customerDetails,
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s T'),
                    'unit' => Payment::EXPIRY_UNIT,
                    'duration' => Payment::EXPIRY_DURATION,
                ],
                'bank_transfer' => [
                    'bank' => 'bca',
                    'va_number' => '111111',
                ]

            ];
                $charge = CoreApi::charge($params);
                if (!$charge) {
                    return response()->json(['code'=> 0, 'message'=> 'Failed']);
                }
                return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=>$charge], 200);
        }
        catch ( \Exception $e) {
            dd($e);
            return response()->json(['code'=> 0, 'message'=> 'Success', 'result'=>$e]);
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

    private function _generatePaymentToken($trans)
    {
    //    $this->initPaymentGateway();
        try {
            Config::$serverKey = "SB-Mid-server-Co2LbAUKr740vzuhtgV7t6-R";

            $customerDetails= [
                'first_name' => $this->auth::user()->name,
                'email' => $this->auth::user()->email,
                'phone' => $this->auth::user()->notelp,
            ];

            $params = [
                //    'enable_payment' => Payment::PAYMENT_CHANNELS,
                'payment_type' => 'transfer_bank',
                'transaction_details'=> [
                        'order_id'    => $trans->invoice,
                        'gross_amount'  => $trans->nominal,
                ],
                'customer_details' => $customerDetails,
                'expiry' => [
                    'start_time' => date('Y-m-d H:i:s T'),
                    'unit' => Payment::EXPIRY_UNIT,
                    'duration' => Payment::EXPIRY_DURATION,
                ],
                'bank_transfer' => [
                    'bank' => 'bca',
                    'va_number' => '111111',
                ]

            ];

            
                // Get Snap Payment Page URL
                // $snap = Snap::createTransaction($params);
                $charge = CoreApi::charge($params);
                // dd($trans);
                // Redirect to Snap Payment Page
                // header('Location: ' . $paymentUrl);
                // if ($snap->token) {
                //     $trans->payment_token = $snap->token;
                //     $trans->payment_url = $snap->redirect_url;

                //     if($trans->save()) {
                //         return response()->json([
                //             'message'       => 'Success',
                //         ], 200);
                //     }
                // }
                dd($charge); exit;
                if (!$charge) {
                    return response()->json(['code'=> 1, 'message'=> 'Success', 'result'=>$charge]);
                }
                return response()->json(['code'=> 0, 'message'=> 'Failed']);
        }
        catch ( \Exception $e) {
            // return response()->json([
            //     'message'       => 'Error',
            //     'data' => $e
            // ], 500);
            return response()->json(['code'=> 0, 'message'=> 'Success', 'result'=>$e]);
        }
       
    }

    
}
