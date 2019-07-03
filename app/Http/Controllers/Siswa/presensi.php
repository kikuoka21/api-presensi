<?php
/**
 * Created by PhpStorm.
 * User: kikuo
 * Date: 6/7/2019
 * Time: 2:09 AM
 */

namespace App\Http\Controllers\Siswa;

use App\Http\Controllers\Controller;
use App\Modules_admin\Modul_Kelas;
use App\Modules_siswa\M_Dashboard;
use App\Modules_siswa\M_presensi;
use App\Modules_siswa\Tool;
use App\Modules_siswa\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class presensi extends Controller
{

	public function get_presensi_harian(Request $request)
	{
		$user = new User();
		$tool = new Tool();

		$json = $request->input('parsing');
		if ($json == null) {
			return Redirect::to('/');
		} else {
			if ($tool->IsJsonString($json)) {
				$json = json_decode($json);
				if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)
					&& isset($json->id_kelas) && isset($json->tgl)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;

					$key = $json->key;

					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {

							$dashboard = new M_Dashboard();
							$kelas = $dashboard->get_data_kelas($json->id_kelas);
							if ($kelas) {
								if ($user->getakses_pengurus($username, $json->id_kelas)) {
									if ($tool->thn_ajar_pertanggal($json->tgl) == $kelas->tahun_ajar) {
										if ($tool->batasan_tglskrng($json->tgl)) {

											$inputmaster = new Modul_Kelas();
											$ketua = $inputmaster->get_ketua_kelas($json->id_kelas);
											$wali = $inputmaster->get_wali_kelas($json->id_kelas);

											$namasiswa = '-';
											$namawali = '-';
											if ($ketua) {
												$namasiswa = object_get($ketua[0], 'nama');

											}
											if ($wali) {
												$namawali = object_get($wali[0], 'nama');
											}
											$datakls = [
												'nama' => $kelas->nama,
												'thn_ajar' => $kelas->tahun_ajar,
												'ketua' => $namasiswa,
												'wali' => $namawali
											];

											$madmin = new M_presensi();

											$arraysiswa = $madmin->getabsen_kelas_siswa($json->id_kelas);
											if ($arraysiswa) {
												$data = [];
												for ($i = 0; $i < count($arraysiswa); $i++) {
													$tanggal = date_create($json->tgl);
													$absen = $madmin->getabsen_siswa2(date_format($tanggal, "Y-m-d"), $arraysiswa[$i]->nis);
													if (!$absen) {
														$hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
														if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
															$libur = 'Tidak ada KBM';
															if ($hasil) {
																$libur = object_get($hasil[0], 'ket');
															}
															$absen[0] = [
																'stat' => 'L',
																'ket' => $libur];
														} else {
															$absen[0] = [
																'stat' => 'A',
																'ket' => "Belum Absen"];
															$madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"));
														}

													}

													$data[$i] = [
														'nis' => object_get($arraysiswa[$i], 'nis'),
														'nama' => object_get($arraysiswa[$i], 'nama'),
														'kehadiran' => $absen[0]
													];


												}
												$result = [
													'code' => 'OK4',
													'datakelas' => $datakls,
													'presensi' => $data

												];
											} else
												$result = ['code' => 'Tidak Ada siswa dalam kelas ' . $kelas->nama];

										} else
											$result = ['code' => 'Presensi belum dimulai di tanggal ' . date("d F Y", strtotime($json->tgl))];
									} else
										$result = ['code' => 'Tanggal Salah'];

								} else
									$result = ['code' => 'Akses Ditolak'];

							} else
								$result = ['code' => 'data kelas tidak ditemukan'];


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

	public function get_presensi_perbulan(Request $request)
	{
		$user = new User();
		$tool = new Tool();

		$json = $request->input('parsing');
		if ($json == null) {
			return Redirect::to('/');
		} else {
			if ($tool->IsJsonString($json)) {
				$json = json_decode($json);
				if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key) && isset($json->level)
					&& isset($json->kelas) && isset($json->tanggal)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$json->tanggal = substr($json->tanggal, 0, 7);

					$key = $json->key;
					if ($json->level == '1') {
						if ($token == $tool->generate_token($key, $username, $type)) {
							if ($user->chek_token($username, $token, $type)) {


								$dashboard = new M_Dashboard();
								$kelas = $dashboard->get_data_kelas($json->kelas);
								if ($kelas) {
									if ($tool->thn_ajar_pertanggal($json->tanggal) == $kelas->tahun_ajar) {

										$inputmaster = new Modul_Kelas();
										$ketua = $inputmaster->get_ketua_kelas($json->kelas);
										$wali = $inputmaster->get_wali_kelas($json->kelas);

										$namasiswa = '-';
										$namawali = '-';
										if ($ketua) {
											$namasiswa = object_get($ketua[0], 'nama');

										}
										if ($wali) {
											$namawali = object_get($wali[0], 'nama');
										}
										$datakls = [
											'nama' => $kelas->nama,
											'ketua' => $namasiswa,
											'wali' => $namawali
										];

										$madmin = new M_presensi();

										$arraysiswa = $madmin->getabsen_kelas_siswa($json->kelas);
										if ($arraysiswa) {
											$data = [];
											for ($i = 0; $i < count($arraysiswa); $i++) {

												$tanggal = date_create($json->tanggal);
												$bln_dpn = $tool->bulan_depan($json->tanggal);
												$arrayke = 0;
												$list = [];
												while ($bln_dpn != date_format($tanggal, "Y-m-d")) {
													if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
														$absen = $madmin->getabsen_siswa(date_format($tanggal, "Y-m-d"), $arraysiswa[$i]->nis);
														if (!$absen) {
															$hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
															if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
																$libur = 'Tidak ada KBM';
																if ($hasil) {
																	$libur = object_get($hasil[0], 'ket');
																}
																$absen = [
																	'tanggal' => date_format($tanggal, "Y-m-d"),
																	'stat' => 'L',
																	'ket' => $libur];
															} else {
																$absen = [
																	'tanggal' => date_format($tanggal, "Y-m-d"),
																	'stat' => 'A',
																	'ket' => "Belum Absen"];
																$madmin->create_absen(object_get($arraysiswa[$i], 'nis'), date_format($tanggal, "Y-m-d"));
															}

															$list[$arrayke] = $absen;
														} else {
															$list[$arrayke] = $absen[0];
														}
														$arrayke++;
														date_add($tanggal, date_interval_create_from_date_string("1 days"));
													} else {
														break;
													}
//
												}
												$data[$i] = [
													'nis' => object_get($arraysiswa[$i], 'nis'),
													'nama' => object_get($arraysiswa[$i], 'nama'),
													'kehadiran' => $list
												];
											}
											$result = [
												'code' => 'OK4',
												'datakelas' => $datakls,
												'presensi' => $data

											];

										} else
											$result = ['code' => 'Tidak Ada siswa dalam kelas ' . $kelas->nama];

									} else
										$result = ['code' => 'Tanggal Salah'];

								} else {
									$result = ['code' => 'data kelas tidak ditemukan'];
								}

							} else
								$result = ['code' => 'TOKEN2'];

						} else
							$result = ['code' => 'TOKEN2'];

					} else
						$result = ['code' => 'Akses Ditolak'];
				} else
					$result = ['code' => 'ISI nama PARAM dikirim salah'];


			} else
				$result = ['code' => 'format data yg dikirim salah '];

			return $result;
		}
	}

	public function presensi_siswa(Request $request)
	{
		$user = new User();
		$tool = new Tool();

		$json = $request->input('parsing');
		if ($json == null) {
			return Redirect::to('/');
		} else {
			if ($tool->IsJsonString($json)) {
				$json = json_decode($json);
				if (isset($json->token) && isset($json->x1d) && isset($json->type) && isset($json->key)
					&& isset($json->tanggal)) {
					$token = $json->token;
					$username = $json->x1d;
					$type = $json->type;
					$json->tanggal = substr($json->tanggal, 0, 7);

					$key = $json->key;
					if ($token == $tool->generate_token($key, $username, $type)) {
						if ($user->chek_token($username, $token, $type)) {

							$dashboard = new M_Dashboard();

							$kd_kelas = $dashboard->get_kode_kelas($tool->thn_ajar_pertanggal($json->tanggal), $username);
							if ($kd_kelas) {
								$kd_kelas = object_get($kd_kelas[0], 'id_kelas');
								$kelas = $dashboard->get_data_kelas($kd_kelas);
								if ($tool->thn_ajar_pertanggal($json->tanggal) == $kelas->tahun_ajar) {
									$inputmaster = new Modul_Kelas();
									$ketua = $inputmaster->get_ketua_kelas($kd_kelas);
									$wali = $inputmaster->get_wali_kelas($kd_kelas);

									$namasiswa = '-';
									$namawali = '-';
									if ($ketua) {
										$namasiswa = object_get($ketua[0], 'nama');

									}
									if ($wali) {
										$namawali = object_get($wali[0], 'nama');
									}
									$datakls = [
										'nama' => $kelas->nama,
										'thn_ajar' => $kelas->tahun_ajar,
										'ketua' => $namasiswa,
										'wali' => $namawali
									];

									$madmin = new M_presensi();


									$tanggal = date_create($json->tanggal);
									$bln_dpn = $tool->bulan_depan($json->tanggal);
									$arrayke = 0;
									$list = [];
									while ($bln_dpn != date_format($tanggal, "Y-m-d")) {
										if ($tool->batasan_tglskrng(date_format($tanggal, "Y-m-d"))) {
											$absen = $madmin->getabsen_siswa(date_format($tanggal, "Y-m-d"), $json->x1d);
											if (!$absen) {
												$hasil = $dashboard->harilibur(date_format($tanggal, "Y-m-d"));
												if ($hasil || $tool->convert_tgl_merah(date_format($tanggal, "Y-m-d"))) {
													$libur = 'Tidak ada KBM';
													if ($hasil) {
														$libur = object_get($hasil[0], 'ket');
													}
													$absen = [
														'tanggal' => date_format($tanggal, "Y-m-d"),
														'stat' => 'L',
														'ket' => $libur];
												} else {
													$absen = [
														'tanggal' => date_format($tanggal, "Y-m-d"),
														'stat' => 'A',
														'ket' => "Belum Absen"];
													$madmin->create_absen($json->x1d, date_format($tanggal, "Y-m-d"));
												}

												$list[$arrayke] = $absen;
											} else {
												$list[$arrayke] = $absen[0];
											}
											$arrayke++;
											date_add($tanggal, date_interval_create_from_date_string("1 days"));
										} else
											break;
									}
//

									$result = [
										'code' => 'OK4',
										'datakelas' => $datakls,
										'kehadiran' => $list

									];


								} else
									$result = ['code' => 'Tanggal Salah'];

							} else
								$result = ['code' => 'Data presensi tidak ditemukan'];

						} else
							$result = ['code' => 'TOKEN2'];

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