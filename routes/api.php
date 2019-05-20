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

//Route::post('auth/login', 'auth@Login');
//Route::post('auth/check-token', 'auth@check_token');

Route::prefix('auth')->group(function () {
	Route::post('login', 'auth@Login');
	Route::post('check-token', 'auth@check_token');
});



//admin
Route::prefix('admin')->group(function () {
	Route::post('dashboard', 'Admin\admin@dashboard');

	Route::prefix('master')->group(function () {
		Route::post('siswa', 'Admin\Input_data_master@input_siswa');


	});
});



//siswa

Route::prefix('siswa')->group(function () {
//	Route::post('dashboard', 'Admin\admin@dashboard');

	Route::post('dashboard', 'Siswa\siswa@dashboard');

	Route::post('buat-qr', 'Siswa\siswa@create_qr');
});


