<?php

use App\Http\Controllers\API\AppController;
use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\BidController;
use App\Http\Controllers\API\ForumController; 
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\MeController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\LelangController;
use App\Http\Controllers\API\MediaLelangController;
use App\Http\Controllers\API\NotificationController;
use App\Http\Controllers\API\VideoLelangController;
use App\Http\Controllers\API\VirtualController;
use GuzzleHttp\Middleware;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes   
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::prefix('/auth')->group(function () {
    Route::post('/register', [RegisterController::class, 'register']);
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/login/{service}', [SocialLoginController::class, 'redirect']);
    Route::get('/login/{service}/callback', [SocialLoginController::class, 'callback']);
});

Route::group(['middleware' => 'jwt.auth'], function() {
    Route::get('/me', [MeController::class, 'index']);
    Route::put('/me/update/{user}', [MeController::class, 'update']);
    Route::post('/me/upload_image', [MeController::class, 'upload_image']);
    Route::post('/me/update_bidder', [MeController::class, 'update_bidder']);
    Route::post('/prokc/sw-token', [MeController::class, 'swToken']);


    // BankController
    Route::get('/bank', [BankController::class, 'index']);


    // KategoriController 
    Route::get('/kategori', [KategoriController::class, 'index']);
    Route::get('/kategori/where', [KategoriController::class, 'search']);



    // Transactions
    Route::post('/transaction/upload_image', [TransactionController::class, 'upload_image']);
    Route::get('/transaction', [TransactionController::class, 'get_trans']);
    Route::get('/transaction/get_all_params', [TransactionController::class, 'get_all_params']);
    Route::post('/transaction/pembayaran_activasi', [TransactionController::class, 'pembayaran_activasi']);
    Route::get('/transaction/get_where', [TransactionController::class, 'get_where']);
    Route::get('/transaction/get_total', [TransactionController::class, 'get_total']);
    Route::post('/transaction/charge', [TransactionController::class, 'postCharge']);
    Route::post('/transaction/penarikan_deposit', [TransactionController::class, 'penarikan_deposit']);


    // lelang
    Route::post('/lelang/add', [LelangController::class, 'add_data']);
    Route::get('/lelang/last', [LelangController::class, 'data_last']);
    Route::get('/lelang/by/{lelang}', [LelangController::class, 'data_by']);
    Route::put('/lelang/update/{lelang}', [LelangController::class, 'update']);
    Route::get('/lelang/hapus_by/{lelang}', [LelangController::class, 'hapus_by']);
    Route::get('/lelang/all_by', [LelangController::class, 'get_by_id']);
    Route::post('/lelang/upload_image', [LelangController::class, 'upload_image']);
    Route::get('/lelang/publish/{lelang}', [LelangController::class, 'publish']);
    Route::get('/lelang/get_all_params', [LelangController::class, 'get_all_params']);
    Route::get('/lelang/data_by/{lelang}', [LelangController::class, 'data_by']);

    // media lelang
    Route::delete('/media_lelang/remove/{id}', [MediaLelangController::class, 'hapus_image']);
    Route::get('/media_lelang/update_status', [MediaLelangController::class, 'update_status']);


    // video lelang
    Route::post('/lelang/upload_video', [VideoLelangController::class, 'upload_video']);
    Route::delete('/video_lelang/remove/{id}', [videoLelangController::class, 'hapus_video']);
    Route::get('/video_lelang/update_status', [videoLelangController::class, 'update_status']);


    // notification
    Route::post('/notification/post_from_client', [NotificationController::class, 'post_from_client']);
    Route::post('/notification/mark_as_read', [NotificationController::class, 'mark_as_read']);
    Route::post('/notification/mark_as_read_all', [NotificationController::class, 'mark_as_read_all']);
    Route::get('/notification/get_notif_by_id', [NotificationController::class, 'get_notif_by_id']);
    Route::get('/notification/get_notif_by_current_id', [NotificationController::class, 'get_notif_by_current_id']);


    // bid
    Route::post('/bid/store_bid', [BidController::class, 'store_bid']);

     //Forum Chat
     Route::post('forum/add_message',[ForumController::class, 'add_message']);
     Route::get('forum/get_by_lelang',[ForumController::class, 'get_by_lelang']);



    Route::get('/auth/logout', [MeController::class, 'logout']);
});

//no auth route
Route::prefix('/notification')->group(function () {
    Route::post('/post_to_midtrans', [NotificationController::class, 'post_to_midtrans']); //ini dikirim ke midtrans
});

//no auth route
Route::prefix('/app')->group(function () {
    Route::get('/get_app', [AppController::class, 'get_app']); //ini dikirim ke midtrans
});

Route::prefix('/public')->group(function () {
    Route::get('/get_all_params', [LelangController::class, 'get_all_params']);
    Route::post('/post_virtual', [VirtualController::class, 'post_data']);
    Route::get('/get_virtual', [VirtualController::class, 'get_data']);
});



