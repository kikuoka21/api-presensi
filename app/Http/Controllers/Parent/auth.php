<?php


namespace App\Http\Controllers\Parent;


use App\Http\Controllers\Controller;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class auth extends Controller
{

    public function login(Request $request)
    {
        $user = new User();
        $tool = new Tool();
        $json = $request->input('parsing');

        if ($tool->IsJsonString($json)) {
            $json = json_decode($json);
            if (isset($json->xp455) && isset($json->x1d) && isset($json->type) && isset($json->key)) {
                $pass = $json->xp455;
                $username = $json->x1d;
                $type = $json->type;
                $key = $json->key;
                $hasil = $user->getUser($username);

                if (!$hasil) {
                    $result = [
                        'hasil' => true,
                        'message' => '--'
                    ];
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