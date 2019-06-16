<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/7/2019
 * Time: 2:09 AM
 */

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_presensi;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class presensi extends Controller
{

    public function get_presensi_harian(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $dashboard = new M_Dashboard();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->level)
                    && isset($json->kelas) && isset($json->tanggal)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $tanggal = $json->tanggal;

                    $key = $json->key;
                    if ($json->level == '1') {
                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                $madmin = new M_presensi();
                                $list = $madmin->getabsen_kelas_tanggal($tanggal, $json->kelas);


                                $result = [
                                    'code' => 'OK4',
                                    'list' => $list

                                ];

                            } else
                                $result = ['code' => 'token data base sudah berubah'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'Akses Ditolak'];
                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function get_presensi_perbulan(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $dashboard = new M_Dashboard();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->level)
                    && isset($json->kelas) && isset($json->tanggal)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $tanggal = $json->tanggal;

                    $key = $json->key;
                    if ($json->level == '1') {
                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                $madmin = new M_presensi();
                                $list = [];
                                $arrayke=0;
                                for ($i=0;$i<=30;$i = $i+1 ){
                                    $data = $madmin->getabsen_kelas_tanggal($tanggal.'-'.$this->tambahnol($i+1), $json->kelas);
                                    if ($data){
                                        $list[$arrayke]=[
                                            'tanggal'=>$tanggal.'-'.($i+1),
                                            'presensi'=> $data
                                        ];
                                        $arrayke++;
                                    }

                                }
                                $result = [
                                    'code' => 'OK4',
                                    'list' => $list

                                ];

                            } else
                                $result = ['code' => 'token data base sudah berubah'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'Akses Ditolak'];
                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    private function tambahnol($angka){
        if(strlen($angka)==1){
            $angka = '0'.$angka;
        }
        return $angka;
    }
}