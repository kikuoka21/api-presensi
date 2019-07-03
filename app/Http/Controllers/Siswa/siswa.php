<?php
/**
 * Created by PhpStorm.
 * User: theno
 * Date: 5/12/2019
 * Time: 9:47 AM
 */

namespace App\Http\Controllers\Siswa;


use App\Http\Controllers\Controller;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_siswa;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class siswa extends Controller
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
//                            $tanggal = '2019-05-11';


							$hasil = $dashboard->harilibur($tanggal);
							if (!$hasil) {
								if ($tool->tgl_merah()) {
									$hari_ini = [
										'status' => 'L',
										'ket' => 'Tidak ada KBM'
									];
								} else {
									$hari_ini = [
										'status' => 'M',
										'ket' => ''
									];
								}
							} else {
								$hari_ini = [
									'status' => 'L',
									'ket' => object_get($hasil[0], 'ket')
								];
							}

							$msiswa = new M_siswa();
							$hasil_kelas = $msiswa->getKels($username, $tool->thn_ajar_skrng());
							if ($hasil_kelas) {
								$id_kelas = object_get($hasil_kelas[0], 'kd_kels');
								$nama_kelas = object_get($hasil_kelas[0], 'kelas');
							} else {
								$id_kelas = '-';
								$nama_kelas = 'Belum Diinput';
							}

							$result = [
								'code' => 'OK4',
								'kd_kelas' => $id_kelas,
								'nm_kelas' => $nama_kelas,
								'hari_ini' => $hari_ini

							];

						} else
							$result = ['code' => 'TOKEN1'];

					} else {
						$result = ['code' => 'TOKEN2'];
					}

				} else
					$result = ['code' => 'ISI nama PARAM dikirim salah'];


			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function create_qr(Request $request)
	{
		$user = new User();
		$tool = new Tool();
		$msiswa = new M_siswa();
		$dashboard = new M_Dashboard();

		$json = $request->input('parsing');
		if ($json == null) {
			return Redirect::to('/');
		} else {
			if ($tool->IsJsonString($json)) {
				$json = json_decode($json);
				if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->kd_kls)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;
					$kelas = $json->kd_kls;
					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {
							if ($tool->tgl_merah()) {
								$result = [
									'code' => 'tidak ada KBM'
								];
							} else {
								$tanggal = $tool->get_date();
//                            $tanggal = '2019-05-21';
								$hasil = $dashboard->harilibur($tanggal);
								if (!$hasil) {
									$hasil = $msiswa->get_flag_2($kelas, $tanggal);
									if (!$hasil) {
										$getsiswa = $msiswa->get_all_siswa($kelas);
										$token = '';
										if ($getsiswa) {
											$token = md5('sudah d!enkrip' . md5($tanggal . '\'' . $kelas) . '!' . $username);
											$msiswa->insert_token($kelas, $tanggal, $token);
											$code = 'OK4';
											for ($i = 0; $i < count($getsiswa); $i++) {
												$check = $msiswa->check_absen(object_get($getsiswa[$i], 'nis'), $tanggal);
												if (!$check)
													$msiswa->create_absen(object_get($getsiswa[$i], 'nis'), $tanggal);
											}

										} else {
											$code = 'tidak ada data siswa di kelas ' . $kelas;
										}


									} else {
										$code = 'OK4';
										$token = $hasil->token;
									}

									$result = [
										'code' => $code,
										'tanggal' => $tanggal,
										'tokennya' => $token
									];
								} else {
									$result = [
										'code' => 'tidak ada KBM'
									];
								}
							}


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

	public function isi_absen(Request $request)
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
					isset($json->kd_kls) && isset($json->token_absen) && isset($json->tanggal)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$key = $json->key;
					$kelas = $json->kd_kls;
					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {

							$msiswa = new M_siswa();
							$tanggal = $json->tanggal;

							$tokenkelas = $msiswa->get_flag_($kelas);
							if ($tokenkelas->token == $json->token_absen) {

								$hasil = $msiswa->check_absen($username, $tanggal);
								if ($hasil) {
									if ($hasil->stat == 'A') {
										$mytime = Carbon::now();
										$jam = $mytime->toTimeString();
										$jam = strtotime($jam);
										if (strtotime('06:25:00') <= $jam) {
											$ket = '';
											$stat = 'H';
											$code = 'OK4';
											if (strtotime('07:15:00') <= $jam) {
												$ket = 'Presensi pada jam ' . $mytime->toTimeString();
												$stat = 'T';
											}
//                                            $code =$ket;
											$msiswa->update_absen($username, $tanggal, $stat, $ket);
										} else {
											$code = 'Pengisian presensi pada jam 6.25 pagi sampai 7.15 pagi';
										}

									} else {
										if ($hasil->stat == 'H')
											$pesan = 'Hadir';
										else if ($hasil->stat == 'I')
											$pesan = 'Izin';
										else if ($hasil->stat == 'T')
											$pesan = 'Telat';
										else
											$pesan = 'Sakit';

										$code = 'anda sudah melakukan Presensi pada tanggal ' . $tanggal . ' dengan status ' . $pesan;
									}

								} else {
									$code = 'eror pada pencarian kehadiran';

								}

								$result = [
									'code' => $code
								];
							} else {
								$result = [
									'code' => 'token kelas salah'
								];
							}

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

	public function profil(Request $request)
	{
		$user = new User();
		$tool = new Tool();

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

							$msiswa = new M_siswa();
							$profil = $msiswa->get_profil_siswa($json->x1d);

							if ($profil) {
								$kelas = $msiswa->history_kelas($json->x1d);
								if (!$kelas)
									$kelas = [];

								$data = [
									'profil' => $profil,
									'kelas' => $kelas
								];

								$result = [
									'code' => 'OK4',
									'data' => $data
								];
							} else
								$result = ['code' => 'Data Tidak Ditemukan'];
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


}