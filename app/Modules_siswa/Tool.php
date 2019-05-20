<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/10/2019
 * Time: 10:49 PM
 */

namespace App\Modules_siswa;


use Carbon\Carbon;

use DB;
//use Illuminate\Support\Facades\DB;

class Tool
{
    function IsJsonString($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function generate_token($key, $id, $type)
    {
        return md5(md5($id . $key) . $type);
    }

    public function thn_ajar_skrng()
    {
        $mytime = Carbon::now();
        $tahun = $mytime->year;
        if ($mytime->month <= 6) {
            return strval($tahun - 1) . $tahun;
        } else {
            return $tahun . strval($tahun + 1);
        }
    }
    public function tgl_skrng()
    {
        $mytime = Carbon::now();
        return $mytime->toDateString();

    }

    public function get_date()
    {
        $mytime = Carbon::now();
        return $mytime->toDateString();
    }

    public function Isi_Log($pesan)
    {
        $mytime = Carbon::now();
        $query = "INSERT INTO log VALUES (:date ,:pesan); ";
        DB::connection('mysql')->select(DB::raw($query), [
            'date' => $mytime->toDateString(),
            'pesan' => $mytime->toTimeString() . ' ' . $pesan
        ]);
    }

    public function tgl_merah()
    {
        $mytime = Carbon::now();
        if ($mytime->isSunday()|| $mytime->isSaturday()) {
            return true;
        } else {
            return false;
        }
    }
}
