<?php

use Illuminate\Http\Request;




Route::prefix('auth')->group(function () {
	Route::post('login', 'auth@Login');
	Route::post('check-token', 'auth@check_token');
	Route::post('ganti-pswd', 'auth@ubah_pass');

});



//admin
Route::prefix('admin')->group(function () {
	Route::post('dashboard', 'Admin\admin@dashboard');

//Select * from siswa where siswa.nis not in ( select nis from isikelas)

	Route::prefix('master')->group(function () {
		Route::post('siswa', 'Admin\Input_data_master@input_siswa');
		Route::post('staf', 'Admin\Input_data_master@input_staf');
		Route::post('tanggal', 'Admin\Input_data_master@input_tanggal');
		Route::post('kelas', 'Admin\Input_data_master@input_kelas');

	});
});



//siswa

Route::prefix('siswa')->group(function () {
//	Route::post('dashboard', 'Admin\admin@dashboard');

	Route::post('dashboard', 'Siswa\siswa@dashboard');

	Route::prefix('presensi')->group(function (){
        Route::post('buat', 'Siswa\siswa@create_qr');
        Route::post('isi', 'Siswa\siswa@isi_absen');



    });

    Route::post('profil', 'Siswa\siswa@profil');

});


