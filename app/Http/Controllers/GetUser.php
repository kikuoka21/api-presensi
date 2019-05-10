<?php


namespace App\Http\Controllers;


use App\Modules\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Str;

class GetUser extends Controller
{
    public $id;

    public function __invoke(Request $request)
    {
        // TODO: Implement __invoke() method.
        $user = new User();
        $id = $request->input('xuid');
        if ($id == null) {
            return Redirect::to('/');
        } else {
            $user->uid = $id;
            $hasil = $user->getUser();
            if (!$hasil) {
                $result = [
                    'code' => Str::random(32),
                    'data' => $request->input('xuid')
                ];
            } else {

                $result = [
                    'aaa' => object_get($hasil[0], 'password'),
                    'token' =>md5(''.md5('aldklnalkidnalk'))
                ];
                $result = [
                    'data' => $result
                ];


            }

        }


        return $result;
    }

}