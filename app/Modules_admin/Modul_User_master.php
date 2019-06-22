<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/13/2019
 * Time: 12:40 PM
 */

namespace App\Modules_admin;

use App\Modules_siswa\Tool;
use DB;

class Modul_user_master
{
    public function cari_siswa($nama, $tahun)
    {
        $query = "select nis , nama_siswa FROM siswa where nama_siswa like :nama and tgl_lahir like :tahun or nis like :namaa and tgl_lahir like :tahunn limit 20";
        $respon = DB::connection('mysql')->select(DB::raw($query), [
            'nama' => '%' . $nama . '%',
            'tahun' => '%' . $tahun . '-%',
            'namaa' => '%' . $nama . '%',
            'tahunn' => '%' . $tahun . '-%'
        ]);
        return $respon;
//        $getid = DB::table('users')->where('username', $nama)->get();
//        return $getid;
    }

    public function get_profil_siswa($nis)
    {
        $getid = DB::table('siswa')->where('nis', $nis)->first();

        return $getid;
    }


    public function update_siswa($nis, $nisn, $nama, $tgl_lahir, $alamat, $tmp_lahir, $agama, $orangtua, $ijazah, $no_ujian)
    {
        $query = "UPDATE siswa SET nisn = :nisn,
                  nama_siswa = :nama,
                  tmp_lahir = :tmp_lahir,
                  tgl_lahir = :tgl,
                  agama = :agama,
                  orang_tua = :orang_tua,
                  alamat = :alamat,
                  no_ijazah = :no_ijazah,
                  no_ujiansmp = :no_ujiansmp
                  where nis = :nis";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'nisn' => $nisn,
            'nama' => $nama,
            'tmp_lahir' => $tmp_lahir,
            'tgl' => $tgl_lahir,
            'agama' => $agama,
            'orang_tua' => $orangtua,
            'alamat' => $alamat,
            'no_ijazah' => $ijazah,
            'no_ujiansmp' => $no_ujian
        ]);

    }

    public function check_data_siswa($nis)
    {
        $query = "select nama_siswa as nama from siswa where nis= :nis";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        return $result;
    }

    public function hapus_data_siswa($nis)
    {
        $query = "DELETE FROM siswa where nis = :nis";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        $query = "DELETE FROM users where username = :nis";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        $query = "DELETE FROM isikelas where nis = :nis";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        $query = "UPDATE kelas SET id_ketua_kelas = :kosong
                  where id_ketua_kelas = :nis ";
        DB::connection('mysql')->select(DB::raw($query), [
            'kosong' => '',
            'nis' => $nis
        ]);
        $query = "DELETE FROM kehadiran where nis = :nis";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
    }

    public function cari_staf($nama)
    {
        $query = "select * FROM staf where lower(nama_staf) like lower(:nama) or nip like :namaa limit 20";
        $respon = DB::connection('mysql')->select(DB::raw($query), [
            'nama' => '%' . $nama . '%',
            'namaa' => '%' . $nama . '%'
        ]);
        return $respon;
//        $getid = DB::table('users')->where('username', $nama)->get();
//        return $getid;
    }

    public function get_profil_staf($nip)
    {
        $getid = DB::table('staf')->where('nip', $nip)->first();

        return $getid;
    }

    public function get_kelas_staf($nip)
    {
        $getid = DB::table('kelas')->select('nama_kelas', 'tahun_ajar')->where('id_wali_kelas', $nip)->orderBy('tahun_ajar', 'desc')->get();

        return $getid;
    }

    public function update_staf($nip, $nama, $level)
    {
        $query = "UPDATE staf SET nama_staf = :nama,
                  level = :level
                  where nip = :nip";
        DB::connection('mysql')->select(DB::raw($query), [
            'nip' => $nip,
            'nama' => $nama,
            'level' => $level
        ]);
        if ($level == 1) {
            $tool = new Tool();
            $tahun = $tool->thn_ajar_skrng();
//            $query = "select id_kelas from kelas where tahun_ajar = :thn and id_wali_kelas = :nip";
            $query = "UPDATE kelas SET id_wali_kelas = :kosong
                  where id_wali_kelas = :nip and tahun_ajar = :thn";
            DB::connection('mysql')->select(DB::raw($query), [
                'kosong' => '',
                'thn' => $tahun,
                'nip' => $nip
            ]);
        }


    }

    public function hapus_data_Staf($nip)
    {
        $query = "DELETE FROM staf where nip = :nip";
        DB::connection('mysql')->select(DB::raw($query), [
            'nip' => $nip
        ]);
        $query = "DELETE FROM users where username = :nip";
        DB::connection('mysql')->select(DB::raw($query), [
            'nip' => $nip
        ]);
        $query = "UPDATE kelas SET id_wali_kelas = :kosong
                  where id_wali_kelas = :nip ";
        DB::connection('mysql')->select(DB::raw($query), [
            'kosong' => '',
            'nip' => $nip
        ]);
    }

}