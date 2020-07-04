<?php


namespace App\Http\Controllers;


use App\Modules_parent\User_parent;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use function PHPSTORM_META\elementType;

class auth extends Controller
{
    public function Login(Request $request)
    {
        // TODO: Implement __invoke() method.
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');

        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                //
                if (isset($json->xp455) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                    $pass = $json->xp455;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $hasil = $user->getUser($username);
                    //                    $inputnya = '';
                    if (!$hasil) {
                        $result = ['code' => 'Username atau password yang dimasukan salah'];
                    } else {
                        if (object_get($hasil[0], 'password') == $pass) {
                            $token = $tool->generate_token($key, $username, $type);
                            if (object_get($hasil[0], 'akses') == '1' && $type == 'www') {
                                $user->input_tokenweb($username, $token);
                                //                                $inputnya = 'berhasil web';
                            } else {
                                $user->input_tokenmobile($username, $token);
//                                $user->input_token_firebase($username, $json->token_firebase);
                                //                                $inputnya = 'berhasil mobile';
                            }
                            $result = [
                                'status' => object_get($hasil[0], 'akses'),
                                'token' => $token,
                                'thn-ajar' => $tool->thn_ajar_skrng(),
                                'tanggal' => $tool->tgl_skrng(),
                                'data_pribadi' => $user->getdata_dashboard($username, object_get($hasil[0], 'akses'))
                            ];

                            $result = [
                                'code' => 'OK4',
                                //                                'input' => $inputnya. '  '. $type,
                                //                                'input' => $tool->time(),
                                'data' => $result
                            ];

                        } else {
                            $result = ['code' => 'Username atau password yang dimasukan salah'];
                        }
                    }

                    //                    $tool->Isi_Log('login ' . $inputnya . ' ' . $username . ' ' . $key);
                    $tool->Isi_Log('login ' . $username . ' ' . $key);

                } else {
                    $result = ['code' => 'data yang dikirm salah'];
                }
            } else {
                $result = ['code' => 'format data yg dikirim salah '];
            }
            return $result;
        }
    }

    public function ccc(Request $request)
    {
        $tool = new Tool();

        //        $panggil = new \App\Modules_siswa\Utilities();
        //
        //        $panggil->setXsearch($request->input('search'));
        //        $panggil->setXtahunCari($getXtahun_cari);
        //        $result = [
        //            'data' => $getXtahun_cari,
        //            '!= null;' => $compare_1,
        //            '!= 0' => $compare_2,
        //            '!= 99' => $compare_3,
        //            '== null' => $getXtahun_cari == null,
        //            '$panggil->PencarianPertama()' => $panggil->PencarianPertama(),
        //
        //        ];
//        $result = [
//            'success' => false,
//            'message' => 'token_invalid & nip_invalid, harap cek kembali authorization dan parameter'
//
//        ];
//        return response()->json($result, 201);
        $message = [
            "title" => "coba coba",
            "content" => "dari api qr_code2",
            "chanel" => 'Presensi'
        ];

        //registration_ids jika target banyak
        $fields = [
            'data' => $message,
            'to' => 'eYkQsEhmS4GYpAGfOharG4:APA91bEJGezM-tHCB8wW9HbTOBc4q8574RqSypJC_c74SXY3VIR_piydHsvYUK5q_yPEuX0DRPgaAkO1FiBvUasmzlr-Dm8gv6K-yNDT8hANuzwQVe-kkvxR6PYW6IeUH75cd9szvnoH',
        ];

        return $tool->call_FMC($fields);
    }

    public function logout(Request $request)
    {
        $tool = new Tool();

        $json = $request->has('parsing');
        if (!$json) {
            return Redirect::to('/');
        } else {
            $json = $request->input('parsing');
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->x1d) && isset($json->wali) && isset($json->token)) {

                    $username = $json->x1d;
                    $wali = $json->wali;
                    $token = $json->token;

                    $user = new User();
                    if ($user->token_to_logout($username, $wali, $token)) {
                        if ($wali) {
                            $test = 'wali';
                            $user->update_firebase_wali($username, '');
                        } else {
                            $test = 'bukan wali';

                            $user->update_firebase_user($username, '');
                        }
                        $result = [
                            'hasil' => true,
                            'hasil2' => $test,
                            'hasil23' => $request->has('parsing'),
                        ];
                    } else
                        $result = [
                            'hasil' => false,
                            'message' => 'Token Tidak Terdaftar'];

                } else {

                    $result = [
                        'hasil' => false,
                        'message' => 'ISI nama PARAM dikirim salah'];
                }
            } else
                $result = [
                    'hasil' => false,
                    'message' => 'format data yg dikirim salah'];
            return $result;
        }
    }

    public function check_token(Request $request)
    {
        $user = new User();
        $tool = new Tool();

        $json = $request->input('parsing');
        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->akses) && isset($json->wali)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $akses = $json->akses;
                    //

                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($json->wali) {
                            $user_wali = new User_parent();
                            if ($user_wali->chek_token_wali($username, $token)) {
                                $result = [
                                    'code' => 'OK4',
                                    'nama' => $user_wali->getdata_dashboard($username),
                                    'thn-ajar' => $tool->thn_ajar_skrng(),
                                    'tanggal' => $tool->tgl_skrng(),
                                ];

                            } else
                                $result = ['code' => 'TOKEN1'];
                        } else {
                            if ($user->chek_token($username, $token, $type)) {
                                $result = [
                                    'code' => 'OK4',
                                    'thn-ajar' => $tool->thn_ajar_skrng(),
                                    'tanggal' => $tool->tgl_skrng(),
                                    'data' => $user->getdata_dashboard($username, $akses)
                                ];
                            } else
                                $result = ['code' => 'TOKEN1'];
                        }
                    } else
                        $result = ['code' => 'TOKEN2'];

                } else
                    $result = [
                        'code' => 'ISI nama PARAM dikirim salah'];
            } else
                $result = [
                    'code' => 'format data yg dikirim salah '];

            return $result;

        }


    }

    public function getip(Request $request)
    {
        $tool = new Tool();

        $hasil = $request->ip() . '  ';
        $pesan = $request->header('User-Agent');
        $tool->Isi_Log('/ ' . $hasil . ' ' . $pesan);

        return view('welcome')->with('result', $hasil . '<br><br>' . $pesan);
    }

    public function getip2(Request $request)
    {


        return view('welcome')->with('result');
    }

    public function ubah_pass(Request $request)
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
                    isset($json->xp4s5) && isset($json->xp4s5_lama)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $pass = $json->xp4s5;
                    //xp4s5 ubah menjadi ini
                    //xp4s5_lama validasi pass
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            //
                            if (isset($json->username_target)) {
                                if ($user->getakses_admin($username)) {

                                    $validasi = $user->getpass_lama($username, $json->xp4s5_lama);
                                    if ($validasi) {
                                        $user->update_pass($json->username_target, $pass);
                                        $result = ['code' => 'OK4'];
                                    } else {
                                        $result = [
                                            'code' => 'Password anda yang dimasukan salah'
                                        ];
                                    }
                                } else $result = [
                                    'code' => 'Akses Ditolak'
                                ];
                            } else {
                                $compare = $user->comparepass($username, $json->xp4s5_lama);
                                if ($compare) {
                                    $user->update_pass($username, $pass);
                                    $result = [
                                        'code' => 'OK4'
                                    ];
                                } else
                                    $result = [
                                        'code' => 'Password Lama Anda Salah'
                                    ];


                            }
                        } else
                            $result = ['code' => 'TOKEN1'];

                    } else
                        $result = ['code' => 'TOKEN2'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else {
                $result = ['code' => 'format data yg dikirim salah '];

            }
            return $result;

        }
    }

}