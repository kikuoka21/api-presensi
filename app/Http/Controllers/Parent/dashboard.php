<?php


namespace App\Http\Controllers\Parent;


use App\Http\Controllers\Controller;
use App\Modules_admin\Modul_Kelas;
use App\Modules_parent\User_parent;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_presensi;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;

class dashboard extends Controller
{

    public function login(Request $request)
    {
        $user = new User_parent();
        $tool = new Tool();
        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->xp455) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                $pass = $json->xp455;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                $hasil = $user->get_wali($username);

                if (count($hasil) == 1) {

                    if (object_get($hasil[0], 'password') == $pass) {

                        $token = $tool->generate_token($key, $username, $type);
                        $user->input_tokenmobile($username, $token, $key);
                        $result = [
                            'hasil' => true,
                            'token' => $token,
                            'nama' => $user->getdata_dashboard($username),
                            'thn-ajar' => $tool->thn_ajar_skrng(),
                            'tanggal' => $tool->tgl_skrng(),

                        ];
                    } else {
                        $result = [
                            'hasil' => false,
                            'message' => 'Sandi yang dimasukan salah'
                        ];
                    }

                } else {
                    $result = [
                        'hasil' => false,
                        'message' => 'Nomor Induk Siswa tidak ditemukan'
                    ];
                }

            } else
                $result = [
                    'hasil' => false,
                    'message' => 'cek kembali parameter yang dikirim'
                ];
        } else
            $result = [
                'hasil' => false,
                'message' => 'cek kembali parameter yang dikirim'
            ];


        return $result;
    }


    public function __invoke(Request $request)
    {
        $user_wali = new User_parent();
        $tool = new Tool();
        $dashboard = new M_Dashboard();
        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->token_firebase)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {

                    if ($user_wali->chek_token_wali($username, $token)) {

                        $tanggal = $tool->get_date();


                        $user = new User();
                        $user->update_firebase_wali($username, $json->token_firebase);
                        $hasil = $dashboard->harilibur($tanggal);

                        $status_siswa = [
                            'status' => 'A',
                            'ket' => ''
                        ];
                        $tag1 = false;
                        $tag2 = false;


                        if (!$hasil) {
                            if ($tool->tgl_merah()) {
                                $hari_ini = [
                                    'status' => false,
                                    'ket' => 'Tidak ada KBM'
                                ];
                            } else {
                                $hari_ini = [
                                    'status' => true,
                                    'ket' => ''
                                ];


                                $tag1 = true;

                            }
                        } else {
                            $hari_ini = [
                                'status' => false,
                                'ket' => object_get($hasil[0], 'ket')
                            ];
                        }

                        $msiswa = new M_siswa();
                        $hasil_kelas = $msiswa->getKels($username, $tool->thn_ajar_skrng());
                        $id_kelas = '-';
                        $nama_kelas = 'Belum Diinput';
                        if ($hasil_kelas) {
                            $id_kelas = object_get($hasil_kelas[0], 'kd_kels');
                            $nama_kelas = object_get($hasil_kelas[0], 'kelas');
                            $tag2 = true;
                        }

                        if ($tag1 && $tag2) {
                            $presensi_siswa = $user_wali->getabsen_wali($username, $tanggal);

                            $status_siswa = [
                                'status' => 'A',
                                'ket' => 'belum absen masuk'
                            ];
                            if ($presensi_siswa) {
                                if (object_get($presensi_siswa[0], 'stat') != "A") {
                                    $status_siswa = [
                                        'status' => object_get($presensi_siswa[0], 'stat'),
                                        'ket' => object_get($presensi_siswa[0], 'ket'),
                                    ];
                                }

                            }
                        }

                        $result = [
                            'token' => true,
                            'hasil' => true,
                            'kd_kelas' => $id_kelas,
                            'nm_kelas' => $nama_kelas,
                            'hari_ini' => $hari_ini,
                            'status_siswa' => $status_siswa

                        ];

                    } else
                        $result = [
                            'token' => false,
                            'hasil' => false,
                            'message' => 'Token Sudah Tidak Valid, Silahkan Login Kembali'
                        ];

                } else {
                    $result = [
                        'token' => false,
                        'hasil' => false,
                        'message' => 'Token Anda Salah, Silahkan Login Kembali'
                    ];
                }

            } else
                $result = [
                    'token' => true,
                    'hasil' => false,
                    'message' => 'ISI nama PARAM dikirim salah'
                ];


        } else
            $result = [
                'token' => true,
                'hasil' => false,
                'message' => 'cek kembali parameter yang dikirim',
            ];


        return $result;
    }

    public function profil(Request $request)
    {
        $user_wali = new User_parent();
        $tool = new Tool();
        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {

                    if ($user_wali->chek_token_wali($username, $token)) {

                        $msiswa = new M_siswa();
                        $profil = $msiswa->get_profil_siswa($json->x1d);

                        if ($profil) {
                            $kelas = $msiswa->history_kelas($json->x1d);

                            $result = [
                                'token' => true,
                                'hasil' => true,
                                'profil' => $profil,
                                'kelas' => $kelas
                            ];

                        } else
                            $result = [
                                'token' => true,
                                'hasil' => false,
                                'message' => 'Data Tidak Ditemukan'
                            ];

                    } else
                        $result = [
                            'token' => false,
                            'hasil' => false,
                            'message' => 'Token Sudah Tidak Valid, Silahkan Login Kembali'
                        ];

                } else {
                    $result = [
                        'token' => false,
                        'hasil' => false,
                        'message' => 'Token Anda Salah, Silahkan Login Kembali'
                    ];
                }

            } else
                $result = [
                    'token' => true,
                    'hasil' => false,
                    'message' => 'ISI nama PARAM dikirim salah'
                ];


        } else
            $result = [
                'token' => true,
                'hasil' => false,
                'message' => 'cek kembali parameter yang dikirim',
            ];


        return $result;
    }


    public function presensi(Request $request){
        $user_wali = new User_parent();
        $tool = new Tool();
        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)&& isset($json->tanggal)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                $json->tanggal = substr($json->tanggal, 0, 7);
                if ($token == $tool->generate_token($key, $username, $type)) {
//                if (true) {
//                if (true) {
//
                    if ($user_wali->chek_token_wali($username, $token)) {

                        $dashboard = new M_Dashboard();

                        $kd_kelas = $dashboard->get_kode_kelas($tool->thn_ajar_pertanggal($json->tanggal), $username);
                        if ($kd_kelas) {
                            $kd_kelas = object_get($kd_kelas[0], 'id_kelas');
                            $kelas = $dashboard->get_data_kelas($kd_kelas);
                            if ($tool->thn_ajar_pertanggal($json->tanggal) == $kelas->tahun_ajar) {
                                $inputmaster = new Modul_Kelas();
                                $ketua = $inputmaster->get_ketua_kelas($kd_kelas);
                                $wali = $inputmaster->get_wali_kelas($kd_kelas);

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
                                    'thn_ajar' => $kelas->tahun_ajar,
                                    'ketua' => $namasiswa,
                                    'wali' => $namawali
                                ];

                                $madmin = new M_presensi();


                                $tanggal = date_create($json->tanggal);
                                $bln_dpn = $tool->bulan_depan($json->tanggal);
                                $arrayke = 0;
                                $list = [];
                                while ($bln_dpn != date_format($tanggal, "Y-m-d")) {
                                    if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
                                        $absen = $madmin->getabsen_siswa(date_format($tanggal, "Y-m-d"), $json->x1d);
                                        if (!$absen) {
                                            $hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
                                            if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
                                                $libur = 'Tidak ada KBM';
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
                                                    'ket' => "Belum Absen"];
                                                $madmin->create_absen($json->x1d, date_format($tanggal, "Y-m-d"));
                                            }

                                            $list[$arrayke] = $absen;
                                        } else {
                                            $list[$arrayke] = $absen[0];
                                        }
                                        $arrayke++;
                                        date_add($tanggal, date_interval_create_from_date_string("1 days"));
                                    } else
                                        break;
                                }
//

                                $result = [
                                    'token' => true,
                                    'hasil' => true,
                                    'datakelas' => $datakls,
                                    'kehadiran' => $list

                                ];


                            } else
                                $result = [
                                    'token' => true,
                                    'hasil' => false,
                                    'message' => 'Tanggal Salah'];

                        } else
                            $result = [
                                'token' => true,
                                'hasil' => false,
                                'message' => 'Data presensi tidak ditemukan'
                            ];

                    } else
                        $result = [
                            'token' => false,
                            'hasil' => false,
                            'message' => 'Token Sudah Tidak Valid, Silahkan Login Kembali'
                        ];

                } else {
                    $result = [
                        'token' => false,
                        'hasil' => false,
                        'message' => 'Token Anda Salah, Silahkan Login Kembali'
                    ];
                }

            } else
                $result = [
                    'token' => true,
                    'hasil' => false,
                    'message' => 'ISI nama PARAM dikirim salah'
                ];


        } else
            $result = [
                'token' => true,
                'hasil' => false,
                'message' => 'cek kembali parameter yang dikirim',
            ];


        return $result;
    }
}