<?php

use App\Http\Controllers\API\Auth\LoginController;
use App\Http\Controllers\API\Auth\RegisterController;
use App\Http\Controllers\API\Auth\SocialLoginController;
use App\Http\Controllers\API\MeController;
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



    
    Route::get('/auth/logout', [MeController::class, 'logout']);
});

// Route::group(['prefix'=> '/auth', ['middleware' => 'throttle:20,5']], function() {
//     Route::post('register', [RegisterController::class, 'register']);
//     Route::post('login', [LoginController::class, 'login']);
// });

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});
