<?php

namespace App\Http\Controllers\API\Deposit;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\DB;



class DepositTotal extends Controller
{
    public static function jumlahDepositMasuk($user)
    {   
        $deposit=0;
        try {
            // $jumlah = 0;
            $deposit = DB::table('transactions')
                ->where('jenis', '=', 'pembayaran_deposit')
                ->where('user_id', '=', $user->id)
                ->where('status', '=', 'settlement')
                ->sum('transactions.nominal');
    
            // $penarikanDeposit = DB::table('transactions')
            //     ->where('jenis', '=', 'penarikan_deposit')
            //     ->where('user_id', '=', $user->id)
            //     ->where('status', '=', 'settlement')
            //     ->sum('transactions.nominal');
            return $deposit;
        } catch (\Exception $e) {
            return $e;
        }
        
    }

    public static function jumlahDepositKeluar($user)
    {   
        $deposit=0;
        try {
            // $jumlah = 0;
            // $tambahDeposit = DB::table('transactions')
            //     ->where('jenis', '=', 'pembayaran_deposit')
            //     ->where('user_id', '=', $user->id)
            //     ->where('status', '=', 'settlement')
            //     ->sum('transactions.nominal');
            $status = ['failure', 'back'];
            $deposit = DB::table('transactions')
                ->where('jenis', '=', 'penarikan_deposit')
                ->where('user_id', '=', $user->id)
                ->whereNotIn('status', $status)
                ->sum('transactions.nominal');
            return $deposit;
        } catch (\Exception $e) {
            return $e;
        }
        
    }

    public static function totalDeposit($user)
    {   
        $jumlah=0;
        $masuk = self::jumlahDepositMasuk($user);
        $keluar = self::jumlahDepositKeluar($user);
        $jumlah = $masuk-$keluar;
        return $jumlah;
    }
}
