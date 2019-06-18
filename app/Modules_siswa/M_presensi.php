<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/7/2019
 * Time: 2:08 AM
 */

namespace App\Modules_siswa;


use Illuminate\Support\Facades\DB;


class M_presensi
{
    public function getabsen_kelas_tanggal($tanggal, $kelas)
    {
        $query = "SELECT siswa.nis,siswa.nama_siswa as nama, stat, ket FROM kehadiran, kelas, siswa
				where kehadiran.id_kelas = :kelas
				and kehadiran.nis= siswa.nis
                and kehadiran.id_kelas = kelas.id_kelas	
  				and tanggal = :tgl
				order by siswa.nama_siswa asc";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal,
            'kelas' => $kelas
        ]);

        return $result;
    }
    public function getabsen_kelas_siswa($kelas)
    {
        $query = "SELECT siswa.nis,siswa.nama_siswa as nama FROM isikelas, kelas, siswa
				where isikelas.id_kelas = :kelas
                and isikelas.id_kelas = kelas.id_kelas
				and isikelas.nis= siswa.nis
				order by siswa.nama_siswa asc";
        $result = DB::connection('mysql')->select(DB::raw($query), [

            'kelas' => $kelas
        ]);

        return $result;
    }
    public function create_absen($nis, $tgl, $kd_kelas)
    {
        $query = "INSERT INTO kehadiran ( nis, tanggal, id_kelas, stat , ket) VALUES (:nis ,:tgl ,:kode_kelas, 'A', 'Tidak Dibuatnya QR')";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'tgl' => $tgl,
            'kode_kelas' => $kd_kelas
        ]);
    }



}