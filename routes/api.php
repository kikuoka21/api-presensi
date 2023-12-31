<?php

use Illuminate\Http\Request;


Route::prefix('auth')->group(function () {
	Route::post('login', 'auth@Login');
	Route::post('check-token', 'auth@check_token');
	Route::post('ganti-pswd', 'auth@ubah_pass');
	Route::get('logout', 'auth@logout');
	Route::post('logout', 'auth@logout');

});

Route::get('zzzz', 'auth@ccc');
Route::get('zz', 'auth@ccc');
Route::post('zzzz', 'auth@ccc');
Route::get('tanggal', 'Admin\Input_data_master@lihat_tanggal_alluser');

//admin
Route::prefix('admin')->group(function () {

	Route::post('dashboard', 'Admin\admin@dashboard');
	Route::post('dashboard/tgl', 'Admin\admin@dashboard_tgl');
	Route::post('listkls', 'Admin\admin@list_kelasthn_ini');
	Route::post('generate_qr', 'Admin\admin@gen_qr');


	Route::post('lihat', 'Admin\admin@list_kelas');//dikirim tgl skrng

	Route::post('laporan-bulan', 'Admin\admin@laporan2');//laporan perbulan
	Route::post('laporan-smes', 'Admin\admin@laporan_smes');//laporan perbulan

	Route::prefix('absen-ubah')->group(function () {
		Route::post('getdata', 'Admin\admin@get_absen_kelas');//bisa untuk laporan perhari
		Route::put('per-siswa', 'Admin\admin@ubah_persiswa');//
		Route::put('per-kelas', 'Admin\admin@ubah_perkelas');//semua kelas

	});


	Route::prefix('master')->group(function () {
		Route::prefix('siswa')->group(function () {
			Route::post('input', 'Admin\Input_data_master@input_siswa');
			Route::post('cari', 'Admin\Master\User_Master@cari_siswa');
			Route::put('ubah', 'Admin\Master\User_Master@ubah_siswa');//tambahan PUT
			Route::post('hapus', 'Admin\Master\User_Master@hapus_siswa');
			Route::post('datasiswa', 'Admin\Master\User_Master@data_siswa');// get data untuk d ubah
		});

		Route::prefix('staf')->group(function () {
			Route::post('input', 'Admin\Input_data_master@input_staf');
			Route::post('list', 'Admin\Master\User_Master@cari_staf');
			Route::post('data-staf', 'Admin\Master\User_Master@data_staf');
			Route::put('ubah', 'Admin\Master\User_Master@update_staf');
			Route::post('hapus', 'Admin\Master\User_Master@hapus_staf');
		});

		Route::prefix('tanggal')->group(function () {
			Route::post('list', 'Admin\Input_data_master@lihat_tanggal');//melihat tanggal yg belum terjadi kedepan
//            Route::post('history', 'Admin\Input_data_master@history_tanggal');//
			Route::post('input', 'Admin\Input_data_master@input_tanggal');
//            Route::post('ubah', 'Admin\Input_data_master@ubah_tanggal');
			Route::post('hapus', 'Admin\Input_data_master@hapus_tanggal');

		});

		Route::prefix('kelas')->group(function () {
			Route::post('input', 'Admin\Input_data_master@input_kelas');
			Route::post('list', 'Admin\Input_data_master@all_kelas');//dikirim thn ajar cukup
			Route::post('isi', 'Admin\Master\Kelas@isi_kelas');


			Route::post('tambah/list-siswa', 'Admin\Master\Kelas@list_siswa');// mendapat siswa yang belum mendapat kelas d tahun_ajaran tsb
			Route::post('tambah/siswa', 'Admin\Master\Kelas@tambah_siswa_kelas');
//            Route::post('kelas', 'Admin\Input_data_master@isi_kelas');

			Route::prefix('hapus')->group(function () {
				Route::post('kelas', 'Admin\Master\Kelas@hapus_kelas');
				Route::post('siswa', 'Admin\Master\Kelas@hapus_siswa');
			});


			Route::prefix('ubah')->group(function () {
//				Route::post('nama-kelas', 'Admin\Master\Kelas@ubah_nama_kelas');
//				Route::post('ketua-kelas', 'Admin\Master\Kelas@ubah_ketuakelas');
//				Route::post('wali-kelas', 'Admin\Master\Kelas@ubah_walikelas');
//				Route::post('wali-kelas/list', 'Admin\Master\Kelas@list_walikelas');
//				Route::post('level-siswa', 'Admin\Master\Kelas@ubah_lv_siswa');


				Route::put('nama-kelas', 'Admin\Master\Kelas@ubah_nama_kelas');
				Route::post ('ketua-kelas', 'Admin\Master\Kelas@ubah_ketuakelas');
				Route::put('wali-kelas', 'Admin\Master\Kelas@ubah_walikelas');
                Route::post('wali-kelas/list', 'Admin\Master\Kelas@list_walikelas');
				Route::put('level-siswa', 'Admin\Master\Kelas@ubah_lv_siswa');

			});
		});
	});
});


//siswa

Route::prefix('siswa')->group(function () {

	Route::prefix('presensi')->group(function () {
		Route::post('buat', 'Siswa\siswa@create_qr');
//		Route::post('isi', 'Siswa\siswa@isi_absen');
		Route::put('isi', 'Siswa\siswa@isi_absen');
		Route::post('saya', 'Siswa\presensi@presensi_siswa');

		Route::prefix('lihat')->group(function () {//pengurus kelas only
			Route::post('harian', 'Siswa\presensi@get_presensi_harian');
//            Route::post('perbulan', 'Siswa\presensi@get_presensi_perbulan');
		});

	});

	Route::post('dashboard', 'Siswa\siswa@dashboard');
	Route::post('profil', 'Siswa\siswa@profil');

});


Route::prefix('parent')->group(function () {

    Route::post('login', 'Parent\dashboard@login');

    Route::post('dashboard', 'Parent\dashboard');
    Route::post('profil', 'Parent\dashboard@profil');
    Route::post('rekap_presensi', 'Parent\dashboard@presensi');
    Route::post('ganti_pass', 'Parent\dashboard@ubah_pass_parent');

});
