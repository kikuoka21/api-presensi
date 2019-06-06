<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 10:51 AM
 */

namespace App\Modules_admin;

use DB;

class M_admin
{
	public function getakses($uid)
	{
		$query = "SELECT level FROM staf where nip = :xuid";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'xuid' => $uid
		]);

		return $result;

	}

	public function getabsen_all($tanggal)
	{
		$query = "SELECT siswa.nis,siswa.nama_siswa as nama,kelas.nama_kelas as kelas, stat, ket FROM kehadiran, kelas, siswa
				where kehadiran.id_kelas = kelas.id_kelas 
				and kehadiran.nis= siswa.nis
				and kehadiran.stat = 'A'
				and tanggal = :tgl
				order by siswa.nama_siswa asc, kelas.nama_kelas  asc ";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'tgl' => $tanggal
		]);

		return $result;

	}

	public function getabsen_kelas($tanggal, $username)
	{
		$query = "SELECT siswa.nis,siswa.nama_siswa as nama,kelas.nama_kelas as kelas, stat, ket FROM kehadiran, kelas, siswa
				where kehadiran.id_kelas = kelas.id_kelas 
				and kelas.id_wali_kelas = :nip
				and kehadiran.nis= siswa.nis					
				and kehadiran.stat = 'A'
  				and tanggal = :tgl
				order by siswa.nama_siswa asc";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'tgl' => $tanggal,
			'nip' => $username
		]);

		return $result;
	}
}