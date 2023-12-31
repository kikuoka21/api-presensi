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


    public function all_kelas($thn_ajar)
    {
        $query = "select id_kelas , nama_kelas 
                  from kelas where tahun_ajar = :tgl
                  order by nama_kelas asc";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $thn_ajar
        ]);


        return $hasil;
    }
    public function get_siswakelas($kd_kls)
    {
        $query = "select siswa.nis, siswa.nama_siswa
                  from siswa, isikelas where isikelas.id_kelas = :kd and siswa.nis = isikelas.nis
                  order by nama_siswa asc";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'kd' => $kd_kls
        ]);

        return $hasil;
    }
    public function getabsen_all2($tanggal, $thnajar, $stat)
    {
        $query = "select siswa.nis,siswa.nama_siswa as nama,kelas.nama_kelas as kelas, stat, ket  from siswa
                  inner join kehadiran on kehadiran.nis = siswa.nis
                  inner join isikelas on isikelas.nis = siswa.nis
                  inner join kelas on isikelas.id_kelas = kelas.id_kelas
                  where  kehadiran.stat = :status
                  and tanggal = :tgl
                  and kelas.tahun_ajar = :thn
                  order by kelas.nama_kelas asc ";

        $result = DB::connection('mysql')->select(DB::raw($query), [
            'status' => $stat,
            'tgl' => $tanggal,
            'thn' => $thnajar,
        ]);

        return $result;
    }

//	public function getabsen_kelas($tanggal, $username)
//	{
//		$query = "SELECT siswa.nis,siswa.nama_siswa as nama,kelas.nama_kelas as kelas, stat, ket FROM kehadiran, kelas, siswa
//				where kehadiran.id_kelas = kelas.id_kelas
//				and kelas.id_wali_kelas = :nip
//				and kehadiran.nis= siswa.nis
//				and kehadiran.stat = 'A'
//  				and tanggal = :tgl
//				order by siswa.nama_siswa asc";
//		$result = DB::connection('mysql')->select(DB::raw($query), [
//			'tgl' => $tanggal,
//			'nip' => $username
//		]);
//
//		return $result;
//	}

	public function getsiswa($username, $thn){
	    $query = "select nis , kelas.nama_kelas from kelas
                    inner join isikelas 
                    on isikelas.id_kelas = kelas.id_kelas
                    where id_wali_kelas = :nip
                    and kelas.tahun_ajar = :thn";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $thn,
            'nip' => $username
        ]);

        return $result;
    }

	public function namakelas($username, $thn){
	    $query = "select id_kelas , nama_kelas from kelas
                    where id_wali_kelas = :nip
                    and kelas.tahun_ajar = :thn";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $thn,
            'nip' => $username
        ]);

        return $result;
    }
	public function getsiswasemua( $thn){
	    $query = "select siswa.nis, kelas.nama_kelas  from siswa
                  inner join isikelas on isikelas.nis = siswa.nis 
                  inner join kelas on isikelas.id_kelas = kelas.id_kelas 
                  where kelas.tahun_ajar = :thn";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $thn
        ]);

        return $result;
    }

}