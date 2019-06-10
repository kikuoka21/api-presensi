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

        Route::prefix('lihat')->group(function () {
//            Route::post('siswa', 'Admin\Input_data_master@input_siswa');
//            Route::post('staf', 'Admin\Input_data_master@input_staf');
            Route::post('tanggal', 'Admin\Input_data_master@lihat_tanggal');
            Route::post('history/tanggal', 'Admin\Input_data_master@history_tanggal');
            Route::post('kelas/semua', 'Admin\Input_data_master@all_kelas');
            Route::post('kelas', 'Admin\Input_data_master@input_kelas');


        });

    });
});


//siswa

Route::prefix('siswa')->group(function () {

    Route::prefix('presensi')->group(function () {
        Route::post('buat', 'Siswa\siswa@create_qr');
        Route::post('isi', 'Siswa\siswa@isi_absen');

        Route::prefix('lihat')->group(function () {
            Route::post('harian', 'Siswa\presensi@get_presensi_harian');
            Route::post('perbulan', 'Siswa\presensi@get_presensi_perbulan');
        });

    });

    Route::post('dashboard', 'Siswa\siswa@dashboard');
    Route::post('profil', 'Siswa\siswa@profil');

});


