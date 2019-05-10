<?php


namespace App\Http\Controllers;


use App\Modules\Tool;
use App\Modules\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class GetUser extends Controller
{

    public function Login(Request $request)
    {
        // TODO: Implement __invoke() method.
        $user = new User();
        $tool = new Tool();

        $json = $request->input('id');

        if ($json == null) {
            return Redirect::to('/');
        } else {
            if ($tool->IsJsonString($json)) {
                $json = json_decode($json);
//
                if (isset($json->xp455) || isset($json->x1d) || isset($json->type) || isset($json->key)) {
                    $pass = $json->xp455;
                    $username = $json->x1d;
                    $key = $json->type;
                    $type = $json->key;
                    $hasil = $user->getUser($username);
                    if (!$hasil) {
                        $result = ['code' => 'nis yang dimasukan salah'];
                    } else {
                        if (object_get($hasil[0], 'password') == $pass) {
                            $token =$tool->generate_token($key, $username);
                            $hasil = $user->input_token($username, $token);

                            $result = [
                                'aaa' => 'aadwa',
                                'token' => $hasil
                            ];
                        } else {
                            $result = ['code' => 'password yang dimasukan salah '];
                        }
                    }
                } else {
                    $result = ['code' => 'data yangdikirm salah'];
                }
            } else {
                $result = ['code' => 'format data yg dikirim salah '];
            }
        }

        return $result;
    }

}