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

        // $sumJumlah = Lelang::selectRaw('sum(status)')
        //         ->whereColumn('kategori', 'lelangs.kategori')
        //         ->whereRaw('(berakhir > ?)',[$sekarang])
        //         ->getQuery();

        $data = Kategori::select('*')
                ->with(['lelangs' => function($q) use($sekarang) {
               $q->whereRaw('(berakhir > ? AND status >= ?)',[$sekarang, 1]);
         }])->get();
        
        return response()->json([
            'success'=>true,
            'data'=>$categori->all(),
            'jumlah'=>$data
        ]);
    }

    public function search(Kategori $categori)
    {   
        $sekarang = date('Y-m-d H:i:s');
        $data = Kategori::orderBy('name', 'ASC')
                ->when(request()->q, function($items) {
                    $items = $items->where('name', 'LIKE', '%' . request()->q . '%');
                })
                ->with(['lelangs' => function($q) use($sekarang) {
               $q->whereRaw('(berakhir > ? AND status >= ?)',[$sekarang,1]);
         }])->get();
        
        return response()->json([
            'success'=>true,
            'data'=>$categori->all(),
            'jumlah'=>$data
        ]);
    }

    
}
