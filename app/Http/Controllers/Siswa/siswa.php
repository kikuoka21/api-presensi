<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/12/2019
 * Time: 9:47 AM
 */

namespace App\Http\Controllers\Siswa;


use App\Http\Controllers\Controller;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class siswa extends Controller
{

    public function dashboard(Request $request)
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
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
//							$tanggal = $tool->get_date();
                            $tanggal = '2019-05-12';

                            if ($tool->tgl_merah()) {
                                $hari_ini = [
                                    'status' => 'L',
                                    'ket' => 'Tidak ada KBM'
                                ];
                            } else {
                                $hasil = $dashboard->harilibur($tanggal);
                                if (!$hasil) {
                                    $hari_ini = [
                                        'status' => 'M',
                                        'ket' => ''
                                    ];
                                } else {
                                    $hari_ini = [
                                        'status' => 'L',
                                        'ket' => object_get($hasil[0], 'ket')
                                    ];
                                }
                            }
                            $msiswa = new M_siswa();
                            $hasil_kelas = $msiswa->getKels($username, $tool->thn_ajar_skrng());

                            $result = [
                                'code' => 'OK4',
                                'kd_kelas' => object_get($hasil_kelas[0], 'kd_kels'),
                                'nm_kelas' => object_get($hasil_kelas[0], 'kelas'),
                                'hari_ini' => $hari_ini

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

    public function create_qr(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $msiswa = new M_siswa();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->kd_kls)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $kelas = $json->kd_kls;
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {

//							$tanggal = $tool->get_date();
                            $tanggal = '2019-05-21';
                            $hasil = $msiswa->get_flag_($kelas, $tanggal);
                            if (!$hasil) {
                                $getsiswa = $msiswa->get_all_siswa($kelas);
                                $token = '';
                                if ($getsiswa) {
                                    $token = md5('sudah d!enkrip' . md5($tanggal . $kelas) . $username);
                                    $msiswa->insert_token($kelas, $tanggal, $token);
                                    $code = 'OK4';
                                    for ($i = 0; $i < count($getsiswa); $i++) {
                                        $msiswa->create_absen(object_get($getsiswa[$i], 'nis'), $tanggal, $kelas);
                                    }

                                } else {
                                    $code = 'tidak ada data siswa di kelas ' . $kelas;
                                }


                            } else {
                                $code = 'OK4';
                                $token = object_get($hasil[0], 'token');
                            }

                            $result = [
                                'code' => $code,
                                'tokennya ' => $token
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