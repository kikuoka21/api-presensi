<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 10:22 AM
 */

namespace App\Http\Controllers\Admin;


use App\Http\Controllers\Controller;
use App\Modules_admin\M_admin;
use App\Modules_admin\Modul_Kelas;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_presensi;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
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
//
                            if ($user->getakses_admin($username)) {
                                $tanggal = $tool->get_date();
//                                $tanggal = '2019-05-12';
                                $list = [];

                                $hasil = $dashboard->harilibur($tanggal);
                                if (!$hasil) {
                                    if ($tool->tgl_merah()) {
//                                    if (false) {
                                        $hari_ini = [
                                            'status' => 'L',
                                            'ket' => 'Tidak ada KBM'
                                        ];
                                    } else {
                                        $hari_ini = [
                                            'status' => 'M',
                                            'ket' => ''
                                        ];

                                        $madmin = new M_admin();
                                        $hasil_kelas = $madmin->getakses($username);
                                        if (object_get($hasil_kelas[0], 'level') == 1) {
                                            $list = $madmin->getabsen_all($tanggal);
                                        } else {
                                            $list = $madmin->getabsen_kelas($tanggal, $username);
                                        }
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
//								'kd_kelas' => object_get($hasil_kelas[0], 'level'),
//								'nm_kelas' => $tanggal,
                                    'list_absen' => $list

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

    public function list_kelas(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
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


                                $dashboard = new M_Dashboard();
                                $result = [
                                    'code' => 'OK4',
//                                    'ada' => $tool->convert_tgl_merah($json->tgl)
                                    'list_absen' => $dashboard->get_kelas($tool->thn_ajar_pertanggal($json->tgl))

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

    public function laporan(Request $request)
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
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&
                    isset($json->id_kelas) && isset($json->tgl)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
//
                            if ($user->getakses_admin($username)) {
                                $kelas = $dashboard->get_data_kelas($json->id_kelas);
                                if ($kelas) {
                                    if ($tool->thn_ajar_pertanggal($json->tgl) == $kelas->tahun_ajar) {

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
                                            'wali' => $namawali
                                        ];

                                        $tanggal = date_create($json->tgl);
                                        $bln_dpn = $tool->bulan_depan($json->tgl);
                                        $madmin = new M_presensi();
                                        $arrayke = 0;
                                        $arraysiswa = $madmin->getabsen_kelas_siswa($json->id_kelas);

                                        while ($bln_dpn != date_format($tanggal, "Y-m-d")) {


                                            if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
                                                $data = $madmin->getabsen_kelas_tanggal(date_format($tanggal, "Y-m-d"), $json->id_kelas);
                                                if (!$data) {
                                                    $hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
                                                    if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
                                                        for ($i = 0; $i < count($arraysiswa); $i++) {
                                                            $libur = 'tidak ada KBM';
                                                            if ($hasil) {
                                                                $libur = object_get($hasil[0], 'ket');
                                                            }
                                                            $data[$i] = ['nis' => object_get($arraysiswa[$i], 'nis'),
                                                                'nama' => object_get($arraysiswa[$i], 'nama'),
                                                                'stat' => 'L',
                                                                'ket' => $libur];
                                                        }
                                                    } else {
                                                        for ($i = 0; $i < count($arraysiswa); $i++) {
                                                            $data[$i] = ['nis' => object_get($arraysiswa[$i], 'nis'),
                                                                'nama' => object_get($arraysiswa[$i], 'nama'),
                                                                'stat' => 'A',
                                                                'ket' => 'Tidak Dibuatnya QR'];
                                                            $madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"), $json->id_kelas);
                                                        }
                                                    }


                                                }

                                                $list[$arrayke] = [
                                                    'tanggal' => date_format($tanggal, "Y-m-d"),
                                                    'presensi' => $data
                                                ];
                                                $arrayke++;
                                                date_add($tanggal, date_interval_create_from_date_string("1 days"));
                                            } else {
                                                break;
                                            }

                                        }

                                        $result = [
                                            'code' => 'OK4',
                                            'datakelas' => $datakls,
                                            'presensi' => $list

                                        ];
                                    } else
                                        $result = ['code' => 'Tahun Ajaran Sudah Berganti'];

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
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else
                $result = ['code' => 'format data yg dikirim salah '];

            return $result;
        }
    }

}