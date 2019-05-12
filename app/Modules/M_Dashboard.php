<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/12/2019
 * Time: 9:39 AM
 */

namespace App\Modules;

use DB;

class M_Dashboard
{
    public function harilibur($tanggal)
    {
        $query = "SELECT ket FROM hari_libur where tanggal = :tgl ";
        $result = DB::connection('mysql')->select(DB::raw($query), [
            'tgl' => $tanggal
        ]);

        return $result;

    }
}