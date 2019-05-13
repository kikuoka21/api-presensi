<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/13/2019
 * Time: 9:14 AM
 */

namespace App\Modules;

use Illuminate\Support\Facades\DB;


class M_siswa
{
	public function getKels($nis, $thn)
	{
		$query = "SELECT kelas.id_kelas as kd_kels, nama_kelas as kelas FROM  isikelas, kelas where 
					isikelas.id_kelas = kelas.id_kelas and 
					isikelas.nis = :nis and 
					kelas.tahun_ajar = :thn ";

		$result = DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis,
			'thn' => $thn
		]);

		return $result;
	}

	public function buat_absen($nis, $tgl, $kd_kelas)
	{
//		$query = "INSERT INTO (nis, tanggal, id_kelas) kehadiran VALUES (:nis ,:tgl ,:kode_kelas)";
		$query = "INSERT INTO kehadiran ( nis, tanggal, id_kelas, stat , ket) VALUES (:nis ,:tgl ,:kode_kelas, 'A', 'tidak absen')";
		DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis,
			'tgl' => $tgl,
			'kode_kelas' => $kd_kelas
		]);
	}
	public function get_flag_($kode, $tgl){
		$query = "SELECT token FROM validasi_absen where id_kelas = :kode and tanggal = ::tgl";

		$result = DB::connection('mysql')->select(DB::raw($query), [
			'kode' => $kode,
			'tgl' => $tgl
		]);

		return $result;
	}
}