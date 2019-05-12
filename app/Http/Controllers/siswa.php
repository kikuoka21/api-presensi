<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/12/2019
 * Time: 9:47 AM
 */

namespace App\Http\Controllers;


use App\Modules\M_Dashboard;
use App\Modules\Tool;
use App\Modules\User;
use Illuminate\Http\Request;

class siswa
{

    public function dashboard(Request $request){
        $user = new User();
        $tool = new Tool();
        $dashboard = new M_Dashboard();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) || isset($json->x1d) || isset($json->type) || isset($json->key)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)){
                            $tanggal = $tool-> get_date();
//                            $tanggal = '2019-05-12';

                            if ($tool->tgl_merah()){
                                $hari_ini=[
                                    'status'=> 'L',
                                    'ket'=> 'Tidak ada KBM'
                                ];
                            }else{
                                $hasil = $dashboard->harilibur($tanggal);
                                if (!$hasil){
                                    $hari_ini=[
                                        'status'=> 'M'
                                    ];
                                }else{
                                    $hari_ini=[
                                        'status'=> 'L',
                                        'ket'=> object_get($hasil[0], 'ket')
                                    ];
                                }
                            }


                            $result = [
                                'code' => 'OK4',
                                'tahun_ajar' => $tool->thn_ajar_skrng(),
                                'tanggal'=> $tanggal,
                                'hari_ini'=>$hari_ini

                            ];

                        } else
                            $result = ['code' => 'token data base sudah berubah'];

                    } else
                        $result = ['code' => 'token beda'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }
}