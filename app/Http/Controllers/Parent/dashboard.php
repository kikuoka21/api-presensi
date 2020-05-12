<?php


namespace App\Http\Controllers\Parent;


use App\Http\Controllers\Controller;
use App\Modules_parent\User_parent;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
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
            if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                $token = $json->token;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                if ($token == $tool->generate_token($key, $username, $type)) {

                    if ($user_wali->chek_token_wali($username, $token)) {

                        $tanggal = $tool->get_date();


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


}