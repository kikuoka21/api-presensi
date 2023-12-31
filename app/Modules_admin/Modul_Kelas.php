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

    public function validasi_kelas($kd_kls)
    {
        $query = "select id_kelas , tahun_ajar, nama_kelas from kelas where id_kelas = :kd";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'kd' => $kd_kls
        ]);

        return $hasil;
    }

    public function get_siswakelas($kd_kls)
    {
        $query = "select siswa.nis, siswa.nama_siswa, isikelas.level 
                  from siswa, isikelas where isikelas.id_kelas = :kd and siswa.nis = isikelas.nis
                  order by nama_siswa asc";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'kd' => $kd_kls
        ]);

        return $hasil;
    }

    public function get_ketua_kelas($kd_kls)
    {
        $query = "select siswa.nis, siswa.nama_siswa as nama from siswa, kelas
                  where kelas.id_kelas = :kd and
                  siswa.nis = kelas.id_ketua_kelas";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'kd' => $kd_kls
        ]);

        return $hasil;
    }

    public function get_wali_kelas($kd_kls)
    {
        $query = "select staf.nip , staf.nama_staf as nama from kelas, staf
                  where kelas.id_kelas = :kd and
                  staf.nip = kelas.id_wali_kelas";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'kd' => $kd_kls
        ]);

        return $hasil;
    }

    public function list_siswa($cari, $thn_ajaran, $tahun_lahir)
    {

        $query = "select  nis,  nama_siswa as nama 
                    from siswa where  siswa.tgl_lahir like :thn_lahir and nis not in (
                    select isikelas.nis from isikelas inner join kelas 
                    on isikelas.id_kelas = kelas.id_kelas 
                    where kelas.tahun_ajar = :thn)
                    and ( lower(nama_siswa) like lower(:cari) or nis like :cari2)
                    order by nama_siswa asc 
                    limit 40";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $thn_ajaran,
            'thn_lahir' => $tahun_lahir . '%',
            'cari' => '%' . $cari . '%',
            'cari2' => '%' . $cari . '%'
        ]);
        return $result;
    }

    public function input_siswa_kelas($idkelas, $nis)
    {
        $query = "INSERT INTO isikelas ( nis, id_kelas) 
					VALUES ( :nis , :id  )";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
    }

    public function check_siswa_isikelas($idkelas, $nis)
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
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
        $query = "UPDATE kelas SET id_ketua_kelas = :ksng where id_ketua_kelas = :nis and id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'ksng' => '',
            'nis' => $nis
        ]);
    }

    public function ganti_lv_siswa($idkelas, $nis, $level)
    {
        $query = "UPDATE isikelas SET level = :level where nis= :nis and id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis,
            'level' => $level
        ]);
        if ($level == 0) {
            $query = "UPDATE kelas SET id_ketua_kelas = :kosong where id_ketua_kelas = :nis and id_kelas = :id";
            DB::connection('mysql')->select(DB::raw($query), [
                'id' => $idkelas,
                'nis' => $nis,
                'kosong' => ''
            ]);
        }

    }

    public function list_walikelas($tahun)
    {
        $query = "select  nip, nama_staf as nama 
                        from staf where  nip not in (
                        select id_wali_kelas from  kelas 
                        where tahun_ajar = :thn)";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'thn' => $tahun
        ]);
        return $result;
    }

    public function ubah_walikelas($idkelas, $nip)
    {
        $query = "UPDATE kelas SET id_wali_kelas = :nip where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nip' => $nip
        ]);
    }

    public function ubah_ketua_kelas($idkelas, $nis)
    {
        $query = "UPDATE kelas SET id_ketua_kelas = :nis where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis
        ]);
        $query = "UPDATE isikelas SET level = :level where nis= :nis and id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nis' => $nis,
            'level' => '1'
        ]);
    }

    public function ubah_nama_kelas($idkelas, $nama_kelas)
    {
        $query = "UPDATE kelas SET nama_kelas = :nama where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nama' => $nama_kelas
        ]);
    }

    public function check_nama_kelas($idkelas, $nama_kelas)
    {
        $query = "select * from kelas where nama_kelas = :nama and tahun_ajar in 
                  (select tahun_ajar from kelas where LOWER(id_kelas) = :id)";
        $respon = DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas,
            'nama' => strtolower($nama_kelas)
        ]);
        return $respon;
    }

    public function hapus_kelas($idkelas)
    {
        $query = "DELETE FROM kelas where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas
        ]);

        $query = "DELETE FROM  validasi_presensi where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas
        ]);


        $query = "DELETE FROM  isikelas where id_kelas = :id";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $idkelas
        ]);

    }


}