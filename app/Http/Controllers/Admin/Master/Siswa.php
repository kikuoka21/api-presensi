<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/13/2019
 * Time: 12:41 PM
 */

namespace App\Http\Controllers\Admin\Master;


use App\Http\Controllers\Controller;
use App\Modules_admin\Modul_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class Siswa extends Controller
{
    public function cari_siswa(Request $request)
    {
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
                        isset($json->nama) && isset($json->thn_lahir)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username)) {
                                    $msiswa = new Modul_siswa();

                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $msiswa->cari_siswa($json->nama, $json->thn_lahir)
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
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

    public function data_siswa(Request $request)
    {
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
                        isset($json->nis) ) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username)) {
                                    $msiswa = new Modul_siswa();


                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $msiswa->get_profil_siswa($json->nis)
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
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

    public function ubah_siswa(Request $request)
    {
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
                        isset($json->nis) && isset($json->nisn) && isset($json->nama_siswa) && isset($json->tmpt_lhr) &&
                        isset($json->tgl_lhr) && isset($json->agama) && isset($json->orangtua) && isset($json->alamat)
                        && isset($json->no_ijazah) && isset($json->no_ujiansmp)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username)) {
                                    $msiswa = new Modul_siswa();
                                    $msiswa->update_siswa($json->nis, $json->nisn, $json->nama_siswa, $json->tgl_lhr,
                                        $json->alamat, $json->tmpt_lhr, $json->agama, $json->orangtua, $json->no_ijazah,
                                        $json->no_ujiansmp);
                                    $result = [
                                        'code' => 'OK4'
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
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

    public function hapus_siswa(Request $request)
    {
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
                        isset($json->nis) && isset($json->p4ss)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username)) {

                                    $validasi = $user->getpass_lama($username, $json->p4ss);
                                    if ($validasi) {
                                        $msiswa = new Modul_siswa();
                                        $hasil = $msiswa->check_data_siswa($json->nis);
                                        if ($hasil){
                                            $msiswa->hapus_data_siswa($json->nis);
                                            $result = [
                                                'code' => 'OK4'
                                            ];
                                        }else
                                            $result = [
                                                'code' => 'Data tidak ditemukan'
                                            ];
                                    } else
                                        $result = [
                                            'code' => 'Pass yang dimasukan salah'
                                        ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
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
}