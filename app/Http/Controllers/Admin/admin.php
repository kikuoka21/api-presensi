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
use App\Modules_siswa\M_Dashboard;
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
							$tanggal = $tool->get_date();
//							$tanggal = '2019-05-12';
							$list = [];
							if ($tool->tgl_merah()) {
								$hari_ini = [
									'status' => 'L',
									'ket' => 'Tidak ada KBM'
								];
							} else {
								$hasil = $dashboard->harilibur($tanggal);
								if (!$hasil) {
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
								} else {
									$hari_ini = [
										'status' => 'L',
										'ket' => object_get($hasil[0], 'ket')
									];
								}
							}
							$result = [
								'code' => 'OK4',
								'date' => $hari_ini,
//								'kd_kelas' => object_get($hasil_kelas[0], 'level'),
//								'nm_kelas' => object_get($hasil_kelas[0], 'level'),
								'list_absen' => $list

							];

						} else
							$result = ['code' => 'token data base sudah berubah'];

					} else
						$result = ['code' => 'token beda'];

				} else
					$result = ['code' => 'ISI nama PARAM dikirim salah'];


			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function input_siswa(Request $request)
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
//							$tanggal = $tool->get_date();
							$tanggal = '2019-05-12';
							$list = [];
							if ($tool->tgl_merah()) {
								$hari_ini = [
									'status' => 'L',
									'ket' => 'Tidak ada KBM'
								];
							} else {
								$hasil = $dashboard->harilibur($tanggal);
								if (!$hasil) {
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
								} else {
									$hari_ini = [
										'status' => 'L',
										'ket' => object_get($hasil[0], 'ket')
									];
								}
							}
							$result = [
								'code' => 'OK4',
								'date' => $hari_ini,
//								'kd_kelas' => object_get($hasil_kelas[0], 'level'),
//								'nm_kelas' => object_get($hasil_kelas[0], 'level'),
								'list_absen' => $list

							];

						} else
							$result = ['code' => 'token data base sudah berubah'];

					} else
						$result = ['code' => 'token beda'];

				} else
					$result = ['code' => 'ISI nama PARAM dikirim salah'];


			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}
}