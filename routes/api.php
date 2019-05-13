<?php

use Illuminate\Http\Request;

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

//Route::middleware('auth:api')->get('/user', function (Request $request) {
//    return $request->user();
//});

Route::post('auth/login', 'auth@Login');
Route::post('auth/check-token', 'auth@check_token');





//admin
//Route::post('auth/check-token', 'auth@check_token');


//siswa
Route::post('siswa/dashboard', 'siswa@dashboard');

Route::post('siswa/buat-qr', 'siswa@create_qr');


