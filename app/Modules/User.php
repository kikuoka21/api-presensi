<?php


namespace App\Modules;

use DB;

class User
{

    public function getUser($uid)
    {
        $query = "SELECT username, password, akses FROM users where username = :xuid ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid
        ]);

        return $result;

    }

    public function input_tokenmobile($uid, $token)
    {
        $query = "UPDATE users SET token= :token where username= :xuid";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'token' => $token
        ]);

    }

    public function input_tokenweb($uid, $token)
    {
        $query = "UPDATE users SET token_2= :token where username= :xuid";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'token' => $token
        ]);

    }

    public function getdata_dashboard($uid, $type)
    {

        if ($type == '0') {

            $tool = new Tool();
            $query = "SELECT nama_siswa as nama,level FROM  siswa,  isikelas, kelas where 
                  isikelas.id_kelas=kelas.id_kelas and 
                  isikelas.nis = siswa.nis and 
                  kelas.tahun_ajar=:thn and 
                  siswa.nis  =:nis";
            $result = DB::connection('mysql')->select(DB::raw($query), [
                'nis' => $uid,
                'thn' => $tool->thn_ajar_skrng()
            ]);

        } else {
            $query = "SELECT nama_staf as nama, level FROM staf  where  nip  =:nis ";
            $result = DB::connection('mysql')->select(DB::raw($query), [
                'nis' => $uid
            ]);
        }

        return $result;
    }


    public function chek_token($xuid, $token, $type)
    {
        $query = "SELECT akses, token_2, token FROM users where username = :xuid ";
        $getid = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $xuid
        ]);
        if (!$getid) {
            return false;
        } else {
            if (object_get($getid[0], 'akses') == '1' && $type == 'www') {
                if (object_get($getid[0], 'token_2') == $token) {
                    return true;
                } else {
                    return false;
                }
            } else {
                if (object_get($getid[0], 'token') == $token) {
                    return true;
                } else {
                    return false;
                }
            }
        }
    }
}