<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/12/2019
 * Time: 9:39 AM
 */

namespace App\Modules_siswa;

use DB;

class M_Dashboard
{
	public function harilibur($tanggal)
	{
		$query = "SELECT ket FROM hari_libur where tanggal = :tgl ";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'tgl' => $tanggal
		]);

		return $result;

	}

	public function get_kelas($thn_ajar)
	{

		$getid = DB::table('kelas')->where('tahun_ajar', $thn_ajar)->get();

		return $getid;

	}

	public function get_data_kelas($id)
	{

		$getid = DB::table('kelas')
			->select('nama_kelas as nama', 'id_wali_kelas as wali', 'id_ketua_kelas as ketua', 'tahun_ajar')
			->where('id_kelas', $id)
			->first();

		return $getid;


	}

	public function get_kode_kelas($thn_ajar, $nis)
	{

		$query = "SELECT kelas.id_kelas FROM kelas,isikelas where
					kelas.tahun_ajar = :thn
					and kelas.id_kelas = isikelas.id_kelas
					and isikelas.nis = :nis
					";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'thn' => $thn_ajar,
			'nis' => $nis
		]);

		return $result;


	}

	public function getnama_siswa($id)
	{

		$getid = DB::table('siswa')
			->select('nama_siswa')
			->where('nis', $id)
			->first();

		return $getid->nama_siswa;


	}
}