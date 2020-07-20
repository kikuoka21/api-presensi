<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 10:22 AM
 */

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Modules_admin\Input_masterr;
use App\Modules_admin\M_admin;
use App\Modules_admin\Modul_Kelas;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_presensi;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class admin extends Controller
{

    public function dashboard(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $dashboard = new M_Dashboard();

        $json = $request->input('parsing');
        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    //$retur = [
                    //'status' => 'aa',
                    //];
                    //return $retur;
                    if ($user->chek_token($username, $token, $type)) {
                        //$username = '760695';

                        if ($user->getakses_admin($username)) {
                            $tanggal = $tool->get_date();
//                                $tanggal = '2020-04-08';
                            $thn_ajar = $tool->thn_ajar_skrng();
                            if (isset($json->token_firebase)) {
                                $user->update_firebase_user($username, $json->token_firebase);
                            }

                            $list = [];
                            $statistik = [
                                "hadir" => 0,
                                "alpha" => 0,
                                "izin" => 0,
                                "sakit" => 0,
                                "telat" => 0,
                                "list_kelas" => []

                            ];
                            $presen = new M_presensi();
                            $msiswa = new M_siswa();
                            $hasil = $dashboard->harilibur($tanggal);
                            if (!$hasil) {
                                if ($tool->tgl_merah()) {
//                                    if (false) {
                                    $hari_ini = [
                                        'status' => 'L',
                                        'ket' => 'Tidak ada Kegiatan Belajar Mengajar'
                                    ];

                                } else {
                                    $hari_ini = [
                                        'status' => 'M',
                                        'ket' => $tanggal
                                    ];

                                    $madmin = new M_admin();
//                                        if ($user->getakses_admin_piket($username)) {
                                    $kelas = $madmin->all_kelas($thn_ajar);
                                    $list_kelas = [];
                                    for ($i = 0; $i < count($kelas); $i++) {
                                        $siswa = $madmin->get_siswakelas($kelas[$i]->id_kelas);
                                        $banyak_alphanya = 0;
                                        for ($j = 0; $j < count($siswa); $j++) {

                                            $absennya = $presen->getabsen_siswa2($tanggal, $siswa[$j]->nis);
                                            if (!$absennya) {
                                                $msiswa->create_absen($siswa[$j]->nis, $tanggal);
                                                $banyak_alphanya++;
                                            } else {

                                                if ($absennya[0]->stat == 'A') {

                                                    $banyak_alphanya++;
                                                }

                                            }
                                        }

                                        $statistik_kelas = [
                                            "kelas" => $kelas[$i]->nama_kelas,
                                            "alpha" => $banyak_alphanya,
                                        ];
                                        $list_kelas[$i] = $statistik_kelas;
                                    }
                                    $row_sakit = $madmin->getabsen_all2($tanggal, $thn_ajar, 'S');
                                    $row_izin = $madmin->getabsen_all2($tanggal, $thn_ajar, 'I');
                                    $row_hadir = $madmin->getabsen_all2($tanggal, $thn_ajar, 'H');
//                                    $row_telat = $madmin->getabsen_all2($tanggal, $thn_ajar, 'T');
                                    $list = $madmin->getabsen_all2($tanggal, $thn_ajar, 'A');
                                    $statistik = [
                                        "hadir" => count($row_hadir),
                                        "alpha" => count($list),
                                        "izin" => count($row_izin),
                                        "sakit" => count($row_sakit),
                                        "telat" => 0,
//                                                "list_kelas" => $kelas,
                                        "list_kelas" => $list_kelas,
                                    ];

//                                        } else {
//                                            $siswa = $madmin->getsiswa($username, $thn_ajar);
//                                            $nama = $madmin->namakelas($username, $thn_ajar);
//                                            $row_sakit = 0;
//                                            $row_izin = 0;
//                                            $row_hadir = 0;
//                                            $row_telat = 0;
//                                            $row_alpha = 0;
//                                            for ($i = 0; $i < count($siswa); $i++) {
//
//                                                $absen = $presen->getabsen_siswa2($tanggal, $siswa[$i]->nis);
//                                                if (!$absen) {
//                                                    $presensi = [
//                                                        "nis" => $siswa[$i]->nis,
//                                                        "nama" => $dashboard->getnama_siswa($siswa[$i]->nis),
//                                                        "kelas" => object_get($nama[0], 'nama_kelas'),
//                                                        "stat" => "A",
//                                                        "ket" => "Belum Melakukan Presensi Masuk"];
//                                                    $msiswa->create_absen($siswa[$i]->nis, $tanggal);
//                                                    $list[$row_alpha] = $presensi;
//                                                    $row_alpha++;
//                                                } else {
//
//                                                    if ($absen[0]->stat == 'A') {
//                                                        $presensi = [
//                                                            "nis" => $siswa[$i]->nis,
//                                                            "nama" => $dashboard->getnama_siswa($siswa[$i]->nis),
//                                                            "kelas" => object_get($nama[0], 'nama_kelas'),
//                                                            "stat" => $absen[0]->stat,
//                                                            "ket" => $absen[0]->ket];
//                                                        $list[$row_alpha] = $presensi;
//                                                        $row_alpha++;
//                                                    }
//
//                                                    if ($absen[0]->stat == 'T') {
//                                                        $row_telat++;
//                                                    }
//
//                                                    if ($absen[0]->stat == 'H') {
//                                                        $row_hadir++;
//                                                    }
//
//                                                    if ($absen[0]->stat == 'S') {
//                                                        $row_sakit++;
//                                                    }
//
//                                                    if ($absen[0]->stat == 'I') {
//                                                        $row_izin++;
//                                                    }
//
//                                                }
//
//                                            }
//
//                                            $statistik = [
//                                                "hadir" => $row_hadir,
//                                                "alpha" => $row_alpha,
//                                                "izin" => $row_izin,
//                                                "sakit" => $row_sakit,
//                                                "telat" => $row_telat,
//                                                "list_kelas" => []
//                                            ];
//
//                                        }
                                }

                            } else {
                                $hari_ini = [
                                    'status' => 'L',
                                    'ket' => object_get($hasil[0], 'ket')
                                ];

                            }

                            $result = [
                                'code' => 'OK4',
                                'date' => $hari_ini,
                                'statistik' => $statistik,
//                                    'kd_kelas' => $thn_ajar,
//                                    'nm_kelas' => $tanggal,
                                'nm_kelas' => isset($json->token_firebase),
                                'list_absen' => $list

                            ];
                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function dashboard_tgl(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $dashboard = new M_Dashboard();

        $json = $request->input('parsing');
        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->tgl)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $tanggal = $json->tgl;
                if ($user->chek_token($username, $token, $type)) {
                    //$username = '760695';

                    if ($user->getakses_admin($username)) {
//                                $tanggal = '2020-04-08';
                        $thn_ajar = $tool->thn_ajar_pertanggal($tanggal);
                        if (isset($json->token_firebase)) {
                            $user->update_firebase_user($username, $json->token_firebase);
                        }

                        $list = [];
                        $statistik = [
                            "hadir" => 0,
                            "alpha" => 0,
                            "izin" => 0,
                            "sakit" => 0,
                            "telat" => 0,
                            "list_kelas" => []

                        ];
                        $presen = new M_presensi();
                        $msiswa = new M_siswa();
                        $hasil = $dashboard->harilibur($tanggal);
                        if (!$hasil) {
                            if ($tool->convert_tgl_merah($tanggal)) {
//                                    if (false) {
                                $hari_ini = [
                                    'status' => 'L',
                                    'ket' => 'Tidak ada Kegiatan Belajar Mengajar'
                                ];

                            } else {
                                $hari_ini = [
                                    'status' => 'M',
                                    'ket' => $tanggal
                                ];

                                $madmin = new M_admin();
//                                    if ($user->getakses_admin_piket($username)) {
                                $kelas = $madmin->all_kelas($thn_ajar);
                                $list_kelas = [];
                                for ($i = 0; $i < count($kelas); $i++) {
                                    $siswa = $madmin->get_siswakelas($kelas[$i]->id_kelas);
                                    $banyak_alphanya = 0;
                                    for ($j = 0; $j < count($siswa); $j++) {

                                        $absennya = $presen->getabsen_siswa2($tanggal, $siswa[$j]->nis);
                                        if (!$absennya) {
                                            $msiswa->create_absen($siswa[$j]->nis, $tanggal);
                                            $banyak_alphanya++;
                                        } else {

                                            if ($absennya[0]->stat == 'A') {

                                                $banyak_alphanya++;
                                            }

                                        }
                                    }

                                    $statistik_kelas = [
                                        "kelas" => $kelas[$i]->nama_kelas,
                                        "alpha" => $banyak_alphanya,
                                    ];
                                    $list_kelas[$i] = $statistik_kelas;
                                }
                                $row_sakit = $madmin->getabsen_all2($tanggal, $thn_ajar, 'S');
                                $row_izin = $madmin->getabsen_all2($tanggal, $thn_ajar, 'I');
                                $row_hadir = $madmin->getabsen_all2($tanggal, $thn_ajar, 'H');
//                                $row_telat = $madmin->getabsen_all2($tanggal, $thn_ajar, 'T');
                                $list = $madmin->getabsen_all2($tanggal, $thn_ajar, 'A');
                                $statistik = [
                                    "hadir" => count($row_hadir),
                                    "alpha" => count($list),
                                    "izin" => count($row_izin),
                                    "sakit" => count($row_sakit),
                                    "telat" => 0,
                                    "list_kelas" => $list_kelas,
                                ];

//                                    } else {
//                                        $siswa = $madmin->getsiswa($username, $thn_ajar);
//                                        $nama = $madmin->namakelas($username, $thn_ajar);
//                                        $row_sakit = 0;
//                                        $row_izin = 0;
//                                        $row_hadir = 0;
//                                        $row_telat = 0;
//                                        $row_alpha = 0;
//                                        for ($i = 0; $i < count($siswa); $i++) {
//
//                                            $absen = $presen->getabsen_siswa2($tanggal, $siswa[$i]->nis);
//                                            if (!$absen) {
//                                                $presensi = [
//                                                    "nis" => $siswa[$i]->nis,
//                                                    "nama" => $dashboard->getnama_siswa($siswa[$i]->nis),
//                                                    "kelas" => object_get($nama[0], 'nama_kelas'),
//                                                    "stat" => "A",
//                                                    "ket" => "Belum Melakukan Presensi Masuk"];
//                                                $msiswa->create_absen($siswa[$i]->nis, $tanggal);
//                                                $list[$row_alpha] = $presensi;
//                                                $row_alpha++;
//                                            } else {
//
//                                                if ($absen[0]->stat == 'A') {
//                                                    $presensi = [
//                                                        "nis" => $siswa[$i]->nis,
//                                                        "nama" => $dashboard->getnama_siswa($siswa[$i]->nis),
//                                                        "kelas" => object_get($nama[0], 'nama_kelas'),
//                                                        "stat" => $absen[0]->stat,
//                                                        "ket" => $absen[0]->ket];
//                                                    $list[$row_alpha] = $presensi;
//                                                    $row_alpha++;
//                                                }
//
//                                                if ($absen[0]->stat == 'T') {
//                                                    $row_telat++;
//                                                }
//
//                                                if ($absen[0]->stat == 'H') {
//                                                    $row_hadir++;
//                                                }
//
//                                                if ($absen[0]->stat == 'S') {
//                                                    $row_sakit++;
//                                                }
//
//                                                if ($absen[0]->stat == 'I') {
//                                                    $row_izin++;
//                                                }
//
//                                            }
//
//                                        }
//
//                                        $statistik = [
//                                            "hadir" => $row_hadir,
//                                            "alpha" => $row_alpha,
//                                            "izin" => $row_izin,
//                                            "sakit" => $row_sakit,
//                                            "telat" => $row_telat,
//                                            "list_kelas" => []
//                                        ];
//
//                                    }
                            }

                        } else {
                            $hari_ini = [
                                'status' => 'L',
                                'ket' => object_get($hasil[0], 'ket')
                            ];

                        }

                        $result = [
                            'code' => 'OK4',
                            'code2' => $tanggal,
                            'date' => $hari_ini,
                            'statistik' => $statistik,
                            'list_absen' => $list

                        ];
                    } else
                        $result = ['code' => 'Akses Ditolak'];

                } else
                    $result = ['code' => 'TOKEN1'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function list_kelas(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->tgl)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
                        if ($user->getakses_admin($username)) {
                            $inputmaster = new Input_masterr();
                            $hasil = $inputmaster->all_kelas($tool->thn_ajar_pertanggal($json->tgl));
                            if ($hasil) {
                                $balikan = [];
                                for ($i = 0; $i < count($hasil); $i++) {
                                    $siswa = '-';
                                    $staf = '-';
                                    if (object_get($hasil[$i], 'id_ketua_kelas') != "") {
                                        $siswa = '(' . object_get($hasil[$i], 'id_ketua_kelas') . ') ' .
                                            $inputmaster->get_nama_siswa(object_get($hasil[$i], 'id_ketua_kelas'));

                                    }
                                    if (object_get($hasil[$i], 'id_wali_kelas') != "") {
                                        $staf = '(' . object_get($hasil[$i], 'id_wali_kelas') . ') ' .
                                            $inputmaster->get_nama_wali(object_get($hasil[$i], 'id_wali_kelas'));
                                    }

                                    $balikan[$i] = [
                                        'id' => object_get($hasil[$i], 'id_kelas'),
                                        'nama_kelas' => object_get($hasil[$i], 'nama'),
                                        'wali' => $staf,
                                        'ketua' => $siswa
                                    ];
                                }
                                $result = [
                                    'code' => 'OK4',
                                    'data' => $balikan
                                ];
                            } else {
                                $thn = $tool->thn_ajar_pertanggal($json->tgl);
                                $result = [
                                    'code' => 'Tidak Ditemukan Kelas Pada Tahun Ajaran ' .
                                        substr($thn, 0, 4) . '/' . substr($thn, 4)
                                ];
                            }
                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function list_kelasthn_ini(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                if ($user->chek_token($username, $token, $type)) {
                    if ($user->getakses_admin($username)) {
                        $inputmaster = new Input_masterr();
                        $hasil = $inputmaster->all_kelas($tool->thn_ajar_skrng());
                        if ($hasil) {
                            $balikan = [];
                            for ($i = 0; $i < count($hasil); $i++) {
                                $siswa = '-';
                                $staf = '-';
                                if (object_get($hasil[$i], 'id_ketua_kelas') != "") {
                                    $siswa = '(' . object_get($hasil[$i], 'id_ketua_kelas') . ') ' .
                                        $inputmaster->get_nama_siswa(object_get($hasil[$i], 'id_ketua_kelas'));

                                }
                                if (object_get($hasil[$i], 'id_wali_kelas') != "") {
                                    $staf = '(' . object_get($hasil[$i], 'id_wali_kelas') . ') ' .
                                        $inputmaster->get_nama_wali(object_get($hasil[$i], 'id_wali_kelas'));
                                }

                                $balikan[$i] = [
                                    'id' => object_get($hasil[$i], 'id_kelas'),
                                    'nama_kelas' => object_get($hasil[$i], 'nama'),
                                    'wali' => $staf,
                                    'ketua' => $siswa
                                ];
                            }
                            $result = [
                                'code' => 'OK4',
                                'data' => $balikan
                            ];
                        } else {
                            $thn = $tool->thn_ajar_skrng();
                            $result = [
                                'code' => 'Tidak Ditemukan Kelas Pada Tahun Ajaran ' .
                                    substr($thn, 0, 4) . '/' . substr($thn, 4)
                            ];
                        }
                    } else
                        $result = ['code' => 'Akses Ditolak'];

                } else
                    $result = ['code' => 'TOKEN1'];
            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function gen_qr(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->kd_kls)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                if ($user->chek_token($username, $token, $type)) {
                    if ($user->getakses_admin($username)) {
                        $kelas = $json->kd_kls;
                        if ($tool->tgl_merah()) {
//                            if (false) {
                            $result = [
                                'code' => 'tidak ada Kegiatan Belajar Mengajar'
                            ];
                        } else {
                            $dashboard = new M_Dashboard();
                            $msiswa = new M_siswa();
                            $tanggal = $tool->get_date();
                            $hasil = $dashboard->harilibur($tanggal);
                            if (!$hasil) {
                                $hasil = $msiswa->get_flag_2($kelas, $tanggal);
                                $token = '';
                                if (!$hasil) {
                                    $getsiswa = $msiswa->get_all_siswa($kelas);
                                    if ($getsiswa) {
                                        $token = md5('sudah d!enkrip' . md5($tanggal . '\'' . $kelas) . '!' . $username);
                                        $msiswa->insert_token($kelas, $tanggal, $token);
                                        $code = 'OK4';
                                        for ($i = 0; $i < count($getsiswa); $i++) {
                                            $check = $msiswa->check_absen(object_get($getsiswa[$i], 'nis'), $tanggal);
                                            if (!$check)
                                                $msiswa->create_absen(object_get($getsiswa[$i], 'nis'), $tanggal);
                                        }

                                    } else {
                                        $code = 'tidak ada data siswa di kelas ' . $kelas;
                                    }
                                } else {
                                    $code = 'OK4';
                                    $token = $hasil->token;
                                }

                                $result = [
                                    'code' => $code,
                                    'code2' => $hasil,
                                    'tanggal' => $tanggal,
                                    'tokennya' => $token
                                ];
                            } else {
                                $result = [
                                    'code' => 'tidak ada Kegiatan Belajar Mengajar2'
                                ];
                            }
                        }
                    } else
                        $result = ['code' => 'Akses Ditolak'];

                } else
                    $result = ['code' => 'TOKEN1'];
            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;
    }

    public function get_absen_kelas(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                isset($json->tgl) && isset($json->id_kelas)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
                        if ($user->getakses_admin($username)) {

                            if ($tool->batasan_tglskrng($json->tgl)) {
                                $dashboard = new M_Dashboard();
                                $kelas = $dashboard->get_data_kelas($json->id_kelas);
                                if ($kelas) {
                                    if ($tool->thn_ajar_pertanggal($json->tgl) == $kelas->tahun_ajar) {
                                        $madmin = new M_presensi();

                                        $arraysiswa = $madmin->getabsen_kelas_siswa($json->id_kelas);
                                        if ($arraysiswa) {
                                            $inputmaster = new Modul_Kelas();
                                            $ketua = $inputmaster->get_ketua_kelas($json->id_kelas);
                                            $wali = $inputmaster->get_wali_kelas($json->id_kelas);

                                            $namasiswa = '-';
                                            $namawali = '-';
                                            if ($ketua) {
                                                $namasiswa = object_get($ketua[0], 'nama');

                                            }
                                            if ($wali) {
                                                $namawali = object_get($wali[0], 'nama');
                                            }
                                            $datakls = [
                                                'nama' => $kelas->nama,
                                                'ketua' => $namasiswa,
                                                'thn_ajar' => $kelas->tahun_ajar,
                                                'wali' => $namawali
                                            ];

                                            $data = [];
                                            for ($i = 0; $i < count($arraysiswa); $i++) {
                                                $tanggal = date_create($json->tgl);
                                                $absen = $madmin->getabsen_siswa2(date_format($tanggal, "Y-m-d"), $arraysiswa[$i]->nis);
                                                if (!$absen) {
                                                    $hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
                                                    if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
                                                        $libur = 'Tidak ada Kegiatan Belajar Mengajar';
                                                        if ($hasil) {
                                                            $libur = object_get($hasil[0], 'ket');
                                                        }
                                                        $absen[0] = [
                                                            'stat' => 'L',
                                                            'ket' => $libur];
                                                    } else {
                                                        $absen[0] = [
                                                            'stat' => 'A',
                                                            'ket' => "Belum Melakukan Presensi Masuk"];
                                                        $madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"));
                                                    }

                                                }

                                                $data[$i] = [
                                                    'nis' => object_get($arraysiswa[$i], 'nis'),
                                                    'nama' => object_get($arraysiswa[$i], 'nama'),
                                                    'kehadiran' => $absen[0]
                                                ];
                                            }
                                            $result = [
                                                'code' => 'OK4',
                                                'tanggal' => date_format($tanggal, "Y-m-d"),
                                                'data' => $datakls,
                                                'presensi' => $data

                                            ];

                                        } else
                                            $result = ['code' => 'Tidak Ada siswa dalam kelas ' . $kelas->nama];
                                    } else
                                        $result = ['code' => 'Tanggal Salah'];

                                } else
                                    $result = ['code' => 'data kelas tidak ditemukan'];
                            } else
                                $result = ['code' => 'Presensi belum dimulai di tanggal ' . $json->tgl];

                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function ubah_persiswa(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                isset($json->tgl) && isset($json->nis) && isset($json->stat) && isset($json->ket) && isset($json->p4ss)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
                        if ($user->getakses_admin($username)) {

                            $validas = $user->getpass_lama($username, $json->p4ss);
                            if ($validas) {
                                $madmin = new M_presensi();
                                $dashboard = new M_Dashboard();

                                $hasil = $dashboard->harilibur($json->tgl);
                                if (!$hasil && !$tool->convert_tgl_merah($json->tgl)) {
                                    $tokenwali = $madmin->update_persiswa($json->tgl, $json->nis, $json->stat, $json->x1d, $json->ket);

//                                        if ($tokenwali != "") {
//                                            $mytime = Carbon::now();
//                                            $jam1 = $mytime->toTimeString();
//                                            $message = [
//                                                "title" => "Update Presensi",
//                                                "content" => "Siswa " . $json->nama . " telah melakukan presensi dengan status" . $tool->status_kehadiran($json->stat) . "pada jam " . $jam1,
//                                                "chanel" => 'Presensi'
//                                            ];
//
//                                            //registration_ids jika target banyak
//                                            $fields = [
//                                                'data' => $message,
//                                                'to' => $tokenwali,
//                                            ];
//                                            $tokenwali2 = $tool->call_FMC($fields);
////                                                $code = $message;
//                                        }
                                    $result = ['code' => 'OK4'];

                                } else
                                    $result = ['code' => 'Tidak Mengubah Presensi di hari libur'];

                            } else
                                $result = [
                                    'code' => 'Password yang dimasukan salah',
                                ];

                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];

        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function ubah_perkelas(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                isset($json->tgl) && isset($json->id_kelas) && isset($json->stat) && isset($json->ket) && isset($json->p4ss)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
                        if ($user->getakses_admin($username)) {
                            $validas = $user->getpass_lama($username, $json->p4ss);
                            if ($validas) {
                                $madmin = new M_presensi();
                                $dashboard = new M_Dashboard();

                                $hasil = $dashboard->harilibur($json->tgl);
                                if (!$hasil && !$tool->convert_tgl_merah($json->tgl)) {
                                    $tokenwali = $madmin->update_perkelas($json->tgl, $json->stat, $json->id_kelas, $json->x1d, $json->ket);
//                                            if ($tokenwali != "") {
//                                                $mytime = Carbon::now();
//                                                $jam1 = $mytime->toTimeString();
//                                                $message = [
//                                                    "title" => "Update Presensi",
//                                                    "content" => "Siswa " . $json->nama . " telah diperbaharui status presensi" . $tool->status_kehadiran($json->stat) . "pada jam " . $jam1,
//                                                    "chanel" => 'Presensi'
//                                                ];
//
//                                                //registration_ids jika target banyak
//                                                $fields = [
//                                                    'data' => $message,
//                                                    'to' => $tokenwali,
//                                                ];
//                                                $tokenwali2 = $tool->call_FMC($fields);
////                                                $code = $message;
//                                            }
                                    $result = ['code' => 'OK4'];

                                } else {
                                    $result = ['code' => 'Tidak Mengubah Presensi di hari libur'];
                                }
                            } else
                                $result = [
                                    'code' => 'Pass yang dimasukan salah',
                                ];
                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function laporan_smes(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                isset($json->id_kelas) && isset($json->smes)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
//
                        if ($user->getakses_admin($username)) {
                            $dashboard = new M_Dashboard();
                            $kelas = $dashboard->get_data_kelas($json->id_kelas);
                            if ($kelas) {
                                $inputmaster = new Modul_Kelas();
                                $wali = $inputmaster->get_wali_kelas($json->id_kelas);

                                $namawali = '-';
                                if ($wali) {
                                    $namawali = object_get($wali[0], 'nama');
                                }
                                $datakls = [
                                    'nama_kelas' => $kelas->nama,
                                    'thn_ajar' => $kelas->tahun_ajar,
                                    'wali' => $namawali
                                ];

                                if (strtolower($json->smes) == 'ga') {
                                    $smes = 'Gasal';
                                    $tnggal = substr($kelas->tahun_ajar, 0, 4) . '-07-01';
                                    $batas = substr($kelas->tahun_ajar, 0, 4) . '-12-29';

                                } else {
                                    $smes = 'Genap';

                                    $tnggal = substr($kelas->tahun_ajar, 0, 4) . '-12-29';
                                    $batas = (substr($kelas->tahun_ajar, 0, 4) + 1);
                                    $batas = $batas . '-07-01';
                                }
                                $madmin = new M_presensi();

                                $arraysiswa = $madmin->getabsen_kelas_siswa($json->id_kelas);
                                $akhir = $batas;
                                if ($arraysiswa) {
                                    $array = 0;//
                                    for ($i = 0; $i < count($arraysiswa); $i++) {
                                        $tanggal = date_create($tnggal);
                                        $tanggal_batas = date_create($batas);

                                        $alpha = 0;
                                        $sakit = 0;
                                        $izin = 0;
                                        $telat = 0;
                                        while (date_format($tanggal_batas, "Y-m-d") != date_format($tanggal, "Y-m-d")) {

                                            $akhir = date_format($tanggal, "Y-m-d");
                                            if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
                                                $array = 1;
                                                $absen = $madmin->getabsen_siswa2(date_format($tanggal, "Y-m-d"), $arraysiswa[$i]->nis);

                                                if ($absen) {
                                                    $ket = object_get($absen[0], 'stat');
                                                    if ($ket == 'A') {
                                                        $alpha++;
                                                    } else if ($ket == 'I') {
                                                        $izin++;
                                                    } else if ($ket == 'S') {
                                                        $sakit++;
                                                    } else if ($ket == 'H')
                                                        $telat++;
                                                } else {
                                                    $hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
                                                    if (!$hasil && !$tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
                                                        $alpha++;
                                                        $madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"));

                                                    }
                                                }
                                                date_add($tanggal, date_interval_create_from_date_string("1 days"));

                                            } else
                                                break;

//
                                        }
                                        $data[$i] = [
                                            'nis' => object_get($arraysiswa[$i], 'nis'),
                                            'nama' => object_get($arraysiswa[$i], 'nama'),
                                            'sakit' => $sakit,
                                            'izin' => $izin,
                                            'alpha' => $alpha,
                                            'hadir' => $telat
//	                                            'total'=> $sakit+$izin+$alpha+$hadir
                                        ];
                                    }
                                    $akhir = date_create($akhir);
                                    if ($array == 1)
                                        date_sub($akhir, date_interval_create_from_date_string("1 days"));
                                    $periode = [
                                        'smes' => $smes,
                                        'awal' => $tnggal,
//                                            'awda' => $array,
                                        'akhir' => date_format($akhir, "Y-m-d")
                                    ];

                                    $result = ['code' => 'OK4',
                                        'data' => $datakls,
                                        'periode' => $periode,
                                        'siswa' => $data
                                    ];

                                } else
                                    $result = ['code' => 'Tidak Ada siswa dalam kelas ' . $kelas->nama];

                            } else {
                                $result = ['code' => 'Data kelas tidak ditemukan'];
                            }
                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

    public function laporan2(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');
        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                isset($json->id_kelas) && isset($json->tgl)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                $json->tgl = substr($json->tgl, 0, 7) . '-01';
                if ($token == $tool->generate_token($key, $username, $type)) {
                    if ($user->chek_token($username, $token, $type)) {
//
                        if ($user->getakses_admin($username)) {
                            $dashboard = new M_Dashboard();
                            $kelas = $dashboard->get_data_kelas($json->id_kelas);
                            if ($kelas) {
                                if ($tool->thn_ajar_pertanggal($json->tgl) == $kelas->tahun_ajar) {

                                    if ($tool->batasan_tglskrng($json->tgl)) {
                                        $inputmaster = new Modul_Kelas();
                                        $ketua = $inputmaster->get_ketua_kelas($json->id_kelas);
                                        $wali = $inputmaster->get_wali_kelas($json->id_kelas);

                                        $namasiswa = '-';
                                        $namawali = '-';
                                        $nipwali = '-';

                                        if ($ketua) {
                                            $namasiswa = object_get($ketua[0], 'nama');

                                        }
                                        if ($wali) {
                                            $namawali = object_get($wali[0], 'nama');
                                            $nipwali = object_get($wali[0], 'nip');
                                        }
                                        $datakls = [
                                            'nama_kelas' => $kelas->nama,
                                            'thn_ajar' => $kelas->tahun_ajar,
                                            'ketua' => $namasiswa,
                                            'wali' => $namawali,
                                            'nipwali' => $nipwali
                                        ];

                                        $madmin = new M_presensi();

                                        $arraysiswa = $madmin->getabsen_kelas_siswa($json->id_kelas);
                                        if ($arraysiswa) {
                                            $data = [];
                                            for ($i = 0; $i < count($arraysiswa); $i++) {

                                                $tanggal = date_create($json->tgl);
                                                $bln_dpn = $tool->bulan_depan($json->tgl);
                                                $arrayke = 0;
                                                $list = [];
                                                while ($bln_dpn != date_format($tanggal, "Y-m-d")) {
                                                    if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
                                                        $absen = $madmin->getabsen_siswa(date_format($tanggal, "Y-m-d"), $arraysiswa[$i]->nis);
                                                        if (!$absen) {
                                                            $hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
                                                            if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
                                                                $libur = 'Tidak ada Kegiatan Belajar Mengajar';
                                                                if ($hasil) {
                                                                    $libur = object_get($hasil[0], 'ket');
                                                                }
                                                                $absen = [
                                                                    'tanggal' => date_format($tanggal, "Y-m-d"),
                                                                    'stat' => 'L',
                                                                    'ket' => $libur];
                                                            } else {
                                                                $absen = [
                                                                    'tanggal' => date_format($tanggal, "Y-m-d"),
                                                                    'stat' => 'A',
                                                                    'ket' => "Belum Melakukan Presensi Masuk"];
                                                                $madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"));
                                                            }

                                                            $list[$arrayke] = $absen;
                                                        } else {
                                                            $list[$arrayke] = $absen[0];
                                                        }
                                                    } else {
                                                        $absen = [
                                                            'tanggal' => date_format($tanggal, "Y-m-d"),
                                                            'stat' => 'H',
                                                            'ket' => ""];

                                                        $list[$arrayke] = $absen;
                                                    }
//

                                                    $arrayke++;
                                                    date_add($tanggal, date_interval_create_from_date_string("1 days"));
                                                }
                                                $data[$i] = [
                                                    'nis' => object_get($arraysiswa[$i], 'nis'),
                                                    'nama' => object_get($arraysiswa[$i], 'nama'),
                                                    'kehadiran' => $list
                                                ];
                                            }
                                            $result = [
                                                'code' => 'OK4',
                                                'data' => $datakls,
                                                'presensi' => $data

                                            ];

                                        } else
                                            $result = ['code' => 'Tidak Ada siswa dalam kelas ' . $kelas->nama];
                                    } else {

                                        $result = ['code' => 'Presensi belum dimulai'];
                                    }
                                } else
                                    $result = ['code' => 'Tanggal Salah'];

                            } else {
                                $result = ['code' => 'data kelas tidak ditemukan'];
                            }
                        } else
                            $result = ['code' => 'Akses Ditolak'];

                    } else
                        $result = ['code' => 'TOKEN1'];

                } else
                    $result = ['code' => 'TOKEN2'];

            } else
                $result = ['code' => 'format data yg dikirim salah'];
        } else
            $result = ['code' => 'format data yg dikirim salah '];

        return $result;

    }

}