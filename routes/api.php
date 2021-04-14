<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\BankController;
use App\Http\Controllers\API\KategoriController;
use App\Http\Controllers\API\MeController;
use App\Http\Controllers\API\TransactionController;
use App\Http\Controllers\API\LelangController;
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
    Route::put('/me/upload_image/{user}', [MeController::class, 'upload_image']);


    // BankController
    Route::get('/bank', [BankController::class, 'index']);


    // KategoriController
    Route::get('/kategori', [KategoriController::class, 'index']);



    // Transactions
    Route::post('/transaction/upload_image', [TransactionController::class, 'upload_image']);
    Route::get('/transaction', [TransactionController::class, 'get_trans']);
    Route::post('/transaction/konfirmasi', [TransactionController::class, 'konfirmasi']);


    // lelang
    Route::post('/lelang/add', [LelangController::class, 'add_data']);
    Route::get('/lelang/last', [LelangController::class, 'data_last']);
    Route::get('/lelang/by/{lelang}', [LelangController::class, 'data_by']);
    Route::put('/lelang/update/{lelang}', [LelangController::class, 'update']);
    Route::get('/lelang/hapus_by/{lelang}', [LelangController::class, 'hapus_by']);
    Route::get('/lelang/all_by', [LelangController::class, 'get_by_id']);








    Route::get('/auth/logout', [MeController::class, 'logout']);
});

// Route::group(['prefix'=> '/auth', ['middleware' => 'throttle:20,5']], function() {
//     Route::post('register', [RegisterController::class, 'register']);
//     Route::post('login', [LoginController::class, 'login']);
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
