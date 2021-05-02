<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Kategori;
use App\Models\Lelang;
use App\Models\User;
use Illuminate\Http\Request;
// use Illuminate\Support\Facades\Storage;
use Tymon\JWTAuth\Facades\JWTAuth;

class KategoriController extends Controller
{
    protected $auth;

    public function __construct(JWTAuth $auth)
    {
       $this->auth = $auth;
    }
    public function index(Kategori $categori)
    {   
        $sekarang = date('Y-m-d H:i:s');

        $sumJumlah = Lelang::selectRaw('sum(status)')
        ->whereColumn('kategori', 'lelangs.kategori')
        ->whereRaw("(berakhir > ?)",[$sekarang])
        ->getQuery();

        // $data = Beban::select('t_beban_biaya.*')
        //         ->with(['pengeluaran' => function($q) use($tgl_awal, $tgl_akhir) {
        //        $q->whereRaw("(created_at >= ? AND created_at <= ?)",[$tgl_awal." 00:00:00", $tgl_akhir." 23:59:59"]);
        //  }])->selectSub($sumJumlah, 'subtotal')->get();
        
        return response()->json([
            'success'=>true,
            'data'=>$categori->all(),
            'jumlah'=>$sumJumlah
        ]);
    }

    
}
