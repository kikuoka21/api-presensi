<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/10/2019
 * Time: 10:49 PM
 */

namespace App\Modules;


use Carbon\Carbon;
use http\Exception;

class Tool
{
    function IsJsonString($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }
    public function generate_token($key, $id)
    {
        $mytime = Carbon::now();
        return md5($mytime->hour().md5($id.$key));
    }
}
