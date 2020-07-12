<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/20/2019
 * Time: 9:53 AM
 */

namespace App\Modules_admin;

use DB;

class coba_coba
{
    public $tahunajar, $semester, $xnip, $jenjang, $fakultas, $periode, $matkul, $kelompok, $pertemuan, $sesi, $tanggal, $listMahasiswa;

    public function entry()
    {
        $message[] = [];
//        foreach ($this->listMahasiswa as $mahasiswa) {
        $listMahasiswa = (array)$this->listMahasiswa;
//        for ($i = 0; $i < count($listMahasiswa); $i++) {
//            $mahasiswa = (object)$listMahasiswa[$i];
//            $message[$i] = [
//                'namatype ' => gettype($mahasiswa),
//                'nim' => $mahasiswa
//            ];
//        }
        $i = 0;
        foreach ($this->listMahasiswa as $mahasiswa) {
//            $mahasiswa = (object)$mahasiswa;
            $message[$i] = [
                'namatype ' => gettype($mahasiswa),
                'nim' => $mahasiswa
            ];
            $i++;
        }

        return $message;
    }

}