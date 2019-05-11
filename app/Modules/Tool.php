<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/10/2019
 * Time: 10:49 PM
 */

namespace App\Modules;


use Carbon\Carbon;

class Tool
{
    function IsJsonString($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function generate_token($key, $id, $type)
    {
        $mytime = Carbon::now();
        return md5($mytime->hour() . md5($id . $key) . $type);
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
}
