<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/10/2019
 * Time: 10:49 PM
 */

namespace App\Modules;


use http\Exception;

class Tool
{
    function IsJsonString($str) {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }
}
