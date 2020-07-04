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
    function key_server_fcm()
    {

        //alkamal
        return 'AAAAeC3s3Ck:APA91bHU6tFo-Jn4SKcaaClwqki1MRf-TceaTz5sckwVolpwgZIyTUJAuTHMJ8loB6hhnnSI3gbFvZVRjE8Ou15VcxhBFx1-PsYIs4APX7GMkdsmP5scT-N7RuB6gjkw0DA4J3fO8ZG7';

        //firebase teja
//        return 'AAAAFUOTLmA:APA91bFoZ69zCgB8KfiWwD3zxtN_njGELKJrPnHnIKM5Ad3JxohHpSTCqj1ky-IrLEi5RzMVLzOM-a-HlF4dNtWvbzqVcEzrzS44EC-BLXg8dnQ24YKny3x9yX2R1mhiJCk9DraEIsee';

    }



    public function call_FMC($fields){
        $ch = curl_init("https://fcm.googleapis.com/fcm/send");
        $header=array('Content-Type: application/json',
            "Authorization: key=".$this->key_server_fcm());
        curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt( $ch,CURLOPT_SSL_VERIFYPEER, false );

        curl_setopt($ch, CURLOPT_POST, 0);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($fields));

        $result =   curl_exec($ch);
        curl_close($ch);
        return $result;
    }
    function IsJsonString($str)
    {
        json_decode($str);
        return (json_last_error() == JSON_ERROR_NONE);
    }

    public function generate_token($key, $id, $type)
    {
        return md5(md5('%!' . $id . $key) . $type . '!%');
    }


    public function is_parent($json)
    {
        $hasil = false;

        if (isset($json->parent)) {


            if ($json->parent)
                $hasil = true;


        }
        return $hasil;
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
        return \Carbon\Carbon::create($tgl)->format('d ') . $this->bulan(\Carbon\Carbon::create($tgl)->format('F')) .
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
