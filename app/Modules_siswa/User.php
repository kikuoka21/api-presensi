<?php


namespace App\Modules_siswa;

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
        DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'token' => $token
        ]);

    }

    public function input_tokenweb($uid, $token)
    {
        $query = "UPDATE users SET token_2= :token where username= :xuid";
        DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'token' => $token
        ]);

    }

    public function getdata_dashboard($uid, $type)
    {

        if ($type == '0') {

            $tool = new Tool();
            $query = "SELECT level FROM  siswa,  isikelas, kelas where 
                  isikelas.id_kelas=kelas.id_kelas and 
                  isikelas.nis = siswa.nis and 
                  kelas.tahun_ajar=:thn and 
                  siswa.nis  =:nis";
            $result = DB::connection('mysql')->select(DB::raw($query), [
                'nis' => $uid,
                'thn' => $tool->thn_ajar_skrng()
            ]);

            if ($result)
                $level = object_get($result[0], 'level');
            else
                $level = 0;

            $query = "SELECT nama_siswa as nama FROM siswa  where  nis  =:nis ";
            $result = DB::connection('mysql')->select(DB::raw($query), [
                'nis' => $uid
            ]);
            $nama = object_get($result[0], 'nama');

        } else {
            $query = "SELECT nama_staf as nama, level FROM staf  where  nip  =:nis ";
            $result = DB::connection('mysql')->select(DB::raw($query), [
                'nis' => $uid
            ]);
            $nama = object_get($result[0], 'nama');
            $level = object_get($result[0], 'level');

        }

        $data = [
            'nama' => $nama,
            'level' => $level
        ];

        return $data;
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

    public function getpass_lama($uid, $xpass)
    {
        $query = "SELECT username FROM users where username = :xuid and password= :pass  ";
        $getid = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'pass' => $xpass
        ]);
        return $getid;
    }

    public function update_pass($uid, $xpass)
    {
        $query = "UPDATE users SET password= :pass , token= :tkn , token_2= :tkn2 where username= :xuid";
        DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'pass' => $xpass,
            'tkn' => '',
            'tkn2' => ''
        ]);

    }

    public function getakses_admin($xuid)
    {
        $getid = DB::table('users')->select('akses')->where('username', $xuid)->get();
        if (!$getid)
            return false;
        else
            if (object_get($getid[0], 'akses') == '1')
                return true;
            else
                return false;


    }

    public function getakses_admin_piket($xuid)
    {
        $query = "SELECT level FROM staf where nip = :xuid ";
        $getid = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $xuid
        ]);
        if (!$getid) {
            return false;
        } else {
            if (object_get($getid[0], 'level') == '1') {
                return true;
            } else {
                return false;
            }
        }
    }

    public function comparepass($uid, $pass)
    {
        $query = "SELECT username FROM users where username = :xuid and  password= :pass";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'xuid' => $uid,
            'pass' => $pass
        ]);

        return $result;

    }
}