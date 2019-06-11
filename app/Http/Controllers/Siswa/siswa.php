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
                            $tanggal = $tool->get_date();
//                            $tanggal = '2019-05-11';


                            $hasil = $dashboard->harilibur($tanggal);
                            if (!$hasil) {
                                if ($tool->tgl_merah()) {
                                    $hari_ini = [
                                        'status' => 'L',
                                        'ket' => 'Tidak ada KBM'
                                    ];
                                } else {
                                    $hari_ini = [
                                        'status' => 'M',
                                        'ket' => ''
                                    ];
                                }
                            } else {
                                $hari_ini = [
                                    'status' => 'L',
                                    'ket' => object_get($hasil[0], 'ket')
                                ];
                            }

                            $msiswa = new M_siswa();
                            $hasil_kelas = $msiswa->getKels($username, $tool->thn_ajar_skrng());
                            if ($hasil_kelas) {
                                $id_kelas = object_get($hasil_kelas[0], 'kd_kels');
                                $nama_kelas = object_get($hasil_kelas[0], 'kelas');
                            } else {
                                $id_kelas = '-';
                                $nama_kelas = 'Belum Diinput';
                            }

                            $result = [
                                'code' => 'OK4',
                                'kd_kelas' => $id_kelas,
                                'nm_kelas' => $nama_kelas,
                                'hari_ini' => $hari_ini

                            ];

                        } else
                            $result = ['code' => 'token data base sudah berubah'];

                    } else {
                        $result = ['code' => $json];
                    }

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
        $dashboard = new M_Dashboard();

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
                            if ($tool->tgl_merah()) {
                                $result = [
                                    'code' => 'tidak ada KBM'
                                ];
                            } else {
                                $tanggal = $tool->get_date();
//                            $tanggal = '2019-05-21';
                                $hasil = $dashboard->harilibur($tanggal);
                                if (!$hasil) {
                                    $hasil = $msiswa->get_flag_($kelas, $tanggal);
                                    if (!$hasil) {
                                        $getsiswa = $msiswa->get_all_siswa($kelas);
                                        $token = '';
                                        if ($getsiswa) {
                                            $token = md5('sudah d!enkrip' . md5($tanggal .'\''. $kelas) .'!'. $username);
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
                                        'tanggal' => $tanggal,
                                        'tokennya' => $token
                                    ];
                                } else {
                                    $result = [
                                        'code' => 'tidak ada KBM'
                                    ];
                                }
                            }


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

    public function isi_absen(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                    isset($json->kd_kls) && isset($json->token_absen) && isset($json->tanggal)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $kelas = $json->kd_kls;
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {

                            $msiswa = new M_siswa();
                            $tanggal = $json->tanggal;

                            $tokenkelas = $msiswa->get_flag_($kelas, $tanggal);
                            if (object_get($tokenkelas[0], 'token') == $json->token_absen) {

                                $hasil = $msiswa->check_absen($username, $tanggal, $kelas);
                                if ($hasil) {
                                    $flag = object_get($hasil[0], 'stat');

                                    if ($flag == 'A') {
                                        $msiswa->update_absen($username, $tanggal, $kelas, "H", "");
                                        $code = 'OK4';
                                    } else {
                                        if ($flag == 'H')
                                            $pesan = 'Hadir';
                                        elseif ($flag == 'I')
                                            $pesan = 'Izin';
                                        else
                                            $pesan = 'Sakit';

                                        $code = 'anda sudah melakukan Presensi pada tanggal ' . $tanggal . ' dengan status ' . $pesan;
                                    }

                                } else {
                                    $code = 'eror pada pencarian kehadiran';

                                }

                                $result = [
                                    'code' => $code,
                                    'tanggal' => $tanggal
                                ];
                            } else {
                                $result = [
                                    'code' => 'token kelas salah'
                                ];
                            }

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

    public function profil(Request $request)
    {
        $user = new User();
        $tool = new Tool();

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

                            $msiswa = new M_siswa();
                            $profil = $msiswa->getprofil($json->x1d);
                            $kelas = $msiswa->history_kelas($json->x1d);

                            if ($profil) {
                                if (!$kelas) {
                                    $kelas = [];
                                }
                                $data = [
                                    'profil' => $profil[0],
                                    'kelas' => $kelas
                                ];

                                $result = [
                                    'code' => 'OK4',
                                    'data' => $data
                                ];
                            } else {
                                $result = [
                                    'code' => 'Data Tidak Ditemukan'
                                ];
                            }

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