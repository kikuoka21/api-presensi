<?php


namespace App\Modules;
use DB;

class User
{
    public $uid;

    public function getUser(){
        $query = "SELECT username, password FROM users where username = :xuid ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $this->uid
        ]);

        return $result;

    }
}