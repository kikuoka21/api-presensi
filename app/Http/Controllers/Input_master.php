<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 1:31 PM
 */

namespace App\Http\Controllers;

use DB;

class Input_master extends Controller
{

	public function input_siswa($nis, $nama, $tgl_lahir, $alamat){
		$query = "INSERT INTO siswa ( nis, nama_siswa, tgl_lahir, alamat) 
					VALUES (:nis ,:nama ,:tgl, :alamat)";
		DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis,
			'nama'=> $nama,
			'tgl'=> $tgl_lahir,
			'alamat'=> $alamat
		]);
	}
	public function input_staff($nis, $nama, $level){
		$query = "INSERT INTO staf ( nip, nama_staf, level) 
					VALUES (:nis ,:nama ,:tgl, :alamat)";
		DB::connection('mysql')->select(DB::raw($query), [
			'nis' => $nis,
			'nama'=> $nama,
			'level'=> $level
		]);
	}


}