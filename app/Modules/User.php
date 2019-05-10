<?php


namespace App\Modules;

use Carbon\Carbon;
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

    public function input_token($uid, $token)
    {
        $query = "UPDATE users SET token= :token where username= :xuid";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'token' => $token
        ]);
        return $result;
    }

    public function generate_token($key)
    {
        $mytime = Carbon::now();
        return $mytime->hour();
    }

}