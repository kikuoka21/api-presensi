<?php


namespace App\Http\Controllers;


use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

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
                    if (!$hasil) {
                        $result = ['code' => 'nis yang dimasukan salah'];
                    } else {
                        if (object_get($hasil[0], 'password') == $pass) {
                            $token = $tool->generate_token($key, $username, $type);
                            if (object_get($hasil[0], 'akses') == '1' && $type == 'www') {
                                $user->input_tokenweb($username, $token);
                                $inputnya = 'web';
                            } else {
                                $user->input_tokenmobile($username, $token);
                                $inputnya = 'mobile';
                            }
                            $getnama = $user->getdata_dashboard($username, object_get($hasil[0], 'akses'));
                            $result = [
                                'status' => object_get($hasil[0], 'akses'),
                                'token' => $token,
	                            'thn-ajar'=> $tool->thn_ajar_skrng(),
	                            'tanggal'=> $tool->tgl_skrng(),
                                'data_pribadi' => $getnama[0]
                            ];
                            $tool->Isi_Log('login OK4 ' . $username . ' ' . $key . ' ' . $inputnya);
                            $result = [
                                'code' => 'OK4',
//                                'input' => $inputnya. '  '. $type,
//                                'input' => $tool->time(),
                                'data' => $result
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
            return $result;
        }
    }

    public function check_token(Request $request)
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
                if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) &&   isset($json->akses)) {
                    $token = $json->token;
                    $username = $json->x1d;
                    $type = $json->type;
                    $key = $json->key;
                    $akses = $json->akses;
//
                    if ($token == $tool->generate_token($key, $username, $type)) {
                        if ($user->chek_token($username, $token, $type)) {
                            $getnama = $user->getdata_dashboard($username, $akses);
                            $result = [
                                'code' => 'OK4',
	                            'thn-ajar'=> $tool->thn_ajar_skrng(),
	                            'tanggal'=> $tool->tgl_skrng(),
                                'data'=> $getnama[0]
                            ];
	                        $tool->Isi_Log('chek tkn OK4 ' . $username . ' ' . $key . ' ' . $akses);
                        } else
                            $result = ['code' => 'token sudah tidak valid'];

                    } else
                        $result = ['code' => 'token anda salah'];

                } else
                    $result = ['code' => 'ISI nama PARAM dikirim salah'];


            } else {
                $result = ['code' => 'format data yg dikirim salah '];

            }
            return $result;

        }


    }
    public function getip(Request $request){
	    $tool = new Tool();

	    $hasil= $request->ip().'  ';
	    $pesan  = $request->header('User-Agent');
	    $tool->Isi_Log('/ ' .$hasil.' '.$pesan);

	    return view('welcome')->with('result',$hasil.'<br><br>'.$pesan);
    }
}