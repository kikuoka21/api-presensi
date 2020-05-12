<?php

namespace App\Modules_parent;

use DB;

class User_parent
{
    public function get_wali($uid)
    {
        $query = "SELECT * FROM wali where nis = :xuid ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid
        ]);
        return $result;
    }

    public function input_tokenmobile($uid, $token, $token_fmc)
    {
        $query = "UPDATE wali SET token= :token, token_firebase = :key where nis= :xuid";
        DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'key' => $token_fmc,
            'token' => $token
        ]);
    }

    public function getdata_dashboard($uid)
    {
        $query = "SELECT nama_siswa as nama FROM siswa  where  nis  =:nis ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'nis' => $uid
        ]);
        return object_get($result[0], 'nama');
    }

    public function chek_token_wali($xuid, $token)
    {
        $query = "SELECT password, token FROM wali where nis = :xuid ";
        $getid = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $xuid
        ]);
        if (!$getid) {
            return false;
        } else {
            if (object_get($getid[0], 'token') == $token) {
                return true;
            } else {
                return false;
            }
        }
    }

    public function getabsen_wali($username, $tanggal)
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
}