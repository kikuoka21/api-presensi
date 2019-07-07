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
        return md5(md5('%!' . $id . $key) . $type . '!%');
    }

    public function thn_ajar_skrng()
    {
        $mytime = Carbon::now();
        $tahun = $mytime->year;
        if ($mytime->month <= 5) {
            return strval($tahun - 1) . $tahun;
        } else {
            return $tahun . strval($tahun + 1);
        }
    }

    public function thn_ajar_pertanggal($tanggal)
    {
        $mytime = date_create($tanggal);
        $tahun = date_format($mytime, "Y");
        if (date_format($mytime, "m") <= 5) {
            return strval($tahun - 1) . $tahun;
        } else {
            return $tahun . strval($tahun + 1);
        }
    }

    public function batasan_tglskrng($tanggal)
    {
        $mytime = date_create($tanggal);
        $skrng = date_create($this->tgl_skrng());
        if ($skrng >= $mytime)
            return true;
        else
            return false;
    }

    public function bulan_depan($tanggal)
    {
        $mytime = date_create($tanggal);
        date_add($mytime, date_interval_create_from_date_string("1 months"));
        return date_format($mytime, "Y-m-d");
//
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
        if ($mytime->isSunday() || $mytime->isSaturday()) {
            return true;
        } else {
            return false;
        }
    }

    public function convert_tgl_merah($tgl)
    {
        $mytime = new Carbon($tgl);
        if ($mytime->isSunday() || $mytime->isSaturday()) {
            return true;
        } else {
            return false;
        }
    }
    public function geubahtanggalbln($tgl)
    {
        return \Carbon\Carbon::create($tgl)->format('d ').$this->bulan(\Carbon\Carbon::create($tgl)->format('F')) .
            \Carbon\Carbon::now()->format(' Y');
    }
    private function bulan($bulan)
    {
        switch (substr($bulan, 0, 3)) {
            case "Jan":
                return "Januari";

            case "Feb":
                return "Februari";

            case "Mar":
                return "Maret";

            case "Apr":
                return "April";

            case "May":
                return "Mei";

            case "Jun":
                return "Juni";

            case "Jul":
                return "Juli";

            case "Aug":
                return "Agustus";

            case "Sep":
                return "September";

            case "Oct":
                return "Oktober";

            case "Nov":
                return "November";

            case "Des":
                return "Desember";

            default:
                return $bulan;
        }
    }
}
