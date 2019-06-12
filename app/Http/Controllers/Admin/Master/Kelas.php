<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 1:31 PM
 */

namespace App\Http\Controllers\Admin\master;

use App\Http\Controllers\Controller;
use App\Modules_admin\Input_masterr;
use App\Modules_admin\Modul_Kelas;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class kelas extends Controller
{
    public function list_siswa(Request $request)
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
                    isset($json->thn) && isset($json->thn_lahir)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Modul_Kelas();
                                $hasil = $inputmaster->list_siswa($json->thn, $json->thn_lahir);

                                $result = [
                                    'code' => 'OK4',
                                    'data' => $hasil
                                ];

                            } else
                                $result = ['code' => 'Akses Ditolak'];

                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'Isi nama PARAM dikirim salah'];

            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function tambah_siswa_kelas(Request $request)
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
                    isset($json->id_kelas) && isset($json->nis)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Modul_Kelas();
                                $hasil = $inputmaster->check_siswa_kelas($json->id_kelas, $json->nis);
                                if (!$hasil) {
                                    $inputmaster->input_siswa_kelas($json->id_kelas, $json->nis);
                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else {
                                    $result = [
                                        'code' => 'NIS Tersebut Sudah Dimasukan Ke Dalam Kelas'
                                    ];
                                }


                            } else
                                $result = ['code' => 'Akses Ditolak'];

                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'Isi nama PARAM dikirim salah'];

            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function hapus_siswa(Request $request)
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
                    isset($json->id_kelas) && isset($json->nis)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Modul_Kelas();
                                $hasil = $inputmaster->check_siswa_kelas($json->id_kelas, $json->nis);
                                if ($hasil) {
                                    $inputmaster->hapus_siswa_kelas($json->id_kelas, $json->nis);
                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else
                                    $result = [
                                        'code' => 'NIS Tersebut Sudah tidak Dalam kelas'
                                    ];

                            } else
                                $result = ['code' => 'Akses Ditolak'];

                        } else
                            $result = ['code' => 'token tidak falid'];

                    } else
                        $result = ['code' => 'token salah'];

                } else
                    $result = ['code' => 'Isi nama PARAM dikirim salah'];

            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

}