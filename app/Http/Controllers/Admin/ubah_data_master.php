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

class ubah_data_master extends Controller
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

}