<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 1:31 PM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules_admin\Input_masterr;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class Input_data_master extends Controller
{
    public function input_siswa(Request $request)
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
                    isset($json->nis) && isset($json->nisn) && isset($json->nama_siswa) && isset($json->tgl_lhr) && isset($json->alamat)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            $inputmaster = new Input_masterr();


                            $hasil = $inputmaster->check_data_siswa($json->nis);

                            if (!$hasil) {
                                // jika tidak d temukan nis tersebut
                                $aaa = 'asdwa';
                                $inputmaster->input_siswa($json->nis, $json->nisn, $json->nama_siswa, $json->tgl_lhr, $json->alamat);

                                $inputmaster->input_users($json->nis, 'd1fdc1c3d4fcaf10e212d10a896ee927', '0');

                                $result = [
                                    'code' => 'OK4'
                                ];
                            } else {
                                $result = ['code' => 'nis yang dimasukan sudah ada'];
                            }
                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function input_staf(Request $request)
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
                    isset($json->nip) && isset($json->nama_staf) && isset($json->level)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            $inputmaster = new Input_masterr();


                            $hasil = $inputmaster->check_data_staff($json->nip);

                            if (!$hasil) {
//                                // jika tidak d temukan nip tersebut
                                $inputmaster->input_staff($json->nip, $json->nama_staf, $json->level);

                                $inputmaster->input_users($json->nip, 'd1fdc1c3d4fcaf10e212d10a896ee927', '1');

                                $result = [
                                    'code' => 'OK4'
                                ];
                            } else {
                                $result = ['code' => 'nip yang dimasukan sudah ada'];
                            }
                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function input_tanggal(Request $request)
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
                    isset($json->tanggal) && isset($json->ket)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            $inputmaster = new Input_masterr();


                            $hasil = $inputmaster->check_data_libur($json->tanggal);

                            if (!$hasil) {
                                $inputmaster->input_libur($json->tanggal, $json->ket);

                                $result = ['code' => 'OK4'];
                            } else {
                                $result = ['code' => 'Tanggal yang anda masukan sudah ada'];
                            }
                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function input_kelas(Request $request)
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
                    isset($json->nama_kls) && isset($json->thn_ajar)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            $inputmaster = new Input_masterr();
                            $hasil = $inputmaster->check_data_kelas($json->nama_kls, $json->thn_ajar);

                            if (!$hasil) {
                                $hasil = $inputmaster->generate_id_kelas();
//                                $idkelas = substr(object_get($hasil[0], 'data'), 1);
                                $inputmaster->input_kelas($hasil, $json->nama_kls, $json->thn_ajar);
                                $result = [
                                    'code' => 'OK4'
                                ];
                            } else {
                                $result = ['code' => 'Data yang diinput sudah ada'];
                            }
                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];
            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }


}