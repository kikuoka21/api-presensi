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

        Route::prefix('siswa')->group(function () {
            Route::post('input', 'Admin\Input_data_master@input_siswa');
//        Route::post('list', 'Admin\Input_data_master@input_siswa');
//        Route::post('ubah', 'Admin\Input_data_master@input_siswa');
//        Route::post('hapus', 'Admin\Input_data_master@input_siswa');
        });

//        Route::post('staf', 'Admin\Input_data_master@input_staf');
        Route::prefix('staff')->group(function () {
            Route::post('input', 'Admin\Input_data_master@input_staf');

//        Route::post('list', 'Admin\Input_data_master@input_siswa');
//        Route::post('ubah', 'Admin\Input_data_master@input_siswa');
//        Route::post('hapus', 'Admin\Input_data_master@input_siswa');
        });

        Route::prefix('tanggal')->group(function () {
            Route::post('list', 'Admin\Input_data_master@lihat_tanggal');
            Route::post('history', 'Admin\Input_data_master@history_tanggal');
            Route::post('input', 'Admin\Input_data_master@input_tanggal');

        });

        Route::prefix('kelas')->group(function () {
            Route::post('input', 'Admin\Input_data_master@input_kelas');
            Route::post('list', 'Admin\Input_data_master@all_kelas');
            Route::post('isi', 'Admin\Input_data_master@isi_kelas');


            Route::post('tambah/list-siswa', 'Admin\Master\Kelas@list_siswa');
            Route::post('tambah/siswa', 'Admin\Master\Kelas@tambah_siswa_kelas');
//            Route::post('siswa', 'Admin\Input_data_master@isi_kelas');
//            Route::post('kelas', 'Admin\Input_data_master@isi_kelas'); // mendapat siswa yang belum mendapat kelas d tahun_ajaran tsb

            Route::prefix('hapus')->group(function () {
//                Route::post('kelas', 'Admin\Input_data_master@isi_kelas');
                Route::post('siswa', 'Admin\Master\Kelas@hapus_siswa');
//                Route::post('wali-kelas', 'Admin\Input_data_master@isi_kelas');
            });

            Route::prefix('ubah')->group(function () {
//                Route::post('nama-kelas', 'Admin\Input_data_master@isi_kelas');
//                Route::post('ketua-kelas', 'Admin\Input_data_master@isi_kelas');
//                Route::post('wali-kelas', 'Admin\Input_data_master@isi_kelas');
            });
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


