<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/20/2019
 * Time: 9:53 AM
 */

namespace App\Modules_admin;

use DB;

class Input_masterr
{
    public function check_data_siswa($nis)
    {
        $query = "select nama_siswa as nama from siswa where nis= :nis";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis
        ]);
        return $result;
    }

    public function input_siswa($nis, $nisn, $nama, $tgl_lahir, $alamat, $tmp_lahir, $agama, $orangtua, $ijazah, $no_ujian)
    {
        $query = "INSERT INTO siswa ( nis, nisn, nama_siswa, tgl_lahir, alamat, tmp_lahir, 
                    agama, orang_tua,no_ijazah, no_ujiansmp )
					VALUES (:nis ,:nisn,:nama ,:tgl, :alamat,:tmp_lahir, :agama, :orang_tua,:no_ijazah, :no_ujiansmp)";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nis,
            'nisn' => $nisn,
            'nama' => $nama,
            'tgl' => $tgl_lahir,
            'alamat' => $alamat,
            'tmp_lahir' => $tmp_lahir,
            'agama' => $agama,
            'orang_tua' => $orangtua,
            'no_ijazah' => $ijazah,
            'no_ujiansmp' => $no_ujian
        ]);
        $this->input_users($nis, 'd1fdc1c3d4fcaf10e212d10a896ee927', '0');

    }


    private function input_users($id, $pass, $akses)
    {
        $query = "INSERT INTO users ( username, password, akses) 
					VALUES (:id ,:pass, :akses)";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $id,
            'pass' => $pass,
            'akses' => $akses
        ]);
    }


    public function check_data_staff($nip)
    {
        $query = "select nama_staf from staf where nip= :nip";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nip' => $nip
        ]);
        return $result;

    }

    public function input_staff($nip, $nama, $level)
    {
        $query = "INSERT INTO staf (nip, nama_staf, level) 
					VALUES (:nis ,:nama , :level)";
        DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $nip,
            'nama' => $nama,
            'level' => $level
        ]);
        $this->input_users($nip, 'd1fdc1c3d4fcaf10e212d10a896ee927', '1');
    }

    public function check_data_libur($tgl)
    {
        $query = "select tanggal as tgl,ket  from hari_libur where tanggal	= :tgl";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tgl
        ]);
        return $result;

    }

    public function input_libur($tanggal, $ket)
    {
        $query = "INSERT INTO hari_libur ( tanggal, ket) 
					VALUES (:tgl ,:ket )";
        DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal,
            'ket' => $ket
        ]);
    }


    public function check_data_kelas($nama, $thn)
    {
        $query = "select nama_kelas from kelas where nama_kelas	= :nama and tahun_ajar = :thn";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nama' => $nama,
            'thn' => $thn
        ]);
        return $result;

    }

    public function generate_id_kelas()
    {
        $query = "SELECT MAX(id_kelas) as data FROM kelas";
        $hasil = DB::connection('mysql')->select(DB::raw($query));
        if (!$hasil)
//
            return "K00001";

        else {
            $idkelas = substr(object_get($hasil[0], 'data'), 1) + 1;
            $panjang = strlen($idkelas);
            while ($panjang < 5) {
                $idkelas = '0' . $idkelas;
                $panjang = strlen($idkelas);
            }
            $idkelas = 'K' . $idkelas;
            return $idkelas;
        }

    }

    public function input_kelas($id_kelas, $nama_kelas, $tahun_ajar)
    {
        $query = "INSERT INTO kelas ( id_kelas, nama_kelas, tahun_ajar) 
					VALUES (:id ,:nama, :tahun )";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $id_kelas,
            'nama' => $nama_kelas,
            'tahun' => $tahun_ajar
        ]);

        $query = "INSERT INTO validasi_presensi ( id_kelas) 
					VALUES (:id)";
        DB::connection('mysql')->select(DB::raw($query), [
            'id' => $id_kelas
        ]);

    }

    public function history_tanggal($tanggal)
    {
        $query = "select tanggal as tgl, ket from hari_libur where tanggal > :tgl order by tanggal asc ";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal
        ]);

        return $hasil;

    }
    public function history_tanggal2($tanggal)
    {
        $query = "select tanggal as tgl, ket from hari_libur where tanggal like :tgl order by tanggal asc";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal.'%'
        ]);

        return $hasil;
    }
    public function all_kelas($tanggal)
    {
        $query = "select tanggal as tgl, ket from hari_libur where tanggal like :tgl order by tanggal asc";
        $hasil = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal.'%'
        ]);

        return $hasil;
    }



}