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
  				and tanggal = :tgl
				order by siswa.nama_siswa asc";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal,
            'kelas' => $kelas
        ]);

        return $result;
    }


}