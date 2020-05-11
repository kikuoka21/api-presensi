<?php


namespace App\Modules_parent;

use App\Modules_siswa\Tool;
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
}