<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/13/2019
 * Time: 12:41 PM
 */

namespace App\Http\Controllers\Admin\Master;


use App\Http\Controllers\Controller;
use App\Modules_admin\Modul_user_master;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class User_Master extends Controller
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
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $msiswa = new Modul_user_master();

                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $msiswa->cari_siswa($json->nama, $json->thn_lahir)
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

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
                        isset($json->nis)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $msiswa = new Modul_user_master();


                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $msiswa->get_profil_siswa($json->nis)
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

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
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $msiswa = new Modul_user_master();
                                    $msiswa->update_siswa($json->nis, $json->nisn, $json->nama_siswa, $json->tgl_lhr,
                                        $json->alamat, $json->tmpt_lhr, $json->agama, $json->orangtua, $json->no_ijazah,
                                        $json->no_ujiansmp);
                                    $result = [
                                        'code' => 'OK4'
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

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
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {

                                    $validasi = $user->getpass_lama($username, $json->p4ss);
                                    if ($validasi) {
                                        $msiswa = new Modul_user_master();
                                        $hasil = $msiswa->check_data_siswa($json->nis);
                                        if ($hasil) {
                                            $msiswa->hapus_data_siswa($json->nis);
                                            $result = [
                                                'code' => 'OK4'
                                            ];
                                        } else
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
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'ISI nama PARAM dikirim salah'];
                } else
                    $result = ['code' => 'format data yg dikirim salah '];

                return $result;
            }
        }

    }


    public function cari_staf(Request $request)
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
                        isset($json->nama)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $user_master = new Modul_user_master();

                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $user_master->cari_staf($json->nama)
                                    ];

                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'ISI nama PARAM dikirim salah'];
                } else
                    $result = ['code' => 'format data yg dikirim salah '];

                return $result;
            }
        }

    }

    public function data_staf(Request $request)
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
                        isset($json->nip)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $user_master = new Modul_user_master();
                                    $data = $user_master->get_profil_staf($json->nip);
                                    if ($data) {
                                        $result = [
                                            'code' => 'OK4',
                                            'data' => $data
                                        ];
                                    } else
                                        $result = ['code' => 'Data Staf tidak ditemukan'];


                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'ISI nama PARAM dikirim salah'];
                } else
                    $result = ['code' => 'format data yg dikirim salah '];

                return $result;
            }
        }

    }

    public function update_staf(Request $request)
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
                        isset($json->nip)&&isset($json->nama)&&isset($json->level)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                    $user_master = new Modul_user_master();
                                    $data = $user_master->get_profil_staf($json->nip);
                                    if ($data) {
                                        $user_master->update_staf($json->nip, $json->nama, $json->level);
                                        $result = [
                                            'code' => 'OK4'
                                        ];
                                    } else
                                        $result = ['code' => 'Data Staf tidak ditemukan'];


                                } else
                                    $result = ['code' => 'Akses Ditolak'];
                            } else
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'ISI nama PARAM dikirim salah'];
                } else
                    $result = ['code' => 'format data yg dikirim salah '];

                return $result;
            }
        }

    }



    public function hapus_staf(Request $request)
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
                        isset($json->nip) && isset($json->p4ss)) {
                        $token = $json->token;
                        $username = $json->x1d;
                        $type = $json->type;
                        $key = $json->key;

                        if ($token == $tool->generate_token($key, $username, $type)) {
                            if ($user->chek_token($username, $token, $type)) {
                                if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {

                                    $validasi = $user->getpass_lama($username, $json->p4ss);
                                    if ($validasi) {
                                        $msiswa = new Modul_user_master();
                                        $hasil = $msiswa->get_profil_staf($json->nip);
                                        if ($hasil) {
                                            $msiswa->hapus_data_Staf($json->nip);
                                            $result = [
                                                'code' => 'OK4'
                                            ];
                                        } else
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
                                $result = ['code' => 'TOKEN1'];

                        } else
                            $result = ['code' => 'TOKEN2'];

                    } else
                        $result = ['code' => 'ISI nama PARAM dikirim salah'];
                } else
                    $result = ['code' => 'format data yg dikirim salah '];

                return $result;
            }
        }

    }



}