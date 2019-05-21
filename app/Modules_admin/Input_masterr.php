<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/20/2019
 * Time: 9:53 AM
 */

namespace App\Modules_admin;

use DB;

class Input_masterr
{
	public function check_data_siswa($nis)
	{
		$query = "select nis from siswa where nis= :nis";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis
		]);
		return $result;
	}

	public function input_siswa($nis,$nisn,  $nama, $tgl_lahir, $alamat)
	{
		$query = "INSERT INTO siswa ( nis,nisn,  nama_siswa, tgl_lahir, alamat) 
					VALUES (:nis ,:nisn,:nama ,:tgl, :alamat)";
		DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis,
			'nisn'=> $nisn,
			'nama' => $nama,
			'tgl' => $tgl_lahir,
			'alamat' => $alamat
		]);
	}


	public function input_users($id, $pass, $akses)
	{
		$query = "INSERT INTO users ( username, password, akses) 
					VALUES (:id ,:pass, :akses)";
		DB::connection('mysql')->select(DB::raw($query), [
			'id' => $id,
			'pass' => $pass,
			'akses' => $akses
		]);
	}


	public function check_data_staff($nip)
	{
		$query = "select nama_staf from staf where nip= :nip";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'nip' => $nip
		]);
		return $result;

	}

	public function input_staff($nip, $nama, $level)
	{
		$query = "INSERT INTO staf (nip, nama_staf, level) 
					VALUES (:nis ,:nama , :level)";
		DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nip,
			'nama' => $nama,
			'level' => $level
		]);
	}

	public function check_data_libur($tgl)
	{
		$query = "select tanggal from hari_libur where tanggal	= :tgl";
		$result = DB::connection('mysql')->select(DB::raw($query), [
			'tgl' => $tgl
		]);
		return $result;

	}

	public function input_libur($tanggal, $ket)
	{
		$query = "INSERT INTO hari_libur ( tanggal, ket) 
					VALUES (:tgl ,:ket )";
		DB::connection('mysql')->select(DB::raw($query), [
			'tgl' => $tanggal,
			'ket' => $ket
		]);
	}

}