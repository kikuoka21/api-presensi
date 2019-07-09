<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/7/2019
 * Time: 2:08 AM
 */

namespace App\Modules_siswa;


use DB;


class M_presensi
{

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

    public function create_absen($nis, $tgl)
    {
        $query = "INSERT INTO kehadiran ( nis, tanggal, stat , ket) VALUES (:nis ,:tgl , 'A', 'Belum Absen')";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'tgl' => $tgl
        ]);
    }

    public function getabsen_siswa($tanggal, $username)
    {
        $query = "SELECT tanggal, stat, ket FROM kehadiran, siswa
				where kehadiran.nis= siswa.nis
                and siswa.nis = :nis	
  				and tanggal = :tgl
				order by siswa.nama_siswa asc";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal,
            'nis' => $username
        ]);

        return $result;

    }

    public function getabsen_siswa2($tanggal, $username)
    {
        $query = "SELECT stat, ket FROM kehadiran, siswa
				where kehadiran.nis= siswa.nis
                and siswa.nis = :nis	
  				and tanggal = :tgl
				order by siswa.nama_siswa asc";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal,
            'nis' => $username
        ]);

        return $result;

    }

    public function update_persiswa($tanggal, $username, $stat, $admin, $ket)
    {
        DB::table('kehadiran')
            ->where('nis', $username)
            ->where('tanggal', $tanggal)
            ->update([
                'stat' => $stat,
                'ket' => $ket
            ]);

    }

    public function update_perkelas($tanggal, $stat, $id_kelas, $admin, $ket)
    {
        DB::table('kehadiran')
//            ->where('id_kelas', $kelas)
            ->Join('isikelas', 'isikelas.nis', '=', 'kehadiran.nis')
            ->where('tanggal', $tanggal)
            ->where('isikelas.id_kelas', $id_kelas)
            ->update([
                'stat' => $stat,
                'ket' => $ket
            ]);

    }


}