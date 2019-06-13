<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/13/2019
 * Time: 12:40 PM
 */

namespace App\Modules_admin;

use DB;

class Modul_siswa
{
    public function cari_siswa($nama, $tahun)
    {
        $query = "select nis , nama_siswa FROM siswa where nama_siswa like :nama and tgl_lahir like :tahun or nis like :namaa and tgl_lahir like :tahunn ";
        $respon=DB::connection('mysql')->select(DB::raw($query), [
            'nama' => '%' . $nama . '%',
            'tahun'=>  '%' . $tahun . '-%',
            'namaa' => '%' . $nama . '%',
            'tahunn'=>  '%' . $tahun . '-%'
        ]);
        return $respon;
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

}