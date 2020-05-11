<?php


namespace App\Http\Controllers\Parent;


use App\Http\Controllers\Controller;
use App\Modules_parent\User_parent;
use App\Modules_siswa\Tool;
use Illuminate\Http\Request;

class auth extends Controller
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

}