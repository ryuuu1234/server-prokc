<?php

use App\Http\Controllers\SendMail;
use App\Models\Kategori;
use App\Models\Lelang;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/send-email', [SendMail::class, 'index']);

Route::get('/coba', function () {
    // $coba = Str::random(10);
    // $coba = Str::orderedUuid(10);
    // $coba = date('mdy');
    // echo $coba;
    // $coba = Lelang::select('user_id')->distinct()->get();
    //                 dd($coba);

    // $date = date('Y-m-d H:i:s');
    // echo $date;

    // $hasil = [];
    // $token = User::find(1)->fcm_token;
    // array_push($hasil, $token);
    // $topik= [
    //     'type'=>'transaction',
    //     'id'=>1,
    // ];

    // $to = [1,2];
    // $user = Kategori::whereIn('id', $to)->get();   
    // // $topik = '{"type":"transaction","id":1}';
    // dd($user);

    // $sekarang = date('Y-m-d H:i:s');

    //     // $sumJumlah = Lelang::selectRaw('sum(status)')
    //     //         ->whereColumn('kategori', 'lelangs.kategori')
    //     //         ->whereRaw('(berakhir > ?)',[$sekarang])
    //     //         ->getQuery();

    //     $data = Kategori::select('*')
    //             ->with(['lelangs' => function($q) use($sekarang) {
    //            $q->whereRaw('(berakhir > ?)',$sekarang);
    //      }])->get();

    // $search = "Bekko";
    // $lelang = Lelang::where('status', '>=', 1)->orderBy('updated_at', 'DESC')
    //         ->when($search, function($items) {
    //             $search = "Bekko";
    //             $items = $items->where('kategori', 'LIKE', '%' . $search . '%');
    //     })->paginate(request()->per_page);
    //     $lelang->load('media_lelang:id,lelang_id,image,status');
    //     $lelang->load('video_lelang:id,lelang_id,video,status');
    //     $lelang->load('user');
    //     $lelang->load('bid');

    // dd($lelang);
    $otp = rand(00000,55555);
    echo $otp;
});

// Route::get('/linkstorage', function () {
//     Artisan::call('storage:link'); // this will do the command line job
// });

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
