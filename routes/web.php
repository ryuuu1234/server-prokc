<?php

use App\Models\Lelang;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

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

Route::get('/coba', function () {
    // $coba = Str::random(10);
    // $coba = Str::orderedUuid(10);
    // $coba = date('mdy');
    // echo $coba;
    // $coba = Lelang::select('user_id')->distinct()->get();
    //                 dd($coba);

    // $date = date('Y-m-d');
    // echo customTanggal($date,'d-m-Y');

    // $hasil = [];
    // $token = User::find(1)->fcm_token;
    // array_push($hasil, $token);
    $topik= [
        'type'=>'transaction',
        'id'=>1,
    ];

    // $topik = '{"type":"transaction","id":1}';
    dd(json_encode($topik));
});

// Route::get('/linkstorage', function () {
//     Artisan::call('storage:link'); // this will do the command line job
// });

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');
