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
                                $inputmaster = new Input_masterr();


                                $hasil = $inputmaster->check_data_siswa($json->nis);

                                if (!$hasil) {
                                    // jika tidak d temukan nis tersebut
                                    $inputmaster->input_siswa($json->nis, $json->nisn, $json->nama_siswa, $json->tgl_lhr,
                                        $json->alamat, $json->tmpt_lhr, $json->agama, $json->orangtua, $json->no_ijazah,
                                        $json->no_ujiansmp);


                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else
                                    $result = ['code' => 'nis yang dimasukan sudah ada dengan nama ' . object_get($hasil[0], 'nama')];

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
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Input_masterr();


                                $hasil = $inputmaster->check_data_staff($json->nip);

                                if (!$hasil) {
//                                // jika tidak d temukan nip tersebut
                                    $inputmaster->input_staff($json->nip, $json->nama_staf, $json->level);

                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else
                                    $result = ['code' => 'nip yang dimasukan sudah ada'];

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
//                            if ($user->getakses_admin($username) ) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {

                                $inputmaster = new Input_masterr();


                                $hasil = $inputmaster->check_data_libur($json->tanggal);

                                if (!$hasil) {
                                    $inputmaster->input_libur($json->tanggal, $json->ket);

                                    $result = ['code' => 'OK4'];
                                } else
                                    $result = ['code' => 'Tanggal yang anda masukan sudah ada dengan data ' . object_get($hasil[0], 'tgl')
                                        . ', ' . object_get($hasil[0], 'ket')];
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

    public function lihat_tanggal(Request $request)
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
                    isset($json->tanggal)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username)) {

                                $inputmaster = new Input_masterr();
                                $hasil = $inputmaster->history_tanggal($json->tanggal);
//                                $hasil = $inputmaster->history_tanggal('2019-05-11');

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
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

    public function history_tanggal(Request $request)
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
                    isset($json->tanggal)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username)) {

                                $inputmaster = new Input_masterr();
                                $hasil = $inputmaster->history_tanggal2($json->tanggal);
//                                $hasil = $inputmaster->history_tanggal2('2019-05-10');

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
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Input_masterr();
                                $hasil = $inputmaster->check_data_kelas($json->nama_kls, $json->thn_ajar);

                                if (!$hasil) {
                                    $hasil = $inputmaster->generate_id_kelas();
//                                $idkelas = substr(object_get($hasil[0], 'data'), 1);
                                    $inputmaster->input_kelas($hasil, $json->nama_kls, $json->thn_ajar);
                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else
                                    $result = ['code' => 'Data yang diinput sudah ada'];

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

    public function all_kelas(Request $request)
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
                    isset($json->thn_ajar)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Input_masterr();
                                $hasil = $inputmaster->all_kelas($json->thn_ajar);
                                if ($hasil) {
                                    $balikan = [];
                                    for ($i = 0; $i < count($hasil); $i++) {
                                        $siswa = [
                                            'id'=> '-',
                                            'nama'=>'-'
                                        ];
                                        $staf = [
                                            'id'=> '-',
                                            'nama'=>'-'
                                        ];
                                        if (object_get($hasil[$i], 'id_ketua_kelas') != "") {
                                            $siswa = [
                                                'id'=> object_get($hasil[$i], 'id_ketua_kelas'),
                                                'nama'=>$inputmaster->get_nama_siswa(object_get($hasil[$i], 'id_ketua_kelas'))
                                            ];

                                        }
                                        if (object_get($hasil[$i], 'id_wali_kelas') != "") {
                                            $staf = [
                                                'id'=> object_get($hasil[$i], 'id_wali_kelas'),
                                                'nama'=>$inputmaster->get_nama_wali(object_get($hasil[$i], 'id_wali_kelas'))
                                            ];
                                        }

                                        $balikan[$i] = [
                                            'id' => object_get($hasil[$i], 'id_kelas'),
                                            'nama' => object_get($hasil[$i], 'nama'),
                                            'siswa' => $siswa,
                                            'walikelas' => $staf
                                        ];
                                    }
                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $balikan
                                    ];
                                } else {
                                    $result = [
                                        'code' => 'Tidak Ditemukan Kelas Pada Tahun Ajaran ' .
                                            substr($json->thn_ajar, 0, 4) . '/' . substr($json->thn_ajar, 4)
                                    ];
                                }

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
    public function isi_kelas(Request $request)
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
                    isset($json->thn_ajar)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
                                $inputmaster = new Input_masterr();
                                $hasil = $inputmaster->all_kelas($json->thn_ajar);
                                if ($hasil) {
                                    $balikan = [];
                                    for ($i = 0; $i < count($hasil); $i++) {
                                        $siswa = '-';
                                        $staf = '-';
                                        if (object_get($hasil[$i], 'id_ketua_kelas') != "") {
                                            $siswa = $inputmaster->get_nama_siswa(object_get($hasil[$i], 'id_ketua_kelas'));
                                        }
                                        if (object_get($hasil[$i], 'id_wali_kelas') != "") {
                                            $staf = $inputmaster->get_nama_wali(object_get($hasil[$i], 'id_wali_kelas'));
                                        }

                                        $balikan[$i] = [
                                            'id' => object_get($hasil[$i], 'id_kelas'),
                                            'nama' => object_get($hasil[$i], 'nama'),
                                            'siswa' => $siswa,
                                            'walikelas' => $staf
                                        ];
                                    }
                                    $result = [
                                        'code' => 'OK4',
                                        'data' => $balikan
                                    ];
                                } else {
                                    $result = [
                                        'code' => 'Tidak Ditemukan Kelas Pada Tahun Ajaran ' .
                                            substr($json->thn_ajar, 0, 4) . '/' . substr($json->thn_ajar, 4)
                                    ];
                                }

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