<?php
/**
 * Created by PhpStorm.
 * User: user
 * Date: 5/15/2019
 * Time: 1:31 PM
 */

namespace App\Http\Controllers\Admin\master;

use App\Http\Controllers\Controller;
use App\Modules_admin\Modul_Kelas;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class kelas extends Controller
{
	public function isi_kelas(Request $request)
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
					isset($json->kd_kelas)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();
								$validasi = $inputmaster->validasi_kelas($json->kd_kelas);
								if ($validasi) {
									$ketua = $inputmaster->get_ketua_kelas($json->kd_kelas);
									$wali = $inputmaster->get_wali_kelas($json->kd_kelas);

									$nis = '-';
									$namasiswa = '-';
									$nip = '-';
									$namawali = '-';
									if ($ketua) {
										$nis = object_get($ketua[0], 'nis');
										$namasiswa = object_get($ketua[0], 'nama');

									}
									if ($wali) {
										$nip = object_get($wali[0], 'nip');
										$namawali = object_get($wali[0], 'nama');
									}


									$data = [
										"nama" => object_get($validasi[0], 'nama_kelas'),
										"tahun" => substr(object_get($validasi[0], 'tahun_ajar'), 0, 4),
										"nis" => $nis,
										"nama_siswa" => $namasiswa,
										"nip" => $nip,
										"nama_staf" => $namawali
									];
									$result = [
										'code' => 'OK4',
										'data' => $data,
										'list' => $inputmaster->get_siswakelas($json->kd_kelas)
									];
								} else
									$result = ['code' => 'Data kelas tidak ditemukan'];

							} else
								$result = ['code' => 'Akses Ditolak'];
						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'ISI nama PARAM dikirim salah'];
			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function list_siswa(Request $request)
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
					isset($json->thn) && isset($json->thn_lahir) && isset($json->nama)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();
								$hasil = $inputmaster->list_siswa($json->nama, $json->thn, $json->thn_lahir);

								$result = [
									'code' => 'OK4',
									'data' => $hasil
								];

							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function tambah_siswa_kelas(Request $request)
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
					isset($json->id_kelas) && isset($json->nis)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();
								$hasil = $inputmaster->check_siswa_isikelas($json->id_kelas, $json->nis);
								if (!$hasil) {
									$inputmaster->input_siswa_kelas($json->id_kelas, $json->nis);
									$result = [
										'code' => 'OK4'
									];
								} else {
									$result = [
										'code' => 'NIS Tersebut Sudah Dimasukan Ke Dalam Kelas'
									];
								}


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function hapus_siswa(Request $request)
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
					isset($json->id_kelas) && isset($json->nis)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();
								$hasil = $inputmaster->check_siswa_isikelas($json->id_kelas, $json->nis);
								if ($hasil) {
									$inputmaster->hapus_siswa_kelas($json->id_kelas, $json->nis);
									$result = [
										'code' => 'OK4'
									];
								} else
									$result = [
										'code' => 'Siswa tersebut tidak dalam kelas'
									];

							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function ubah_lv_siswa(Request $request)
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
					isset($json->id_kelas) && isset($json->nis) && isset($json->level)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();
								$hasil = $inputmaster->check_siswa_isikelas($json->id_kelas, $json->nis);
								if ($hasil) {

									$inputmaster->ganti_lv_siswa($json->id_kelas, $json->nis, $json->level);

									$result = [
										'code' => 'OK4'
									];
								} else
									$result = [
										'code' => 'NIS tersebut tidak ditemukan'
									];

							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function list_walikelas(Request $request)
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
					isset($json->thn_ajar)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();


								$hasil = $inputmaster->list_walikelas($json->thn_ajar);

								$result = [
									'code' => 'OK4',
									'data' => $hasil
								];


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function ubah_walikelas(Request $request)
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
					isset($json->id_kelas) && isset($json->nip)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();

								$inputmaster->ubah_walikelas($json->id_kelas, $json->nip);

								$result = [
									'code' => 'OK4'
								];


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function ubah_ketuakelas(Request $request)
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
					isset($json->id_kelas) && isset($json->nis)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();

								$inputmaster->ubah_ketua_kelas($json->id_kelas, $json->nis);

								$result = [
									'code' => 'OK4'
								];


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function ubah_nama_kelas(Request $request)
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
					isset($json->id_kelas) && isset($json->nama_kelas)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$inputmaster = new Modul_Kelas();

								$hasil = $inputmaster->check_nama_kelas($json->id_kelas, $json->nama_kelas);

								if ($hasil) {
									$result = [
										'code' => 'double'
									];
								} else {
									$inputmaster->ubah_nama_kelas($json->id_kelas, $json->nama_kelas);
									$result = [
										'code' => 'OK4',
									];
								}


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function hapus_kelas(Request $request)
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
					isset($json->id_kelas) && isset($json->p4ss)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($user->getakses_admin($username) && $user->getakses_admin_piket($username)) {
								$validasi = $user->getpass_lama($username, $json->p4ss);
								if ($validasi) {
									$inputmaster = new Modul_Kelas();

									$validasi = $inputmaster->validasi_kelas($json->id_kelas);

									if (!$validasi) {
										$result = [
											'code' => 'Data kelas tidak ditemukan'
										];
									} else {
										$inputmaster->hapus_kelas($json->id_kelas);
										$msiswa = new M_siswa();

										$getsiswa = $msiswa->get_all_siswa($json->id_kelas);
										for ($i = 0; $i < count($getsiswa); $i++) {
											$inputmaster->hapus_siswa_kelas($json->id_kelas, object_get($getsiswa[$i], 'nis'));
										}
										$result = [
											'code' => 'OK4',
										];
									}
								} else
									$result = [
										'code' => 'Pass yang dimasukan salah',
									];


							} else
								$result = ['code' => 'Akses Ditolak'];

						} else
							$result = ['code' => 'TOKEN1'];

					} else
						$result = ['code' => 'TOKEN2'];

				} else
					$result = ['code' => 'Isi nama PARAM dikirim salah'];

			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

}