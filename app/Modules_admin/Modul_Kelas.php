<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/20/2019
 * Time: 9:53 AM
 */

namespace App\Modules_admin;

use DB;

class Modul_Kelas
{
    public function list_siswa($thn_ajaran, $tahun_lahir)
    {
        $query = "select  nis, nisn, nama_siswa as nama 
                    from siswa where  siswa.tgl_lahir like :thn_lahir and nis not in (
                    select isikelas.nis from siswa, isikelas, kelas 
                    where isikelas.id_kelas = kelas.id_kelas 
                    and kelas.tahun_ajar = :thn)";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $thn_ajaran,
            'thn_lahir' => $tahun_lahir . '%'
        ]);
        return $result;
    }

    public function input_siswa_kelas($idkelas, $nis)
    {
        $query = "INSERT INTO isikelas ( nis, id_kelas) 
					VALUES ( :nis , :id  )";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
        return $result;
    }

    public function check_siswa_kelas($idkelas, $nis)
    {
        $query = "select nis from isikelas where nis = :nis and id_kelas = :id";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
        return $result;
    }

    public function hapus_siswa_kelas($idkelas, $nis)
    {
        $query = "DELETE FROM  isikelas where nis = :nis and id_kelas = :id";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
        return $result;
    }


}