<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/13/2019
 * Time: 9:14 AM
 */

namespace App\Modules_siswa;

use Illuminate\Support\Facades\DB;


class M_siswa
{

    public function get_profil_siswa($nis)
    {
        $getid = DB::table('siswa')->where('nis', $nis)->first();

        return $getid;
    }


    public function history_kelas($nis)
    {
        $query = "SELECT isikelas.id_kelas as kd_kelas , nama_kelas as kelas , kelas.tahun_ajar FROM  isikelas, kelas where 
					isikelas.id_kelas = kelas.id_kelas and 
					isikelas.nis = :nis 
					order by kelas.tahun_ajar desc ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        return $result;
    }

//order by siswa.nama_siswa asc, kelas.nama_kelas  asc

    public function getKels($nis, $thn)
    {
        $query = "SELECT kelas.id_kelas as kd_kels, nama_kelas as kelas FROM  isikelas, kelas where 
					isikelas.id_kelas = kelas.id_kelas and 
					isikelas.nis = :nis and 
					kelas.tahun_ajar = :thn ";

        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'thn' => $thn
        ]);

        return $result;
    }


    public function get_flag_($kode, $tgl)
    {
        $query = "SELECT token FROM validasi_presensi where id_kelas = :kode and tanggal = :tgl";

        $result = DB::connection('mysql')->select(DB::raw($query), [
            'kode' => $kode,
            'tgl' => $tgl
        ]);

        return $result;
    }

    public function insert_token($kode, $tgl, $token)
    {

        $query = "UPDATE validasi_presensi SET token= :token , tanggal = :tgl where id_kelas= :kode ";
        DB::connection('mysql')->select(DB::raw($query), [
            'kode' => $kode,
            'tgl' => $tgl,
            'token' => $token
        ]);

    }

    public function get_all_siswa($kd_kls)
    {
        $query = "SELECT nis FROM isikelas where id_kelas = :kode";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'kode' => $kd_kls
        ]);
        return $result;

    }


    public function create_absen($nis, $tgl, $kd_kelas)
    {
        $query = "INSERT INTO kehadiran ( nis, tanggal, id_kelas, stat , ket) VALUES (:nis ,:tgl ,:kode_kelas, 'A', 'Belum Absen')";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'tgl' => $tgl,
            'kode_kelas' => $kd_kelas
        ]);
    }


    public function check_absen($nis, $tgl, $kd_kelas)
    {
        $query = "SELECT stat FROM kehadiran where nis = :nis and tanggal = :tgl and id_kelas = :kode ";

        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'tgl' => $tgl,
            'kode' => $kd_kelas

        ]);

        return $result;
    }

    public function update_absen($nis, $tgl, $kd_kelas, $stat, $ket)
    {

        $query = "UPDATE kehadiran SET stat= :stat , ket = :ket where tanggal= :tgl and nis =:nis and id_kelas= :idkelas ";
        DB::connection('mysql')->select(DB::raw($query), [
            'stat' => $stat,
            'ket' => $ket,
            'tgl' => $tgl,
            'nis' => $nis,
            'idkelas' => $kd_kelas
        ]);

    }
}