<?php


/*
 * Author : Shaka
 * Date Created : 03 Februari 2015
 * Deskripsi : digunakan oleh sekre kelas karyawan Cabang untuk Cetak absen UTS dan UAS Cabang
 *
 */
session_start();
include('cekLogin.php');
include('configs/variables.php');
include('configs/output.php');
//cekModul($nmfile . ".php", $_SESSION['slogin'][1]); // lupa yang ini belum di sync
getMenu($_SESSION['slogin'][1]);

$use = 1;
$mys->assign('use', $use);

$namaModul = "Entry Nilai Sidang Tugas Akhir (Versi Beta 0.1)";
/*
 * Versi 0.1
 * - modul dibuat karena keadaan saat ini 2019/2020 Genap
 *   moderator, penguji 1, dan penguji 2 dapat entr nilai mahasiswa yang di-sidang
 *  * */
$mys->assign('namaModul', $namaModul);

$showFrm = "frmThAjar";
$dtyear = combothnajar();
$mys->assign('dtyear', $dtyear);

$combosemester = combosemester();
$mys->assign('combosemester', $combosemester);

if (!isset($_COOKIE["sidang_thnajar"])) {
    $sql_thn_cur = "select max(cthajar||decode(csmt,'O',1,'E',2,3)||csmt)
						from ttglvalid where ckdfak=:ckdfak and ckdmodul=:ckdmodul";
    $rs_thn = $cnora->Execute($sql_thn_cur, array('ckdfak' => '' . $_SESSION['kdfak_pad'], 'ckdmodul' => '02'));//'02' kode modul KPRS via Webdosen
    $data_smt['thncur'] = substr($rs_thn->fields[0], 0, 8);
    $data_smt['smtcur'] = substr($rs_thn->fields[0], -1);
} else {
    $data_smt['thncur'] = $_COOKIE["sidang_thnajar"];
    $data_smt['smtcur'] = $_COOKIE["sidang_smt"];
}

/*
$sql_thn_cur="select max(cthajar||decode(csmt,'O',1,'E',2,3)||csmt)
                    from ttglvalid where ckdfak=:ckdfak and ckdmodul=:ckdmodul";
$rs_thn = $cnora->Execute($sql_thn_cur,array('ckdfak'=>''.$_SESSION['kdfak_pad'],'ckdmodul'=>'02'));//'02' kode modul KPRS via Webdosen
$data_smt['thncur'] = substr($rs_thn->fields[0],0,8);
$data_smt['smtcur'] = substr($rs_thn->fields[0],-1);
*/
//print_r($data_smt);
$mys->assign('thajarAktif', $data_smt['thncur']);
$mys->assign('smtAktif', $data_smt['smtcur']);

$dtsidangke = getDataSidangke();
$dtfakultas = getDataFakultas();

//cetakArray($dtfakultas , "fakultas");
$dtprodi = getDataProdi($_SESSION['slogin'][1], $dtfakultas[0][0]);

$tsidang = "tsidang";
$cekData = array();

//cetakArray($dtsidangke, "sidangke");

// Batas akhir Header

/*
 * Batas Awal Core
 */


$arrJam = array(1 => "08:00", 2 => "09:30", 3 => "11:00", 4 => "13:30", 5 => "15:00", 6 => "16:30");
$arrJamAkhir = array(1 => "10:00", 2 => "11:30", 3 => "13:00", 4 => "15:30", 5 => "17:00", 6 => "18:30");


/*
 * menangani action Form Pilih tahun ajaran semester
 * ---> batas awalaaaaaaa
 */
if (isset($_POST['action']) && $_POST['action'] == "tampil") {
    $thnajar = $_POST['thnajar'];
    $smt = $_POST['smt'];
    //$stat = $_POST['status'];
    $stat = "";
    $csidangke = $_POST['sidangke'];
    $ckdfak = $_POST['fakultas'];
    $ckdprogst = substr($_POST['prodi'], 0, 2);
    $ckdjen = substr($_POST['prodi'], 2, 2);

    if ($ckdfak == "01" && $ckdjen == "30") $ckdfak = "33";

    simpanCookies($_POST);

    if ($ckdfak == "03") {
        $tsidang = "tsidangfe";
    }

    $semester = "";
    switch ($smt) {
        case "O" :
            $semester = "Gasal";
            break;
        case "E" :
            $semester = "Genap";
            break;
        case "A" :
            $semester = "Antara";
            break;
        case "L" :
            $semester = "Remedial Gasal";
            break;
        case "R" :
            $semester = "Remedial Genap";
            break;
        default :
            $semester = "";
    }
    $tahunajar = substr($thnajar, 0, 4) . '/' . substr($thnajar, -4);
    $cek = "0";
    //$cnora->debug=true;
    /*
     * query untuk mengambil data mahasiswa yang disidang
     */
    $sqlDataMhs = "select b.cnim, c.cnama, a.cnojadwal, "
        . "case a.cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status, "
        . "to_char(b.dtglsidang,'DD MON YYYY') tgl, b.cnomeja, b.cjam, b.dtglsidang, a.cstatus, b.cjenis, c.ckdjen, cmeet "
        . ", to_char(b.dtglsidang,'DY') hari "
        . "from tpenguji A, tjadsidang B, mmahasiswa C "
        . "where a.cnojadwal=b.cnojadwal "
        . "and a.ckddos=:CKDDOS "
        . "and b.cthajar=:CTHAJAR "
        . "and b.csmt=:CSMT "
        . "and b.csidangke=:CSIDANGKE "
        . "and b.ckdfak=:CKDFAK "
        . "and c.ckdjen=:CKDJEN "
        . "and c.ckdprogst=:CKDPROGST "
        . "and b.cnim=c.cnim "
        . "union "
        . "select b.cnim, c.cnama, b.cnojadwal, "
        . "case a.cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status, "
        . "to_char(b.dtglsidang,'DD MON YYYY') tgl, b.cnomeja, b.cjam, b.dtglsidang, a.cstatus, b.cjenis, c.ckdjen, cmeet  "
        . ", to_char(b.dtglsidang,'DY') hari "
        . "from tjadsidang b, mmahasiswa c, $tsidang a, tberitaacara d "
        . "where b.cnim=c.cnim "
        . "and a.ckddos=:CKDDOS "
        . "and b.cthajar=:CTHAJAR "
        . "and b.csmt=:CSMT "
        . "and b.csidangke=:CSIDANGKE "
        . "and b.ckdfak=:CKDFAK "
        . "and d.CNOBA=a.cnoba "
        . "and b.cnojadwal=d.cnojadwal "
        . "and c.ckdjen=:CKDJEN "
        . "and c.ckdprogst=:CKDPROGST "
        . "order by dtglsidang, cjam, cnomeja";
    if ($ckdfak == "02")
        $sqlDataMhs = "select b.cnim, c.cnama, a.cnojadwal "
            . ", case a.cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
            . ", to_char(b.dtglsidang,'DD MON YYYY') tgl, b.cruang, b.cjam, b.dtglsidang, a.cstatus, '' cjenis, c.ckdjen, cmeet "
            . ", to_char(b.dtglsidang,'DY') hari "
            . "from tsidangast A, tjadsidangast B, mmahasiswa C "
            . "where a.CNOJADWAL=b.cnojadwal "
            . "and b.CNIM=c.cnim "
            . "and b.CTHAJAR=:CTHAJAR "
            . "and b.csmt=:CSMT "
            . "and b.CSIDANGKE=:CSIDANGKE "
            . "and b.CKDFAK=:CKDFAK "
            . "and a.ckddos=:CKDDOS "
            . "and c.ckdjen=:CKDJEN "
            . "and c.ckdprogst=:CKDPROGST "
            //."and a.cstatus like '". $stat."%'  "
            . "order by dtglsidang, cjam, cnomeja";
    //$cnora->debug=true;
    $rsDataMhs = $cnora->Execute($sqlDataMhs, array(
            'CTHAJAR' => $thnajar
        , 'CSMT' => $smt
        , 'CKDDOS' => $_SESSION['slogin'][1]
        , 'CSIDANGKE' => $csidangke
        , 'CKDFAK' => $ckdfak
        , 'CKDJEN' => $ckdjen
        , 'CKDPROGST' => $ckdprogst
        )
    );

    $tampil = "Belum ada jadwal sidang untuk $tahunajar $semester<br />Silahkan hubungi ";
    $tampil .= "Panitia Sidang / Sekretariat Fakultas / DAA terkait jadwal sidang ";

    if (!$rsDataMhs->EOF) {
        $tampil = "<mark>Note:<br /><ul>";
        $tampil .= "<li>Klik sub-judul untuk melihat detail</li>";
        $tampil .= "<li>Klik NIM atau Nama untuk memilih mahasiswa</li>";
        $tampil .= "<li>Klik Ruang untuk menuju ruang sidang online (Google Meet / Zoom / dll)</li>";
        $tampil .= "</ul></mark><br />";
        $tampil .= "<form method='post' name='fmy' action='secquest_check.php'>";
        $tampil .= "<input type='hidden' name='txttmp[]' value=''>";
        $tanggal = "-";

        //$tglSekarang = date("d M Y");
        //$date=date_create("2019-07-24");
        //$tglSekarang = strtoupper (date_format($date,"d M Y"));
        $tglSekarang = strtoupper(date("d M Y"));

        $i = 0;


        while (!$rsDataMhs->EOF) {

            // cek dulu, jadwalnya masih sesuai apa nggak
            $cnojadwal = $rsDataMhs->fields[2];
            $ckddos = $_SESSION['slogin'][1];
            $cstatus = $rsDataMhs->fields[8];
            $cjenis = $rsDataMhs->fields[9];
            $ckdjen = $rsDataMhs->fields[10];
            $cmeet = $rsDataMhs->fields[11];
            $cnim = $rsDataMhs->fields[0];
            $dy = $rsDataMhs->fields[12];
            $arrJam = getJamSesi($thnajar, $smt, $dy)['arrJam'];
            //if (cekJadwalSidang($cnojadwal, $ckddos, $cstatus, $csidangke, $thnajar, $smt, $cnim, $ckdfak) ) {

            $tglSidang = $rsDataMhs->fields[4];
            if ($tanggal != $tglSidang) {
                if ($tanggal != "-") {
                    $tampil .= "</table>";
                    $tampil .= "</details >";
                    $tampil .= "<br />";
                }
                $open = "";
                if ($tglSekarang == $tglSidang) $open = "open=''";
                $tampil .= "<details $open>
							<summary>
							<b>Jadwal Sidang Tanggal " . $tglSidang . "</b>
							</summary>
						";
                $tampil .= "<table border=1 width=100% cellpadding=10 cellspacing=2>";
                $tampil .= "<tr  bgcolor='#6490cb'>";
                $tampil .= "<th width=10%>Jam</th>";
                $tampil .= "<th width=7%>Ruang</th>";
                $tampil .= "<th width=15%>NIM</th>";
                $tampil .= "<th width=40%>Nama</th>";
                $tampil .= "<th width=20%>Status</th>";
                $tampil .= "</tr>";
                $tanggal = $tglSidang;
                $i = 0;
            }

            // datalink: thnajar;smt;nim;stat;
            $cnim = $rsDataMhs->fields[0];
            $cnojadwal = $rsDataMhs->fields[2];
            $cjam = $rsDataMhs->fields[6];
            $datalink = $thnajar . ";" . $smt . ";" . $cnim . ";" . $stat . ";" . $cnojadwal . ";" . $csidangke . ";" . $ckdfak . ";" . $cjenis . ";" . $ckdprogst . ";" . $ckdjen . ";" . $cmeet . ";" . $cjam . ";" . $tglSidang . ";" . $dy . ";";
            $link = base64_encode(encrypt_string($datalink)) . ";0;1;a;acNilaiTA.php;";
            //<a href='javascript:krmStat(\"{$dtIsiAjar[loop].LINK}\")'>

            $bgcolor = ($i % 2 == 0) ? "#fbffd5" : "#ffffff";
            $tampil .= "<tr bgcolor='" . $bgcolor . "'>";
            $tampil .= "<td align=center>" . $arrJam[$cjam] . "</td>";
            $tampil .= "<td align=center><a href='$cmeet' target='_blank'>Ruang " . $rsDataMhs->fields[5] . "</a></td>";
            $tampil .= "<td align=center><a href='javascript:krmStat(\"$link\")'>" . $rsDataMhs->fields[0] . "</a></td>";
            $tampil .= "<td align=left><a href='javascript:krmStat(\"$link\")'>" . $rsDataMhs->fields[1] . "</a></td>";
            $tampil .= "<td align=center>" . $rsDataMhs->fields[3] . "</td>";
            $tampil .= "</tr>";

            $i++;

            //}

            $rsDataMhs->MoveNext();
        }
        $tampil .= "</form>";
    }
    echo $tampil;
    exit(); // menggunakan exit agar tidak load semua html
}
/*
 * ---> batas akhir action Form Pilih tahun ajaran semester
 */


/*
 * menangani action pilih salah satu mahasiswa
 * ---> batas awaaaaaal
 */
if (isset($_POST['btninput']) && $_POST['btninput'] == "Verifikasi Security") {

    $showFrm = "entryNilai";

    $txttmp = $_POST['txttmp'][0];
    $txttmp1 = explode(";", $txttmp);
    $part = encrypt_string(base64_decode($txttmp1[0]), false);
    //$partArr = explode(";", encrypt_string(base64_decode($txttmp1[0]) ,false));//parameter kedua (false) menyatakan decrypt
    $partArr = explode(";", $part);

    //cetakArray($partArr, "partarray");

    simpanLog("sidang", $txttmp, "Dosen pilih mahasiswa: " . $part);

    $cthajar = $partArr[0];
    $csmt = $partArr[1];
    $cnim = $partArr[2];
    $stat = $partArr[3];
    $cnojadwal = $partArr[4];
    $csidangke = $partArr[5];
    $ckdfak = $partArr[6];
    $cjenis = $partArr[7];
    $ckdprogst = $partArr[8];
    $ckdjen = $partArr[9];
    $cjam = $partArr[11];
    $tglSidang = $partArr[12];
    $dy = $partArr[13];

    $ckddos = $_SESSION['slogin'][1];

    if ($ckdfak == "03") {
        $tsidang = "tsidangfe";
    }

    $cekData['statusBA'] = getStatusBa2($ckdfak, $cnim, $cnojadwal);

    $getStatus = getStatus($ckddos, $cthajar, $csmt, $cnojadwal, $ckdfak, $csidangke);

    $arrJam = getJamSesi($cthajar, $csmt, $dy)['arrJam'];
    $arrJamAkhir = getJamSesi($cthajar, $csmt, $dy)['arrJamAkhir'];

    $isJadwal = true;
    $errMsg = "";

    if (!cekJadwalSidang($cnojadwal, $ckddos, $getStatus[0], $csidangke, $cthajar, $csmt, $cnim, $ckdfak)) {
        $errMsg = "Jadwal Anda telah digantikan.";
        $isJadwal = false;
    }


    if ($errMsg == "" && !cekThajarSmt($cthajar, $csmt)) {
        $errMsg = "Sistem digunakan untuk tahun ajaran 2019/2020 Genap dan seterusnya";
        $isJadwal = false;
    }

    /*
    if(!cekJadSidang($cnojadwal, $cjam, $arrJam, $arrJamAkhir, $cthajar, $csmt, $cnim, $ckdfak)){
        $errMsg = "Entry nilai bisa dilakukan pada $tglSidang ".$arrJam[$cjam]." s/d ".$arrJamAkhir[$cjam];
        $isJadwal = false;
    }
    */


    if ($errMsg == "") {
        $dtmhs = getDataMhs($cnim, $cthajar, $csmt, $csidangke, $ckdfak, $ckdprogst);
        $getPenguji = getPenguji($cnojadwal, $ckdfak);

        $dtNilaiMhs = getNilaiMhs($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt);
        $frmNilai = getFormNilai($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $getStatus[0], $csidangke);
        //getBa($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $cstatus)
        $dtBa = getBa($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $getStatus[0]);
        $getLinkBa = getLinkBa($cthajar, $csmt, $csidangke, $ckdfak, $ckdjen, $ckdprogst, $cnim, $getStatus[0]);        //$dtPenguji = getDataPenguji($cnim, $cthajar, $csmt, $cnojadwal, $ckdfak, $cjenis);

    }

    //cetakArray($dtBa, "dtBa");

}
/*
 * ---> batas akhir action pilih salah satu mahasiswa
 */


/*
 * menangani action simpan nilai TA
 * ---> batas awaaaaaal
 */
// cetakArray($_POST, "POST");
if (isset($_POST['btnSimpanNilai']) && $_POST['btnSimpanNilai'] == "Simpan Nilai") {

    $showFrm = "entryNilai";

    $txttmp = $_POST['txttmp'];
    $txttmp1 = explode(";", $txttmp);
    $part = encrypt_string(base64_decode($txttmp1[0]), false);
    //$partArr = explode(";", encrypt_string(base64_decode($txttmp1[0]) ,false));//parameter kedua (false) menyatakan decrypt
    $partArr = explode(";", $part);

    //cetakArray($_POST, "_POST");

    $infoMsg = "";

    $cthajar = $partArr[0];
    $csmt = $partArr[1];
    $cnim = $partArr[2];
    $stat = $partArr[3];
    $cnojadwal = $partArr[4];
    $csidangke = $partArr[5];
    $ckdfak = $partArr[6];
    $cjenis = $partArr[7];
    $ckdprogst = $partArr[8];
    $ckdjen = $partArr[9];
    $cjam = $partArr[11];
    $tglSidang = $partArr[12];
    $dy = $partArr[13];

    $ckddos = $_SESSION['slogin'][1];

    $cnoba = getNoBa($cnojadwal, $ckdfak);

    $isJadwal = true;
    $errMsg = "";
    $infoMsg = "";

    $getStatus = getStatus($ckddos, $cthajar, $csmt, $cnojadwal, $ckdfak, $csidangke);
    $dtmhs = getDataMhs($cnim, $cthajar, $csmt, $csidangke, $ckdfak, $ckdprogst);

    $arrJam = getJamSesi($cthajar, $csmt, $dy)['arrJam'];
    $arrJamAkhir = getJamSesi($cthajar, $csmt, $dy)['arrJamAkhir'];


    $tsidang = "tsidang";
    if ($ckdfak == "03") {
        $tsidang = "tsidangfe";
    }


    $var = "NILAI" . $getStatus[0];
    $errMsg = "";

    if (!cekJadwalSidang($cnojadwal, $ckddos, $getStatus[0], $csidangke, $cthajar, $csmt, $cnim, $ckdfak)) {
        $errMsg = "Jadwal Anda telah digantikan.";
        $isJadwal = false;
    }


    if ($errMsg == "" && !cekThajarSmt($cthajar, $csmt)) {
        $errMsg = "Sistem digunakan untuk tahun ajaran 2019/2020 Genap dan seterusnya";
        $isJadwal = false;
    }

    /*
    if($errMsg=="" && !cekJadSidang($cnojadwal, $cjam, $arrJam, $arrJamAkhir, $cthajar, $csmt, $cnim, $ckdfak)){
        $errMsg = "Entry nilai bisa dilakukan pada $tglSidang ".$arrJam[$cjam]." s/d ".$arrJamAkhir[$cjam];
        $isJadwal = false;
    }
    */


    if ($errMsg == "") {

        if (getStatusBa($ckdfak, $cnoba, $cnojadwal) == "T") {
            $errMsg = "Berita Acara sudah dicetak, nilai tidak dapat diubah.";
        } else {

            // cari noba, kalo kosong, berarti belum ada, dan harus generate dulu
            //$cnoba = getNoBa($cnojadwal, $ckdfak);
            $jumlahsimpan = 0;

            if ($cnoba == "") { // nomor BA belum ada
                // batas awal simpan berita acara


                $cnoba = generateNoBa($ckdfak, $cthajar, $csmt);

                $sql = "insert into $tsidang (CNOBA, CKDDOS, CSTATUS, ";
                foreach ($_POST['fields'] as $key => $value) {
                    $sql .= $value . ", ";
                }
                //$sql = substr($sql, 0, -2);
                $sql .= "CREVISI) ";
                $sql .= "values (:CNOBA, :CKDDOS, :CSTATUS, ";
                foreach ($_POST['fields'] as $key => $value) {
                    $sql .= ":" . $value . ", ";
                }
                //$sql = substr($sql, 0, -2);
                $sql .= ":CREVISI) ";

                $datasql["CREVISI"] = $_POST['txtrevisi'];
                $datasql["CNOBA"] = $cnoba;
                $datasql["CKDDOS"] = $ckddos;
                $datasql["CSTATUS"] = $getStatus[0];
                foreach ($_POST['fields'] as $key => $value) {
                    $datasql[$value] = $_POST[$var][$key];
                }

                $datasimpan[$jumlahsimpan] = array("jenis" => "nilai", "sql" => $sql, "datasql" => $datasql, "keterangan" => "Simpan Nilai");
                $jumlahsimpan++;

                $dataNilai = getDataNilai($ckdfak, $cnojadwal, $cthajar, $csmt);
                $totalNilai = hitungNilai($dataNilai, $_POST['persentase'], $_POST['fields']);

                $dataJadwal = getJadwal($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt);
                $pesan = "Simpan Nilai ";

                if ($getStatus[0] == "MD") {
                    $pesan = "Simpan Nilai dan Berita Acara";
                    // kalo moderator, yang di simpan nilai akhir, grade, cket, calasan
                    $sql = "insert into tberitaacara "
                        . "(cnoba, cnojadwal, dtglba, cruang, cjam, cflag, nnilakhir, cgrade, cket, calasan )"
                        . "values ( :CNOBA, :CNOJADWAL, sysdate, :CRUANG, :CJAM, 'F', :NNILAKHIR, :CGRADE , :CKET, :CALASAN )";
                    $datasql = array("NNILAKHIR" => $_POST['nnilakhir']
                    , "CGRADE" => $_POST['cgrade']
                    , "CKET" => $_POST['cmbketerangan']
                    , "CALASAN" => $_POST['txtalasan']
                    , "CNOBA" => $cnoba
                    , "CNOJADWAL" => $cnojadwal
                    , "CRUANG" => $dataJadwal[2]
                    , "CJAM" => $dataJadwal[6]
                    );
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => "Simpan BA");
                    $jumlahsimpan++;

                    $sql = "update tdaftarul set cjudul=upper(:CJUDUL) where cnodaful||cnim=(select cnodaful||cnim from tjadsidang where cnojadwal=:CNOJADWAL and cnim=:CNIM)";
                    $datasql = array("CJUDUL" => $_POST['txtjudulta']
                    , "CNOJADWAL" => $cnojadwal
                    , "CNIM" => $cnim
                    );
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => "Ubah Judul (S)");
                    $jumlahsimpan++;

                } else {
                    // kalo penguji, yang di simpan nilai akhir, grade
                    $dtNilaiMhs = getNilaiMhs($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt);
                    $sql = "insert into tberitaacara "
                        . "(cnoba, cnojadwal, dtglba, cruang, cjam, cflag, nnilakhir, cgrade)"
                        . "values ( :CNOBA, :CNOJADWAL, sysdate, :CRUANG, :CJAM, 'F', :NNILAKHIR, :CGRADE )";
                    $datasql = array("NNILAKHIR" => $_POST['nnilakhir']
                    , "CGRADE" => $_POST['cgrade']
                    , "CNOBA" => $cnoba
                    , "CNOJADWAL" => $cnojadwal
                    , "CRUANG" => $dataJadwal[2]
                    , "CJAM" => $dataJadwal[6]
                    );
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => "Simpan BA");
                    $jumlahsimpan++;
                }


                if (simpanOracle2($jumlahsimpan, $datasimpan)) {
                    $infoMsg = "$pesan berhasil... ";
                    updateNoba($ckdfak); // kalo simpan berhasil, counternya diupdate
                } else {
                    $errMsg = "$pesan gagal...";
                }

                // batas akhir simpan berita acara
            } else {
                // batas awal simpan/ubah nilai dosen

                $simpannilai = "Simpan/Ubah Nilai ";
                // cari noba berdasarkan dosen, kalo kosong, berarti belum ada,
                // dan harus simpan ke tsidang
                if (getNoBaDosen($ckddos, $cnojadwal, $ckdfak) == "") {

                    $sql = "insert into $tsidang (CNOBA, CKDDOS, CSTATUS, ";
                    foreach ($_POST['fields'] as $key => $value) {
                        $sql .= $value . ", ";
                    }
                    //$sql = substr($sql, 0, -2);
                    $sql .= "CREVISI) ";
                    $sql .= "values (:CNOBA, :CKDDOS, :CSTATUS, ";
                    foreach ($_POST['fields'] as $key => $value) {
                        $sql .= ":" . $value . ", ";
                    }
                    //$sql = substr($sql, 0, -2);
                    $sql .= ":CREVISI) ";
                    $simpannilai = "Simpan ";

                } else { // udah pernah ada ba dengan dosen ini, makanya update ajah
                    $sql = "update $tsidang set ";
                    foreach ($_POST['fields'] as $key => $value) {
                        $sql .= $value . "=:" . $value . ", ";
                        //$sql .= $value."='".$var."$key' ";
                    }
                    //$sql = substr($sql, 0, -2);
                    $sql .= "crevisi=:CREVISI ";
                    $sql .= "where cnoba=:CNOBA and ckddos=:CKDDOS and cstatus=:CSTATUS";
                    $simpannilai = "Ubah ";

                }

                $datasql["CREVISI"] = $_POST['txtrevisi'];
                $datasql["CNOBA"] = $cnoba;
                $datasql["CKDDOS"] = $ckddos;
                $datasql["CSTATUS"] = $getStatus[0];
                foreach ($_POST['fields'] as $key => $value) {
                    $datasql[$value] = $_POST[$var][$key];
                }

                // untuk simpan/ubah nilai dari dosen moderator / penguji ke tabel tsidang
                $datasimpan[$jumlahsimpan] = array("jenis" => "nilai", "sql" => $sql, "datasql" => $datasql, "keterangan" => $simpannilai . " nilai (" . $getStatus[0] . ") ");
                $jumlahsimpan++;


                //$infoMsg= $simpannilai." nilai berhasil...";

                $dataNilai = getDataNilai($ckdfak, $cnojadwal, $cthajar, $csmt);

                $totalNilai = hitungNilai($dataNilai, $_POST['persentase'], $_POST['fields']);

                $totalNilai = round($totalNilai, 0);

                //echo "totalNilai: $totalNilai; grade: $grade;";

                if ($getStatus[0] == "MD") {
                    // kalo moderator, yang di ubah nilai akhir, grade, cket, calasan
                    switch ($_POST['cmbketerangan']) {
                        case"3":
                            $grade = "X";
                            break;
                        case"2":
                            $grade = "F";
                            break;
                        default:
                            $grade = getGrade($totalNilai);
                    }
                    $sql = "update tberitaacara set nnilakhir=:NNILAKHIR, cgrade=:CGRADE "
                        . ", CKET=:CKET, CALASAN=:CALASAN "
                        . "where cnoba=:CNOBA ";
                    $datasql = array("NNILAKHIR" => $totalNilai
                    , "CGRADE" => $grade
                    , "CKET" => $_POST['cmbketerangan']
                    , "CALASAN" => $_POST['txtalasan']
                    , "CNOBA" => $cnoba
                    );

                    // untuk ubah grade, keterangan dan alasan dari moderator
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => $simpannilai . " nilai (MD) ");
                    $jumlahsimpan++;

                    // query ubah judul di tdaftarul
                    $sql = "update tdaftarul set cjudul=upper(:CJUDUL) where cnodaful||cnim=(select cnodaful||cnim from tjadsidang where cnojadwal=:CNOJADWAL and cnim=:CNIM)";
                    $datasql = array("CJUDUL" => $_POST['txtjudulta']
                    , "CNOJADWAL" => $cnojadwal
                    , "CNIM" => $cnim
                    );
                    // query ubah judul di tdaftarul
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => "Ubah Judul (U)");
                    $jumlahsimpan++;

                } else {
                    // kalo penguji, yang di ubah nilai akhir, grade
                    $dtNilaiMhs = getNilaiMhs($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt);
                    // kalo cketnya selain 3 sama 2, maka gradenya dihitung ulang
                    switch ($dtNilaiMhs["cket"]) {
                        case "3":
                        case "2":
                            $grade = $dtNilaiMhs["grade"];
                            break;
                        default:
                            $grade = getGrade($totalNilai);
                    }
                    $sql = "update tberitaacara set nnilakhir=:NNILAKHIR, cgrade=:CGRADE "
                        . "where cnoba=:CNOBA ";
                    $datasql = array("NNILAKHIR" => $totalNilai
                    , "CGRADE" => $grade
                    , "CNOBA" => $cnoba
                    );

                    // untuk ubah total nilai dan grade dari ketua penguji / anggota penguji
                    $datasimpan[$jumlahsimpan] = array("jenis" => "ba", "sql" => $sql, "datasql" => $datasql, "keterangan" => $simpannilai . " nilai (" . $getStatus[0] . ") ");
                    $jumlahsimpan++;

                }

                if (simpanOracle2($jumlahsimpan, $datasimpan)) {
                    $infoMsg = "$simpannilai nilai berhasil... ";
                    updateNoba($ckdfak); // kalo simpan berhasil, counternya diupdate
                } else {
                    $errMsg = "$simpannilai nilai GAGAL... ";
                }

                // batas akhir simpan/ubah nilai dosen
            }

        } // batas akhir cek berita acara sudah simpan apa belum


        //$infoMsg= $sql;
        $dtNilaiMhs = getNilaiMhs($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt);
        $frmNilai = getFormNilai($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $getStatus[0], $csidangke);
        //getBa($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $cstatus)
        $dtBa = getBa($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $getStatus[0]);
        $getLinkBa = getLinkBa($cthajar, $csmt, $csidangke, $ckdfak, $ckdjen, $ckdprogst, $cnim, $getStatus[0]);
        //$dtPenguji = getDataPenguji($cnim, $cthajar, $csmt, $cnojadwal, $ckdfak, $cjenis);

    }
    $getPenguji = getPenguji($cnojadwal, $ckdfak);

    //cetakArray($dtBa, "dtBa");

}
/*
 * ---> batas akhir action simpan nilai TA
 */


/*
 * Batas Akhir Core
 */


/*
 * fungsi getLinkBa
 * digunakan untuk generate link untuk form lihat BA
 */
function getLinkBa($thnajar, $smt, $csidangke, $ckdfak, $ckdjen, $ckdprogst, $cnim, $stat)
{
    global $cnora, $tsidang;
    $link = "#";
    $datasql = array('CTHAJAR' => $thnajar
    , 'CSMT' => $smt
    , 'CKDDOS' => $_SESSION['slogin'][1]
    , 'CSIDANGKE' => $csidangke
    , 'CKDFAK' => $ckdfak
    , 'CKDJEN' => $ckdjen
    , 'CKDPROGST' => $ckdprogst
    , 'CSTAT' => $stat
    , 'CNIM' => $cnim
    );
    $sqlDataMhs = "select b.cnim, c.cnama, b.cnojadwal "
        . ", to_char(dtglsidang,'YYYY-MM-DD-DY') tglsidang, d.cnoba, to_char(d.dtglba,'YYYY-MM-DD-DY') tglba "
        . ", to_char(d.dtglba + 14, 'YYYY-MM-DD-DY') as tglbatas "
        . ", case upper(substr(cgrade,1,1)) when 'A' then 'LULUS' when 'B' then 'LULUS' when 'C' then 'LULUS' else 'TIDAK LULUS' end as hasil "
        . ", case cket when '1' then 'Syarat Terpenuhi' when '2' then 'Persyaratan Sidang Tidak Terpenuhi' when '3' then 'Mahasiswa Tidak Hadir' else '' end as keterangan "
        . ", calasan, u.CJUDUL, u.ckddos, gelarpegawai(u.ckddos) pembimbing, d.NNILAKHIR, d.CGRADE "
        . "from tjadsidang b, mmahasiswa c, $tsidang a, tberitaacara d, tdaftarul u  "
        . "where b.cnim=c.cnim "
        . "and a.ckddos=:CKDDOS "
        . "and b.cthajar=:CTHAJAR "
        . "and b.csmt=:CSMT "
        . "and b.csidangke=:CSIDANGKE "
        . "and b.ckdfak=:CKDFAK "
        . "and d.CNOBA=a.cnoba "
        . "and b.cnodaful=u.cnodaful and u.cnim=b.cnim "
        . "and b.cnojadwal=d.cnojadwal "
        //."and d.cflag='T'  "
        . "and c.ckdjen=:CKDJEN "
        . "and c.ckdprogst=:CKDPROGST "
        . "and a.cstatus like :CSTAT "
        . "order by dtglsidang, c.cnama"
        . "";
    //$cnora->debug=true;
    $rsDataMhs = $cnora->Execute($sqlDataMhs, $datasql);
    if (!$rsDataMhs->EOF) {

        $cnim = $rsDataMhs->fields[0];
        $cnama = $rsDataMhs->fields[1];
        $tglsidang = tanggal_indo($rsDataMhs->fields[3]);
        $cnoba = $rsDataMhs->fields[4];
        $tglbatas = tanggal_indo($rsDataMhs->fields[6]);
        $hasil = $rsDataMhs->fields[7];
        $ket = $rsDataMhs->fields[8];
        $alasan = $rsDataMhs->fields[9];
        $judul = $rsDataMhs->fields[10];
        $pembimbing = $rsDataMhs->fields[12];
        $nilakhir = $rsDataMhs->fields[13];
        $grade = $rsDataMhs->fields[14];
        $cnojadwal = $rsDataMhs->fields[2];

        $datalink = $cnim . ";" . $cnama . ";" . $tglsidang . ";" . $cnoba . ";" . $tglbatas . ";" . $hasil . ";" . $ket . ";" . $alasan . ";" . $judul . ";" . $pembimbing . ";" . $nilakhir . ";" . $grade . ";" . $cnojadwal . ";" . $ckdfak . ";";
        $link = base64_encode(encrypt_string($datalink)) . ";0;1;a;acBaSidang.php.php;";

    }

    return $link;
}

/*
 * Batas Akhir fungsi getLinkBa ------------------------------
 */


/*
 * fungsi cekJadwalSidang
 * digunakan untuk cek apakah jadwal sidang
 *    sudah digantikan orang lain atau belum
 * nilai kembali: TRUE atau FALSE
 * TRUE >> jika jadwal sesuai, berarti belum digantikan
 * FALSE >> jika jadwal sudah tidak sesuai, berarti sudah digantikan atau sudah daftar sidang 2
 */
function cekJadwalSidang($cnojadwal, $ckddos, $cstatus, $csidangke, $cthajar, $csmt, $cnim, $ckdfak)
{
    global $cnora, $tsidang;
    $cek = true;

    // yang di cek fakultas selain ASTRI
    if ($ckdfak != "02") {
        $query = "select ckddos from $tsidang s, tberitaacara b "
            . "where s.cnoba=b.cnoba "
            . "and b.cnojadwal=:CNOJADWAL "
            . "and cstatus=:CSTATUS";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        , 'CSTATUS' => $cstatus
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        if (!$rsSidang->EOF) {
            if ($rsSidang->fields[0] != $ckddos) {
                $cek = false;
            }
            //echo "masuk sini....:$cek <br />";
        }

    }

    //echo "kembali....:$cek <br />";
    //echo "$cnojadwal, $ckddos, $cstatus : $cek <br />";

    return $cek;
}


/*
 * fungsi cekJadSidang
 * digunakan untuk cek apakah jadwal sidang
 *    sesuai dengan sesi jadwal
 * nilai kembali: TRUE atau FALSE
 * TRUE >> jika jadwal sesuai, berada di dalam range sesi jadwal
 * FALSE >> jika jadwal sudah tidak sesuai, berada di luar range sesi jadwal
 */
function cekJadSidang($cnojadwal, $cjam, $arrJam, $arrJamAkhir, $cthajar, $csmt, $cnim, $ckdfak)
{
    global $cnora;
    $cek = true;
    //$cnora->debug=true;

    $jamAwal = $arrJam[$cjam];
    $jamAkhir = $arrJamAkhir[$cjam];

    // yang di cek fakultas selain ASTRI
    if ($ckdfak != "02") {
        $query = "select count(a.cnim) from tjadsidang a "
            . "where cnojadwal=:CNOJADWAL "
            . "and cnim=:CNIM "
            . "and cthajar=:CTHAJAR "
            . "and csmt=:CSMT "
            . "and cnim=:CNIM "
            . "and sysdate between  "
            . "to_date(to_char(a.dtglsidang,'YYYY-MM-DD')||' $jamAwal','YYYY-MM-DD HH24:MI') "
            . "and "
            . "to_date(to_char(a.dtglsidang,'YYYY-MM-DD')||' $jamAkhir','YYYY-MM-DD HH24:MI') "
            . "";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        , 'CTHAJAR' => $cthajar
        , 'CSMT' => $csmt
        , 'CNIM' => $cnim
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        if (!$rsSidang->EOF) {
            if ($rsSidang->fields[0] > 0) {
                $cek = true;
            } else {
                $cek = false;
            }
            //echo "masuk sini....:$cek <br />";
        }

    }


    return $cek;
}


/*
 * fungsi cekJadwalSidangKe2
 * 1. digunakan untuk cek apakah jadwal sidang
 *    sudah digantikan orang lain atau belum
 * 2. kalo udah daftar sidang ke 2, maka gak muncul di sidang 1
 * 3. kalo udah daftar sidang desain ke 2, maka gak muncul di sidang desain ke 1
 * nilai kembali: TRUE atau FALSE
 * TRUE >> jika jadwal sesuai, berarti belum digantikan
 * FALSE >> jika jadwal sudah tidak sesuai, berarti sudah digantikan atau sudah daftar sidang 2
 */
function cekJadwalSidangKe2($cnojadwal, $ckddos, $cstatus, $csidangke, $cthajar, $csmt, $cnim, $ckdfak)
{
    global $cnora, $tsidang;
    $cek = true;

    // yang di cek fakultas selain ASTRI
    if ($ckdfak != "02") {
        $query = "select ckddos from $tsidang s, tberitaacara b "
            . "where s.cnoba=b.cnoba "
            . "and b.cnojadwal=:CNOJADWAL "
            . "and cstatus=:CSTATUS";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        , 'CSTATUS' => $cstatus
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        if (!$rsSidang->EOF) {
            if ($rsSidang->fields[0] != $ckddos) {
                $cek = false;
            }
            //echo "masuk sini....:$cek <br />";
        }

        // cek
        if ($cek && $csidangke == "1") {
            //echo "masuk sini";

            $query = "select count(*) from tjadsidang "
                . "where cnim=:CNIM "
                . "and cthajar=:CTHAJAR "
                . "and csmt=:CSMT "
                . "and csidangke='2'";
            $dataquery = array(
                'CNIM' => $cnim
            , 'CTHAJAR' => $cthajar
            , 'CSMT' => $csmt
            );

            $rsSidang = $cnora->Execute($query, $dataquery);
            if ($rsSidang->fields[0] > 0) {
                $cek = false;
            }

        }

        if ($cek && $csidangke == "3") {

            $query = "select count(*) from tjadsidang "
                . "where cnim=:CNIM "
                . "and cthajar=:CTHAJAR "
                . "and csmt=:CSMT "
                . "and csidangke='4'";
            $dataquery = array(
                'CNIM' => $cnim
            , 'CTHAJAR' => $cthajar
            , 'CSMT' => $csmt
            );

            $rsSidang = $cnora->Execute($query, $dataquery);
            if ($rsSidang->fields[0] > 0) {
                $cek = false;
            }

        }
    }

    //echo "kembali....:$cek <br />";
    //echo "$cnojadwal, $ckddos, $cstatus : $cek <br />";

    return $cek;
}

/*
 * fungsi getFak
 * digunakan untuk mencari status dosen
 * berada di fakultas mana
 */
function getFak($ckddos, $cthajar, $csmt)
{
    global $cnora, $tsidang;
    $data = "";

    $query = "select cstatus "
        . ", case cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
        . "from $tsidang s, tberitaacara b "
        . "where s.cnoba=b.cnoba "
        . "and b.cnojadwal=:CNOJADWAL "
        . "and ckddos=:CKDDOS";
    $dataquery = array(
        'CNOJADWAL' => $cnojadwal
    , 'CKDDOS' => $ckddos
    );

    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        $data[] = $rsSidang->fields[0];
        $data[] = $rsSidang->fields[1];
    } else {

        $query = "select cstatus "
            . ", case cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
            . "from tjadsidang b, tpenguji p "
            . "where b.CNOJADWAL=p.CNOJADWAL "
            . "and b.cnojadwal=:CNOJADWAL "
            . "and ckddos=:CKDDOS";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        , 'CKDDOS' => $ckddos
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        if (!$rsSidang->EOF) {
            $data[] = $rsSidang->fields[0];
            $data[] = $rsSidang->fields[1];
        }

    }

    return $data;
}


/*
 * fungsi getJenistaMhs
 * digunakan untuk mencari jenis Tugas Akhir
 * untuk mahasiswa fikom
 * nilai kembali: ckdprogst, ckdminat, ckdjenis, ckdmetode
 */
function getJenistaMhs($cnim, $cthajar, $csmt)
{
    global $cnora, $tsidang;
    $data['ckdprogst'] = "";
    $data['ckdminat'] = "";
    $data['ckdjenis'] = "";
    $data['ckdmetode'] = "";

    $query = "select m.ckdprogst, m.ckdminat, j.ckdjenis, j.ckdmetode  "
        . "from mmahasiswa m, tjenista j "
        . "where m.cnim=j.cnim "
        . "and m.cnim=:cnim "
        . "and j.cthajar=:cthajar "
        . "and j.csmt=:csmt";
    $dataquery = array(
        'cnim' => $cnim
    , 'cthajar' => $cthajar
    , 'csmt' => $csmt
    );

    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        $data['ckdprogst'] = $rsSidang->fields[0];
        $data['ckdminat'] = $rsSidang->fields[1];
        $data['ckdjenis'] = $rsSidang->fields[2];
        $data['ckdmetode'] = $rsSidang->fields[3];
    }
    return $data;
}


/*
 * fungsi getStatus
 * digunakan untuk mencari status dosen
 * apakah moderator
 * atau penguji 1 (ketua penguji)
 * atau penguji 2 (anggota penguji)
 */
function getStatus($ckddos, $cthajar, $csmt, $cnojadwal, $ckdfak, $csidangke)
{
    global $cnora, $tsidang;
    $data = array();

    $query = "select cstatus "
        . ", case cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
        . "from $tsidang s, tberitaacara b "
        . "where s.cnoba=b.cnoba "
        . "and b.cnojadwal=:CNOJADWAL "
        . "and ckddos=:CKDDOS";

    if ($ckdfak == "02") {
        $query = "select cstatus "
            . ", case cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
            . "from tsidangast b "
            . "where b.cnojadwal=:CNOJADWAL "
            . "and ckddos=:CKDDOS";
    }

    $dataquery = array(
        'CNOJADWAL' => $cnojadwal
    , 'CKDDOS' => $ckddos
    );

    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        $data[] = $rsSidang->fields[0];
        $data[] = $rsSidang->fields[1];
    } else {

        $query = "select cstatus "
            . ", case cstatus when 'MD' then 'Moderator' when 'P1' then 'Ketua Penguji' when 'P2' then 'Anggota Penguji' end as status "
            . "from tjadsidang b, tpenguji p "
            . "where b.CNOJADWAL=p.CNOJADWAL "
            . "and b.cnojadwal=:CNOJADWAL "
            . "and ckddos=:CKDDOS";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        , 'CKDDOS' => $ckddos
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        if (!$rsSidang->EOF) {
            $data[] = $rsSidang->fields[0];
            $data[] = $rsSidang->fields[1];
        }

    }

    return $data;
}


/*
 * fungsi getNoBa
 * digunakan untuk mengambil nomor berita acara yang sudah disimpan
 * berdasarkan nomor jadwal
 */
function getNoBa($cnojadwal, $ckdfak)
{
    global $cnora, $tsidang;
    $cnoba = "";

    $query = "select cnoba "
        . "from tberitaacara "
        . "where cnojadwal=:CNOJADWAL "
        . "order by dtglba desc ";
    $dataquery = array(
        'CNOJADWAL' => $cnojadwal
    );

    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        $cnoba = $rsSidang->fields[0];
    }

    return $cnoba;
}


/*
 * fungsi getNoBaDosen
 * digunakan untuk mengambil nomor berita acara yang sudah disimpan
 * berdasarkan nomor jadwal
 */
function getNoBaDosen($ckddos, $cnojadwal, $ckdfak)
{
    global $cnora, $tsidang;
    $cnoba = "";

    $query = "select b.cnoba "
        . "from $tsidang s, tberitaacara b "
        . "where s.cnoba=b.cnoba "
        . "and b.cnojadwal=:CNOJADWAL "
        . "and ckddos=:CKDDOS";

    if ($ckdfak == "02") {
        $query = "select b.cnoba "
            . "from tsidangast b "
            . "where b.cnojadwal=:CNOJADWAL "
            . "and ckddos=:CKDDOS";
    }

    $dataquery = array(
        'CNOJADWAL' => $cnojadwal
    , 'CKDDOS' => $ckddos
    );
    //$cnora->debug=true;
    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        $cnoba = $rsSidang->fields[0];
    }
    return $cnoba;
}


/*
 * fungsi getDataMhs
 * digunakan untuk mencari data mahasiswa
 * sesuai mahasiswa yang dipilih
 */
function getDataMhs($cnim, $cthajar, $csmt, $csidangke, $ckdfak, $ckdprogst)
{
    global $cnora, $cekData;
    $data = array();

    switch ($ckdfak) {
        case "02":
            $query = "select x.*, y.cjudul juduls from(  "
                . "select m.cnim, m.cnama, initcap(p.CNMPROGST), u.CJUDUL, '' cjudul_ta, hitipk(m.cnim), gelarpegawai(u.ckddos), u.cthajar, u.csmt  "
                . "from tbimbingast u, mmahasiswa m, mprodi p "
                . "where u.cnim=m.cnim "
                . "and m.ckdprogst=p.ckdprogst "
                . "and m.ckdjen=p.ckdjen "
                . "and m.cnim=:CNIM "
                . "and u.cthajar=:CTHAJAR "
                . "and u.csmt=:CSMT) x "
                . "left join tjudulskripsi y "
                . "on x.cnim||x.cthajar||x.csmt=y.cnim||y.cthajar||y.csmt "
                . "";
            break;
        case "07":
            $query = "select x.*, y.cjudul juduls from(  "
                . "select m.cnim, m.cnama, initcap(p.CNMPROGST), u.CJUDUL , j.cjudul_ta, hitipk(m.cnim), gelarpegawai(u.ckddos), b.cthajar, b.csmt  "
                . "from tdaftarbt b, tdaftarul u, mmahasiswa m, mprodi p, tjenista j "
                . "where b.CNODAFTAR=u.CNODAFTAR "
                . "and b.CNIM=u.CNIM "
                . "and u.cnim=m.cnim "
                . "and m.ckdprogst=p.ckdprogst "
                . "and m.ckdjen=p.ckdjen "
                . "and b.cflagbtl='F' "
                . "and m.cnim=:CNIM "
                . "and b.cthajar=:CTHAJAR "
                . "and b.csmt=:CSMT "
                . "and b.cthajar=j.cthajar "
                . "and b.csmt=j.csmt "
                . "and b.cnim=j.cnim "
                . "and b.csmt=:CSMT) x "
                . "left join tjudulskripsi y "
                . "on x.cnim||x.cthajar||x.csmt=y.cnim||y.cthajar||y.csmt ";
            break;
        default:
            $query = "select x.*, y.cjudul juduls from(  "
                . "select m.cnim, m.cnama, initcap(p.CNMPROGST), u.CJUDUL, '' cjudul_ta, hitipk(m.cnim), gelarpegawai(u.ckddos), cthajar, csmt "
                . "from tdaftarbt b, tdaftarul u, mmahasiswa m, mprodi p "
                . "where b.CNODAFTAR=u.CNODAFTAR "
                . "and b.CNIM=u.CNIM "
                . "and u.cnim=m.cnim "
                . "and m.ckdprogst=p.ckdprogst "
                . "and m.ckdjen=p.ckdjen "
                . "and b.cflagbtl='F' "
                . "and m.cnim=:CNIM "
                . "and b.cthajar=:CTHAJAR "
                . "and b.csmt=:CSMT  "
                . "union  "
                . "select m.cnim, m.cnama, initcap(p.CNMPROGST), u.CJUDUL, '' cjudul_ta, hitipk(m.cnim), gelarpegawai(u.ckddos), cthajar, csmt   "
                . "from tdaftarbtd3 b, tdaftarul u, mmahasiswa m, mprodi p "
                . "where b.CNODAFTAR=u.CNODAFTAR "
                . "and b.CNIM=u.CNIM "
                . "and u.cnim=m.cnim "
                . "and m.ckdprogst=p.ckdprogst "
                . "and b.cflagbtl='F' "
                . "and m.cnim=:CNIM "
                . "and b.cthajar=:CTHAJAR "
                . "and b.csmt=:CSMT) x "
                . "left join tjudulskripsi y "
                . "on x.cnim||x.cthajar||x.csmt=y.cnim||y.cthajar||y.csmt "
                . " ";
    }


    $dataquery = array(
        'CNIM' => $cnim
    , 'CTHAJAR' => $cthajar
    , 'CSMT' => $csmt
    );
    //$cnora->debug=true;
    $rsMhs = $cnora->Execute($query, $dataquery);
    if (!$rsMhs->EOF) {
        $data[] = array("NIM", $rsMhs->fields[0]);
        $data[] = array("Nama", $rsMhs->fields[1]);
        $data[] = array("Program Studi", $rsMhs->fields[2]);
        $data[] = array("IPK", $rsMhs->fields[5]);
        $data[] = array("Pembimbing", $rsMhs->fields[6]);

        $judulta = $rsMhs->fields[3];
        $cekData['judulTa'] = true;
        if ($rsMhs->fields[3] == "") {
            $judulta = $rsMhs->fields[4];
            $cekData['judulTa'] = false;
        }
        $judulta = ($judulta == "") ? $rsMhs->fields[9] : $judulta;

        $data[] = array("Judul TA", $judulta);
    }


    $data[] = array("Sidang ke", getSidangke($ckdprogst, $csidangke));

    return $data;
}


/*
 * fungsi getFormNilai
 * digunakan untuk cek apakah jadwal sidang
 * sudah digantikan orang lain atau belum
 * nilai kembali: TRUE atau FALSE
 * TRUE >> jika jadwal sesuai, berarti belum digantikan
 * TRUE >> jika jadwal sudah tidak sesuai, berarti sudah digantikan
 */
function getFormNilai($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $cstatus, $csidangke)
{
    global $cnora;
    $data = array();

    if ($ckdfak == "33") $ckdfak = "01";

    $dtNilai = getDataNilai($ckdfak, $cnojadwal, $cthajar, $csmt);

    $query = "select ckdprogst, ckdminat, ckdjen from mmahasiswa where cnim=:CNIM ";
    $dataquery = array('CNIM' => $cnim);

    $rsMhs = $cnora->Execute($query, $dataquery);

    $ckdprogst = $rsMhs->fields[0];
    $ckdminat = $rsMhs->fields[1];
    $ckdjen = $rsMhs->fields[2];

    //pisahkan per fakultas
    switch ($ckdfak) {
        case "01":
            // pisahkan per jenjang
            switch ($ckdjen) {
                case "30": // Jenjang D3
                    $data[] = array("Penguasaan Materi", 0.25
                    , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                    , "NMATERI"); // MATE
                    $data[] = array("Pemrograman", 0.50
                    , $dtNilai["MD"]["NKPROG"], $dtNilai["P1"]["NKPROG"], $dtNilai["P2"]["NKPROG"]
                    , "NKPROG"); // KUASA
                    $data[] = array("Penulisan", 0.20
                    , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                    , "NTULIS"); // TULIS
                    $data[] = array("Presentasi", 0.05
                    , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                    , "NSAJI"); // SAJI
                    break;
                case "50": // Jenjang S1

                    // pisahkan per prodi di jenjang S1
                    switch ($ckdprogst) {
                        case "11": // TI
                            $data[] = array("Presentasi + Demo", 0.15
                            , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                            , "NSAJI");  // SAJI
                            $data[] = array("Penulisan", 0.20
                            , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                            , "NTULIS");  // TULIS
                            $data[] = array("Penguasaan Materi", 0.40
                            , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                            , "NMATERI");  // MATERI
                            $data[] = array("Penguasaan Program", 0.25
                            , $dtNilai["MD"]["NPROG"], $dtNilai["P1"]["NPROG"], $dtNilai["P2"]["NPROG"]
                            , "NPROG");  // PROG
                            break;
                        case "12": // SI
                            $data[] = array("Presentasi", 0.05
                            , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                            , "NSAJI"); // SAJI
                            $data[] = array("Penulisan", 0.20
                            , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                            , "NTULIS"); // TULIS
                            $data[] = array("Penguasaan Materi", 0.35
                            , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                            , "NMATERI"); // MATERI
                            $data[] = array("Penguasaan Program / Prototipe", 0.30
                            , $dtNilai["MD"]["NKPROG"], $dtNilai["P1"]["NKPROG"], $dtNilai["P2"]["NKPROG"]
                            , "NKPROG"); // KUASA
                            $data[] = array("Kompleksitas", 0.10
                            , $dtNilai["MD"]["NPROG"], $dtNilai["P1"]["NPROG"], $dtNilai["P2"]["NPROG"]
                            , "NPROG"); // DEMO
                            break;
                        case "13": // SK
                            $data[] = array("Demo Alat", 0.20
                            , $dtNilai["MD"]["NKPROG"], $dtNilai["P1"]["NKPROG"], $dtNilai["P2"]["NKPROG"]
                            , "NKPROG");  // MATERI
                            $data[] = array("Presentasi", 0.05
                            , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                            , "NSAJI");  // TULIS
                            $data[] = array("Penulisan", 0.25
                            , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                            , "NTULIS");  // SAJI
                            $data[] = array("Penguasaan Materi", 0.35
                            , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                            , "NMATERI");  // KUASA
                            $data[] = array("Penguasaan Program", 0.15
                            , $dtNilai["MD"]["NPROG"], $dtNilai["P1"]["NPROG"], $dtNilai["P2"]["NPROG"]
                            , "NPROG");  // DEMO
                            break;
                    }
                    break;
                // akhir Jenjang S1
            } // akhir dari pisahkan per jenjang

            break; // akhir FAK FTI

        case "03": // FEB
            $data[] = array("Skripsi", 0.50
            , $dtNilai["MD"]["NORIG"], $dtNilai["P1"]["NORIG"], $dtNilai["P2"]["NORIG"]
            , "NORIG");
            $data[] = array("Komprehensif", 0.5
            , $dtNilai["MD"]["NBHS"], $dtNilai["P1"]["NBHS"], $dtNilai["P2"]["NBHS"]
            , "NBHS");
            break; // akhir FAK FEB

        case "04": // FISIP
            $data[] = array("Presentasi", 0.20
            , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
            , "NMATERI");
            $data[] = array("Penulisan + Metodologi", 0.3
            , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
            , "NSAJI");
            $data[] = array("Penguasaan Materi", 0.5
            , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
            , "NTULIS");
            break; // akhir FAK FISIP

        case "05": // FT
            // pisahkan per prodi di jenjang S1
            switch ($ckdprogst) {
                case"51": // prodi arsitektur
                    switch ($csidangke) {
                        case "1":
                        case "2":
                            $n1 = "PENILAIAN";
                            $n2 = "Presentasi";
                            $n3 = "Penguasaan Materi";
                            break;
                        case "3":
                        case "4":
                            $n1 = "Analisa";
                            $n2 = "Konsep";
                            $n3 = "Penguasaan Materi";
                            break;
                    }
                    $data[] = array($n1, 0.70
                    , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                    , "NMATERI");
                    $data[] = array($n2, 0.15
                    , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                    , "NSAJI");
                    $data[] = array($n3, 0.15
                    , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                    , "NTULIS");
                    break; // batas prodi arsitektur

                case"52": // prodi elektro
                    $data[] = array("PENILAIAN", 0.30
                    , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
                    , "NMATERI");
                    $data[] = array("Presentasi", 0.20
                    , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
                    , "NSAJI");
                    $data[] = array("Penguasaan Materi", 0.50
                    , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
                    , "NTULIS");
                    break; // batas prodi arsitektur
            } // akhir pemisahan per prodi
            break; // akhir FAK FT

        case "07": // FIKOM
            $data[] = array("Kemampuan Presentasi dan Menjawab", 0.3
            , $dtNilai["MD"]["NSAJI"], $dtNilai["P1"]["NSAJI"], $dtNilai["P2"]["NSAJI"]
            , "NSAJI");
            $data[] = array("Penulisan", 0.3
            , $dtNilai["MD"]["NTULIS"], $dtNilai["P1"]["NTULIS"], $dtNilai["P2"]["NTULIS"]
            , "NTULIS");
            $data[] = array("Isi Materi", 0.4
            , $dtNilai["MD"]["NMATERI"], $dtNilai["P1"]["NMATERI"], $dtNilai["P2"]["NMATERI"]
            , "NMATERI");
            break; // akhir FAK FISIP

    } // akhir pemisahan perfakultas


    return $data;
}


/*
 * fungsi getDataNilai
 * digunakan untuk memberikan mengambil data nilai
 * yang usdah di-entry oleh moderator dan penguji
 */
function getDataNilai($ckdfak, $cnojadwal, $cthajar, $csmt)
{
    global $cnora;
    $data["MD"] = array("NORIG" => "999"
    , "NBHS" => "999"
    , "NSAJI" => "999"
    , "NTULIS" => "999"
    , "NMATERI" => "999"
    , "NPROG" => "999"
    , "NKPROG" => "999"
    , "NDATA" => "999"
    , "NPROSES" => "999"
    );
    $data["P1"] = array("NORIG" => "999"
    , "NBHS" => "999"
    , "NSAJI" => "999"
    , "NTULIS" => "999"
    , "NMATERI" => "999"
    , "NPROG" => "999"
    , "NKPROG" => "999"
    , "NDATA" => "999"
    , "NPROSES" => "999"
    );
    $data["P2"] = array("NORIG" => "999"
    , "NBHS" => "999"
    , "NSAJI" => "999"
    , "NTULIS" => "999"
    , "NMATERI" => "999"
    , "NPROG" => "999"
    , "NKPROG" => "999"
    , "NDATA" => "999"
    , "NPROSES" => "999"
    );
    switch ($ckdfak) {
        case "03":
            $sql = "select s.* "
                . "from tjadsidang j, tberitaacara b, tsidangfe s "
                . "where j.cnojadwal=b.cnojadwal  "
                . "and b.cnoba=s.cnoba "
                . "and j.cthajar=:cthajar "
                . "and j.csmt=:csmt "
                . "and j.cnojadwal=:cnojadwal "
                . "order by cstatus";
            $rs = $cnora->Execute($sql, array(
                'cnojadwal' => $cnojadwal
            , 'cthajar' => $cthajar
            , 'csmt' => $csmt
            ));

            if (!$rs->EOF) {
                while (!$rs->EOF) {
                    $data[$rs->fields[2]] = array(
                        "NORIG" => $rs->fields[3]
                    , "NBHS" => $rs->fields[8]
                    );
                    $rs->MoveNext();
                }
            }

            break;
        default:

            $sql = "select s.* "
                . "from tjadsidang j, tberitaacara b, tsidang s "
                . "where j.cnojadwal=b.cnojadwal  "
                . "and b.cnoba=s.cnoba "
                . "and j.cthajar=:cthajar "
                . "and j.csmt=:csmt "
                . "and j.cnojadwal=:cnojadwal "
                . "order by cstatus";
            $rs = $cnora->Execute($sql, array(
                'cnojadwal' => $cnojadwal
            , 'cthajar' => $cthajar
            , 'csmt' => $csmt
            ));

            if (!$rs->EOF) {
                while (!$rs->EOF) {
                    $data[$rs->fields[2]] = array(
                        "NSAJI" => $rs->fields[3]
                    , "NTULIS" => $rs->fields[4]
                    , "NMATERI" => $rs->fields[5]
                    , "NPROG" => $rs->fields[6]
                    , "NKPROG" => $rs->fields[7]
                    , "NDATA" => $rs->fields[8]
                    , "NPROSES" => $rs->fields[9]
                    );
                    $rs->MoveNext();
                }
            }

    }

    return $data;

}

/*
 * fungsi getJadwal
 * digunakan untuk mengambil data
 * nilai sidang dari beritacara
 * nilaikembali: array("NilaiTotal", "Grade")
 */
function getJadwal($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt)
{
    global $cnora;

    $sql = "select * from tjadsidang j "
        . "where j.cnojadwal=:cnojadwal "
        . "and j.cthajar=:cthajar "
        . "and j.csmt=:csmt "
        . "and j.cnojadwal=:cnojadwal "
        . "and j.cnim=:cnim ";
    $datasql = array('cthajar' => $cthajar
    , 'csmt' => $csmt
    , 'cnim' => $cnim
    , 'cnojadwal' => $cnojadwal);
    //$cnora->debug=true;
    $rs = $cnora->Execute($sql, $datasql);

    if (!$rs->EOF) {
        $data = $rs->fields;
    }

    return $data;

}


/*
 * fungsi getNilaiMhs
 * digunakan untuk mengambil data
 * nilai sidang dari beritacara
 * nilaikembali: array("NilaiTotal", "Grade")
 */
function getNilaiMhs($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt)
{
    global $cnora;

    $data = array("nilaitotal" => "-", "grade" => "-", "cket" => "", "keterangan" => "", "calasan" => "-");

    $tjadsidang = "tjadsidang";
    $tberitaacara = "tberitaacara";
    if ($ckdfak == "02") {
        $tjadsidang = "tjadsidangast";
        $tberitaacara = "tberitaacaraast";
    }

    $sql = "select nnilakhir, cgrade "
        . ", cket, calasan "
        . ", case cket when null then '' when '1' then 'Syarat Terpenuhi' when '2' then 'Syarat Tidak Terpenuhi' when '3' then 'Tidak Hadir' else '-' end keterangan  "
        . "from $tjadsidang j, $tberitaacara b "
        . "where j.cnojadwal=b.cnojadwal  "
        . "and j.cthajar=:cthajar "
        . "and j.csmt=:csmt "
        . "and j.cnojadwal=:cnojadwal "
        . "and j.cnim=:cnim ";
    $data = array('cthajar' => $cthajar
    , 'csmt' => $csmt
    , 'cnim' => $cnim
    , 'cnojadwal' => $cnojadwal
    );
    //$cnora->debug=true;
    $rs = $cnora->Execute($sql, $data);

    if (!$rs->EOF) {
        $data["nilaitotal"] = $rs->fields[0];
        $data["grade"] = $rs->fields[1];
        $data["cket"] = $rs->fields[2];
        $data["calasan"] = $rs->fields[3];
        $data["keterangan"] = $rs->fields[4];
    }

    return $data;

}


/*
 * fungsi getBa
 * digunakan untuk mengambil data
 * nilai sidang dari beritacara
 * nilaikembali: array("NilaiTotal", "Grade")
 */
function getBa($cnim, $ckdfak, $cnojadwal, $cthajar, $csmt, $cstatus)
{
    global $cnora, $tsidang;

    $datahasil = array("nilaitotal" => "-", "grade" => "-", "cket" => "-", "keterangan" => "-", "calasan" => "-", "crevisi" => "-");

    //1 syarat terpenuhi, 2 syarat tidak terpenuhi, 3 tidak hadir, null dosennya bingung
    $sql = "select nnilakhir, cgrade  "
        . ", cket, case cket when null then '' when '1' then 'Syarat Terpenuhi' when '2' then 'Syarat Tidak Terpenuhi' when '3' then 'Tidak Hadir' else '-' end keterangan  "
        . ", calasan, crevisi "
        . "from tjadsidang j, tberitaacara b, $tsidang s "
        . "where j.cnojadwal=b.cnojadwal  "
        . "and b.cnoba=s.cnoba  "
        . "and j.cthajar=:cthajar "
        . "and j.csmt=:csmt "
        . "and j.cnojadwal=:cnojadwal "
        . "and j.cnim=:cnim "
        . "and s.cstatus=:cstatus ";

    if ($ckdfak == "02") {
        $sql = "select nnilakhir, cgrade  "
            . ", cket, case cket when null then '' when '1' then 'Syarat Terpenuhi' when '2' then 'Syarat Tidak Terpenuhi' when '3' then 'Tidak Hadir' else '-' end keterangan  "
            . ", calasan, crevisi "
            . "from tjadsidangast j, tberitaacaraast b, tsidangast s "
            . "where j.cnojadwal=b.cnojadwal  "
            . "and b.cnojadwal=s.cnojadwal  "
            . "and j.cthajar=:cthajar "
            . "and j.csmt=:csmt "
            . "and j.cnojadwal=:cnojadwal "
            . "and j.cnim=:cnim "
            . "and s.cstatus=:cstatus ";
    }
    $data = array('cthajar' => $cthajar
    , 'csmt' => $csmt
    , 'cnim' => $cnim
    , 'cnojadwal' => $cnojadwal
    , 'cstatus' => $cstatus
    );
    //$cnora->debug=true;
    $rs = $cnora->Execute($sql, $data);

    if (!$rs->EOF) {
        $datahasil["nilaitotal"] = $rs->fields[0];
        $datahasil["grade"] = $rs->fields[1];
        $datahasil["cket"] = $rs->fields[2];
        $datahasil["keterangan"] = $rs->fields[3];
        $datahasil["calasan"] = $rs->fields[4];
        $datahasil["crevisi"] = $rs->fields[5];
    }

    return $datahasil;

}


/*
* fungsi getDataSidangke
* digunakan untuk memberikan mengambil data jenis sidang
* untuk fti: Sidang 1; Sidang 2
* untuk ft prodi arsitektur: Sidang 1; Sidang 2; Desain 1; Desain 2
*/
function getDataSidangke()
{
    global $cnora;

    $sqlMatkul = "select count(*) from mdosbimbta "
        . "where ckddos=:CKDDOS "
        . "and ckdprogst='51'";
    $rsMatkul = $cnora->Execute($sqlMatkul, array('CKDDOS' => $_SESSION['slogin'][1])
    );

    if ($rsMatkul->fields[0] > 0) {
        $dtsidangke[] = array("1", "Sidang Programming ke-1");
        $dtsidangke[] = array("2", "Sidang Programming ke-2");
        $dtsidangke[] = array("3", "Sidang Desain ke-1");
        $dtsidangke[] = array("4", "Sidang Desain ke-2");
    } else {
        $dtsidangke[] = array("1", "Sidang ke-1");
        $dtsidangke[] = array("2", "Sidang ke-2");
    }

    return $dtsidangke;

}

/*
 * fungsi getDataFakultas
 * digunakan untuk mengambil data
 * fakultas mana saja dosen membimbing dan menguji
 */
function getDataFakultas()
{
    global $cnora;
    $data = array();

    $sqlFak = "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from mdosbimbta a "
        . "where ckddos =:CKDDOS "
        . "union "
        . "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from TDTLDOSBIMBTA a "
        . "where ckddos =:CKDDOS "
        . "union "
        . "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from mdosuji a "
        . "where ckodedos =:CKDDOS "
        . "union "
        . "select '02' ckdfak, 'Akademi Sekretari' nmfak  "
        . "from mdosbimbast "
        . "where ckodedos=:CKDDOS "
        . "order by ckdfak ";
    // astri di ilangin dulu
    $sqlFak = "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from mdosbimbta a "
        . "where ckddos =:CKDDOS "
        . "and ckdfak<>'02' "
        . "union "
        . "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from TDTLDOSBIMBTA a "
        . "where ckddos =:CKDDOS "
        . "union "
        . "select distinct a.ckdfak, nmfak(a.ckdfak) nmfak "
        . "from mdosuji a "
        . "where ckodedos =:CKDDOS "
        . "order by ckdfak ";

    $rsFak = $cnora->Execute($sqlFak, array('CKDDOS' => $_SESSION['slogin'][1]));

    if (!$rsFak->EOF) {
        while (!$rsFak->EOF) {
            $data[] = array($rsFak->fields[0], $rsFak->fields[1]);
            $rsFak->MoveNext();
        }
    }

    return $data;
}

/*
 * fungsi getDataProdi
 * digunakan untuk mengambil data
 * pilihan prodi sesuai fakultas dipilih
 */
function getDataProdi($ckddos, $ckdfak)
{
    global $cnora;
    $data = array();

    $sqlFak = "select distinct ckdprogst, case cjenis when '5' then '30' else '50' end ckdjen "
        . ", nmprodi(ckdprogst||case cjenis when '5' then '30' else '50' end) nmprodi  "
        . "from Mdosbimbta "
        . "where ckdfak=:CKDFAK "
        . "and ckddos=:CKDDOS "
        . "order by ckdprogst ";
    if ($ckdfak == "02") {
        $sqlFak = "select ckdprogst, ckdjen, INITCAP(cnmprogst) nmprodi from mprodi where ckdfak='02'";
    }

    $rsFak = $cnora->Execute($sqlFak, array(
        'CKDDOS' => $ckddos
    , 'CKDFAK' => $ckdfak
    ));

    if (!$rsFak->EOF) {
        while (!$rsFak->EOF) {
            $data[] = array($rsFak->fields[0], $rsFak->fields[1], $rsFak->fields[2]);
            $rsFak->MoveNext();
        }
    }

    return $data;
}


/*
 * fungsi getDataPenguji
 * digunakan untuk mengambil data
 * penguji/moderator untuk ditampilkkan ke combobox
 * agar penguji bisa diganti oleh moderator
 */
function getDataPenguji($cnim, $cthajar, $csmt, $cnojadwal, $ckdfak, $cjenis)
{
    global $cnora;
    $data = array();

    //$cnora->debug=true;
    switch ($ckdfak) {
        case "02":
            $query = "select a.ckodedos ckddos, gelarpegawai(ckodedos) NMDOSEN  "
                . "from mdosbimbast a  "
                . "where caktif='T' "
                . "union "
                . "select distinct a.ckddos, gelarpegawai(ckddos) NMDOSEN "
                . "from tbimbingast a "
                . "where cthajar=:CTHAJAR "
                . "and csmt=:CSMT "
                . "union "
                . "select ckodedos CKDDOS, gelarpegawai(ckodedos) NMDOSEN   "
                . "from MDOSUJI "
                . "where ckodedos not in (select ckddos from mdosbimbta )  "
                . "and cflaguji='T'  "
                . "AND CKDFAK=:CKDFAK  "
                . "order by 2 ASC ";
            $dataquery = array(
                'CKDFAK' => $ckdfak
            , 'CTHAJAR' => $cthajar
            , 'CSMT' => $csmt
            , 'CJENIS' => $cjenis
            );

            break;
        case "07":
            $getJenisTa = getJenistaMhs($cnim, $cthajar, $csmt);
            $query = "select a.ckddos, gelarpegawai(ckddos) NMDOSEN "
                . "from tdtldosbimbta a "
                . "where ckdfak=:CKDFAK "
                . "and cthajar=:CTHAJAR "
                . "and csmt=:CSMT "
                . "and ckdminat=:CKDMINAT "
                . "and ckdjenis=:CKDJENIS "
                . "and ckdmetode=:CKDMETODE "
                . "union "
                . "select ckodedos CKDDOS, gelarpegawai(ckodedos) NMDOSEN   "
                . "from MDOSUJI "
                . "where ckodedos not in (select ckddos from mdosbimbta )  "
                . "and cflaguji='T'  "
                . "AND CKDFAK=:CKDFAK  "
                . "order by 2 ASC ";
            $dataquery = array(
                'CKDFAK' => $ckdfak
            , 'CTHAJAR' => $cthajar
            , 'CSMT' => $csmt
            , 'CKDMINAT' => $getJenisTa['ckdminat']
            , 'CKDJENIS' => $getJenisTa['ckdjenis']
            , 'CKDMETODE' => $getJenisTa['ckdmetode']
            );

            break;
        default:
            $query = "select CKDDOS, GELARPEGAWAI(CKDDOS) NMDOSEN from mdosbimbta  "
                . "where ckdfak=:CKDFAK "
                . "and cthajar=:CTHAJAR "
                . "and csmt=:CSMT "
                . "and cjenis=:CJENIS "
                . "UNION "
                . "select ckodedos CKDDOS, gelarpegawai(ckodedos) NMDOSEN  "
                . "FROM MDOSUJI "
                . "where ckodedos not in (select ckddos from mdosbimbta ) "
                . "and cflaguji='T' "
                . "AND CKDFAK=:CKDFAK "
                . "order by 2 ASC ";
            $dataquery = array(
                'CKDFAK' => $ckdfak
            , 'CTHAJAR' => $cthajar
            , 'CSMT' => $csmt
            , 'CJENIS' => $cjenis
            );

    } // akhir dari switch($ckdfak)

    $rsFak = $cnora->Execute($query, $dataquery);

    if (!$rsFak->EOF) {
        while (!$rsFak->EOF) {
            $data[] = array($rsFak->fields[0], $rsFak->fields[1]);
            $rsFak->MoveNext();
        }
    }

    return $data;
}


/*
 * fungsi getPenguji
 * digunakan untuk mencari data penguji dan moderator
 * sesuai mahasiswa yang dipilih
 */
function getPenguji($cnojadwal, $ckdfak)
{
    global $cnora, $tsidang;
    $data = array();

    $query = "select cstatus, ckddos, gelarpegawai(ckddos) dosen "
        . "from $tsidang s, tberitaacara b "
        . "where s.cnoba=b.cnoba "
        . "and b.cnojadwal=:CNOJADWAL "
        . "order by cstatus";

    if ($ckdfak == "02") {
        $query = "select cstatus, ckddos, gelarpegawai(ckddos) dosen "
            . "from tsidangast b "
            . "where b.cnojadwal=:CNOJADWAL "
            . "order by cstatus";
    }

    $dataquery = array(
        'CNOJADWAL' => $cnojadwal
    );
    $flag = "T"; // flag T berarti udah ada di tsidangentry
    $cek = 0;
    //$cnora->debug=true;
    $rsSidang = $cnora->Execute($query, $dataquery);
    if (!$rsSidang->EOF) {
        while (!$rsSidang->EOF) {
            $data[$rsSidang->fields[0]] = array($rsSidang->fields[1], $rsSidang->fields[2], $flag);
            $cek++;
            $rsSidang->MoveNext();
        }
    }

    //cetakArray($data, "getpenguji");

    if ($cek < 3) {
        $query = "select cstatus, ckddos, gelarpegawai(ckddos) dosen "
            . "from tjadsidang b, tpenguji p "
            . "where b.CNOJADWAL=p.CNOJADWAL "
            . "and b.cnojadwal=:CNOJADWAL "
            . "order by cstatus";
        $dataquery = array(
            'CNOJADWAL' => $cnojadwal
        );

        $rsSidang = $cnora->Execute($query, $dataquery);
        while (!$rsSidang->EOF) {
            if (!isset($data[$rsSidang->fields[0]])) {
                $data[$rsSidang->fields[0]] = array($rsSidang->fields[1], $rsSidang->fields[2], "F");
            }
            $rsSidang->MoveNext();
        }

    }

    //cetakArray($data, "getpenguji");

    return $data;
}


/*
 * fungsi getSidangke
 * digunakan untuk membantu menerjemahkan
 * kode sidangke menjadi kalimat
 */
function getSidangke($ckdprogst, $csidangke)
{
    $sidangke = "";

    if ($ckdprogst == "51") {
        switch ($csidangke) {
            case "1":
                $sidangke = "Sidang Programming ke-1";
                break;
            case "2":
                $sidangke = "Sidang Programming ke-2";
                break;
            case "3":
                $sidangke = "Sidang Desain ke-1";
                break;
            case "4":
                $sidangke = "Sidang Desain ke-2";
                break;
            default:
                $sidangke = "-";
        }
    } else {
        switch ($csidangke) {
            case "1":
                $sidangke = "Sidang ke-1";
                break;
            case "2":
                $sidangke = "Sidang ke-2";
                break;
            case "3":
                $sidangke = "Sidang Desain ke-1";
                break;
            case "4":
                $sidangke = "Sidang Desain ke-2";
                break;
            default:
                $sidangke = "-";
        }
    }

    return $sidangke;
}


/*
 * fungsi getStatusBa
 * digunakan untuk mencari status berita acara
 */
function getStatusBa($ckdfak, $cnoba, $cnojadwal)
{
    global $cnora, $cekData;
    $hasil = "";

    $sql = "select cflag from tberitaacara "
        . "where cnoba=:cnoba and cnojadwal=:cnojadwal ";
    $datasql = array("cnoba" => $cnoba
    , "cnojadwal" => $cnojadwal
    );
    //$cnora->debug=true;
    $rs = $cnora->Execute($sql, $datasql);
    if (!$rs->EOF) {
        $hasil = $rs->fields[0];
    }

    $cekData['statusBA'] = $hasil;

    return $hasil;
}


/*
 * fungsi getStatusBa2
 * digunakan untuk mencari status berita acara
 * berdasarkan nojadwal dan NIM
 */
function getStatusBa2($ckdfak, $cnim, $cnojadwal)
{
    global $cnora, $cekData;
    $hasil = "";

    $sql = "select cflag from tberitaacara b, tjadsidang j "
        . "where b.cnojadwal=j.cnojadwal and b.cnojadwal=:cnojadwal and j.cnim=:cnim ";
    $datasql = array("cnim" => $cnim
    , "cnojadwal" => $cnojadwal
    );
    //$cnora->debug=true;
    $rs = $cnora->Execute($sql, $datasql);
    if (!$rs->EOF) {
        $hasil = $rs->fields[0];
    }

    return $hasil;
}


/*
 * fungsi generateNoBa
 * digunakan untuk membuat nomor Berita Acara
 */
function generateNoBa($ckdfak, $cthajar, $csmt)
{
    global $cnora;
    $NOMOR = 0;
    $XTHAJAR = "";
    $CNOBUKTI = "";
    $X = 0;

    $rbln = "";
    switch (date("m")) {
        case "01" :
            $rbln = "I";
            break;
        case "02" :
            $rbln = "II";
            break;
        case "03" :
            $rbln = "III";
            break;
        case "04" :
            $rbln = "IV";
            break;
        case "05" :
            $rbln = "V";
            break;
        case "06" :
            $rbln = "VI";
            break;
        case "07" :
            $rbln = "VII";
            break;
        case "08" :
            $rbln = "VIII";
            break;
        case "09" :
            $rbln = "IX";
            break;
        case "10" :
            $rbln = "X";
            break;
        case "11" :
            $rbln = "XI";
            break;
        case "12" :
            $rbln = "XII";
            break;
    }

    $thn = substr(date("Y"), 2, 2);

    if ($ckdfak == "02") {

        $sql = "SELECT NNOBRT FROM CNTTAAST";
        $rs = $cnora->Execute($sql);
        if (!$rs->EOF) {
            $NOMOR = $rs->fields[0];
        }

        $NOMOR = $NOMOR + 1;

        IF ($NOMOR < 10)
            $CNOBUKTI = '000' . $NOMOR;
        ELSE IF ($NOMOR < 100)
            $CNOBUKTI = '00' . $NOMOR;
        ELSE IF ($NOMOR < 1000)
            $CNOBUKTI = '0' . $NOMOR;
        ELSE IF ($NOMOR < 10000)
            $CNOBUKTI = $NOMOR;

        $CNOBUKTI = "F2-AST-1/" . $CNOBUKTI . "/" . $rbln . "/" . $thn;
    } else {

        $sql = "SELECT CTAHUN,NNOBA FROM CNTNOBA where ckdfak=:ckdfak";
        $datasql = array("ckdfak" => $ckdfak);
        $rs = $cnora->Execute($sql, $datasql);
        if (!$rs->EOF) {
            $XTHAJAR = $rs->fields[0];
            $NOMOR = $rs->fields[1];
        }

        $sql = "select count(t.dtglsidang) FROM TBERITAACARA B,TJADSIDANG T "
            . "WHERE B.CNOJADWAL=T.CNOJADWAL  "
            . "AND T.CTHAJAR||T.CSMT=(SELECT CTHAJAR||CSMT FROM MCONFIGJADBT)  "
            . "and ckdfak=:ckdfak";
        $sql = "select count(t.dtglsidang) FROM TBERITAACARA B,TJADSIDANG T "
            . "WHERE B.CNOJADWAL=T.CNOJADWAL  "
            . "AND T.CTHAJAR||T.CSMT=('$cthajar'||'$csmt')  "
            . "and ckdfak=:ckdfak"; // testing hardcode ke 20172018
        $datasql = array("ckdfak" => $ckdfak);
        //$cnora->debug=true;
        $rs = $cnora->Execute($sql, $datasql);
        if (!$rs->EOF) {
            $X = $rs->fields[0];
        }

        if ($X == 0) {
            if ($XTHAJAR != date("Y") or $X == 0) {
                $sql = "update CNTNOBA set CTAHUN=to_char(sysdate,'YYYY'),nnoba=0 where ckdfak=:ckdfak";
                $datasql = array("ckdfak" => $ckdfak);
                simpanOracle("ba", $sql, $datasql, "Ubah CounterBA");
                $XTHAJAR = date("Y");
                $NOMOR = 0;
            }
        }

        $NOMOR = $NOMOR + 1;

        IF ($NOMOR < 10)
            $CNOBUKTI = '000' . $NOMOR;
        ELSE IF ($NOMOR < 100)
            $CNOBUKTI = '00' . $NOMOR;
        ELSE IF ($NOMOR < 1000)
            $CNOBUKTI = '0' . $NOMOR;
        ELSE IF ($NOMOR < 10000)
            $CNOBUKTI = $NOMOR;

        $fak = "UBL";
        $s = "S";
        switch ($ckdfak) {
            case "01":
                $fak = "FTI";
                break;
            case "03":
                $s = "K";
                $fak = "FEK";
                break;
            case "04":
                $fak = "FISIP";
                break;
            case "05":
                $fak = "FT";
                break;
            case "07":
                $fak = "FIKOM";
                break;
        }

        $CNOBUKTI = $s . "/UBL/" . $fak . "/" . $CNOBUKTI . "/" . $rbln . "/" . $thn;

    }


    return $CNOBUKTI;
}


/*
 * fungsi updateNoba
 * digunakan untuk mengubah counter di CNTNOBA ditambah satu
 */
function updateNoba($ckdfak)
{
    $sql = "update cntnoba set nnoba=nnoba+1 where ckdfak=:ckdfak";
    $datasql = array("ckdfak" => $ckdfak);
    simpanOracle("ba", $sql, $datasql, "Tambah CounterBA");
}

/*
 * fungsi hitungNilai
 * digunakan untuk menghitung total nilai dari moderator, penguji 1 dan penguji 2
 */
function hitungNilai($dataNilai, $dataPersentase, $dataFields)
{
    $totalnilai = 0;

    foreach ($dataFields as $index => $field) {
        $nilai = hitungP($dataNilai['MD'][$field], $dataNilai['P1'][$field], $dataNilai['P2'][$field]) * $dataPersentase[$field];
        $totalnilai = $totalnilai + $nilai;
    }

    return $totalnilai;
}


/*
 * fungsi hitungP
 * digunakan untuk menghitung total nilai per item
 */
function hitungP($nilaiMD, $nilaiP1, $nilaiP2)
{
    $nilai = 0;
    $P = 0;

    if ($nilaiP1 == 999 && $nilaiP2 == 999) {
        $P = 999;
    } else if ($nilaiP1 == 999) {
        $P = $nilaiP2;
    } else if ($nilaiP2 == 999) {
        $P = $nilaiP1;
    } else {
        $P = ($nilaiP1 + $nilaiP2) / 2;
    }

    if ($nilaiMD == 999 && $P == 999) {
        $nilai = 0;
    } else if ($nilaiMD == 999) {
        $nilai = $P;
    } else if ($P == 999) {
        $nilai = $nilaiMD;
    } else {
        if (($nilaiMD - $P) > 20) {
            $nilai = $P + 10;
        } else {
            $nilai = ($nilaiMD + $P) / 2;
        }

    }

    return $nilai;
}

/*
 * fungsi getGrade
 * mengembalikan grade berdasarkan nilai
 */
function getGrade($totalNilai)
{
    //totalNilai = parseFloat(totalNilai);
    $grade = "*";
    if ($totalNilai >= 101) {
        $grade = "-";
    } else if ($totalNilai >= 85) {
        $grade = "A";
    } else if ($totalNilai >= 80) {
        $grade = "A-";
    } else if ($totalNilai >= 75) {
        $grade = "B+";
    } else if ($totalNilai >= 70) {
        $grade = "B";
    } else if ($totalNilai >= 65) {
        $grade = "B-";
    } else if ($totalNilai >= 60) {
        $grade = "C";
    } else if ($totalNilai >= 40) {
        $grade = "D";
    } else if ($totalNilai >= 0) {
        $grade = "E";
    } else {
        $grade = "F";
    }
    return $grade;
}


/*
 * fungsi cekThajarSmt
 * memeriksa apakah tahun ajar smt >= 20192020 Genap
 * jika di bawah dari itu, maka tidak boleh
 */
function cekThajarSmt($cthajar, $csmt)
{
    $cek = false;

    switch ($csmt) {
        case "O":
            $csmt = 1;
            break;
        case "E":
            $csmt = 2;
            break;
        default:
            $csmt = 0;
    }

    $cthajar = $cthajar + $csmt;
    if ($cthajar > 20192021) {
        $cek = true;
    }

    return $cek;
}


/*
 * fungsi cekThajarSmt
 * memeriksa apakah tahun ajar smt >= 20192020 Genap
 * jika di bawah dari itu, maka tidak boleh
 */
function getJamSesi($cthajar, $csmt, $dy)
{
    $jamSesi = array();

    if ($cthajar == "20192020" && $csmt == "E") {
        $jamSesi['arrJam'] = array(1 => "08:00", 2 => "10:00", 3 => "13:00", 4 => "15:00", 5 => "17:00", 6 => "19:00");
        $jamSesi['arrJamAkhir'] = array(1 => "10:00", 2 => "12:00", 3 => "15:00", 4 => "17:00", 5 => "19:00", 6 => "21:00");
        if ($dy == "FRI") {
            $jamSesi['arrJam'] = array(1 => "07:30", 2 => "09:30", 3 => "13:00", 4 => "15:00", 5 => "17:00", 6 => "19:00");
            $jamSesi['arrJamAkhir'] = array(1 => "09:30", 2 => "11:30", 3 => "15:00", 4 => "17:00", 5 => "19:00", 6 => "21:00");
        }
    } else {
        $jamSesi['arrJam'] = array(1 => "08:00", 2 => "09:30", 3 => "11:00", 4 => "13:30", 5 => "15:00", 6 => "16:30");
        $jamSesi['arrJamAkhir'] = array(1 => "09:30", 2 => "11:00", 3 => "12:30", 4 => "15:00", 5 => "16:30", 6 => "18:00");
    }

    return $jamSesi;
}


/*
 * fungsi tanggal_indo
 * digunakan untuk melakukan konversi tanggal
 * format parameter: YYYY-MM-DD-DY
 */
function tanggal_indo($tanggal)
{
    $bulan = array(1 => 'Januari',
        'Februari',
        'Maret',
        'April',
        'Mei',
        'Juni',
        'Juli',
        'Agustus',
        'September',
        'Oktober',
        'November',
        'Desember'
    );
    $hari = array("MON" => "Senin"
    , "TUE" => "Selasa"
    , "WED" => "Rabu"
    , "THU" => "Kamis"
    , "FRI" => "Jumat"
    , "SAT" => "Sabtu"
    , "SUN" => "Minggu"
    );

    $split = explode('-', $tanggal);
    return $hari[$split[3]] . ' ' . $split[2] . ' ' . $bulan[(int)$split[1]] . ' ' . $split[0];
}


/*
 * fungsi simpanOracle
 * digunakan untuk menyimpan data ke oracle
 * ada tiga jenis untuk mengikuti jenis di simpanLog
 * 1. sidang
 *    digunakan untuk menyimpan log
 *    orang yang mengakses modul entry nilai sidang dan berhamemilih mahasiswa
 * 2. ba
 *    menyimpan log berita acara yang disimpan oleh moderator,
 *    baik baru pertama kali maupun update
 * 3. nilai
 *    menyimpan log simpan nilai oleh moderator dan penguji
 *    baik baru pertama kali maupun update
 */
function simpanOracle($jenis, $sql, $datasql, $keterangan)
{
    global $cnora;
    $cek = false;
    $cnora->startTrans();
    //$cnora->debug=true;
    $cnora->Execute($sql, $datasql);
    if ($cnora->hasFailedTrans()) {
        $cek = false;
    } else {
        $cek = true;
    }
    $cnora->completeTrans();

    $ket = ($cek) ? "Success" : "Failed";

    $keterangan = $keterangan . " : ($ket) " . implode("#", $datasql);
    simpanLog($jenis, $sql, $keterangan);

    return $cek;
}


/*
 * fungsi simpanOracle2
 * digunakan untuk menyimpan data ke oracle
 * ada tiga jenis untuk mengikuti jenis di simpanLog
 * 1. sidang
 *    digunakan untuk menyimpan log
 *    orang yang mengakses modul entry nilai sidang dan berhamemilih mahasiswa
 * 2. ba
 *    menyimpan log berita acara yang disimpan oleh moderator,
 *    baik baru pertama kali maupun update
 * 3. nilai
 *    menyimpan log simpan nilai oleh moderator dan penguji
 *    baik baru pertama kali maupun update
 */
function simpanOracle2($jumlah, $data)
{
    global $cnora;
    $cek = false;

    $cnora->startTrans();
    //$cnora->debug=true;

    for ($i = 0; $i < $jumlah; $i++) {
        $jenis = $data[$i]["jenis"];
        $sql = $data[$i]["sql"];
        $datasql = $data[$i]["datasql"];
        $keterangan = $data[$i]["keterangan"];

        $cnora->Execute($sql, $datasql);

        $keterangan = $keterangan . " : " . implode("#", $datasql);
        simpanLog($jenis, $sql, $keterangan);
    }


    if ($cnora->hasFailedTrans()) {
        $cek = false;
    } else {
        $cek = true;
    }
    $cnora->completeTrans();


    return $cek;
}


/*
 * fungsi simpanLog
 * digunakan untuk menyimpan log ke mysql
 * ada tiga jenis
 * 1. sidang
 *    digunakan untuk menyimpan log
 *    orang yang mengakses modul entry nilai sidang dan berhamemilih mahasiswa
 * 2. ba
 *    menyimpan log berita acara yang disimpan oleh moderator,
 *    baik baru pertama kali maupun update
 * 3. nilai
 *    menyimpan log simpan nilai oleh moderator dan penguji
 *    baik baru pertama kali maupun update
 */
function simpanLog($jenis, $query, $keterangan)
{
    global $cnmy;
    $tabel = "";

    switch ($jenis) {
        case "sidang":
            $tabel = "Tsidang_log";
            break;
        case "ba":
            $tabel = "Tsidangba_log";
            break;
        case "nilai":
            $tabel = "Tsidangnilai_log";
            break;
        default :
            $tabel = "Tsidang_log";
    }

    //$cnmy->debug=true;
    $sqllog = "insert into $tabel values(?, now(), ?, ?, ?)";
    $datalog = array($_SESSION['slogin'][1], $_SERVER['REMOTE_ADDR'], $query, $keterangan);
    $rsmy = $cnmy->Execute($sqllog, $datalog);

}


/*
 * fungsi simpanCookies
 * digunakan untuk menimpan pilihan form yang sudah dipilih
 * agar jika load form lagi, ditampilkan default ke pilihan tsb
 */
function simpanCookies($data)
{
    foreach ($data as $key => $value) {
        setcookie("sidang_" . $key, $value, time() + (86400 * 30), "/"); // 86400 = 1 day
    }
}


function cetakArray($data, $nama)
{
    echo "data $nama";
    echo "<pre>";
    print_r($data);
    echo "</pre>";
}

// Batas awal Footer
if (isset($dtsidangke)) $mys->assign('dtsidangke', $dtsidangke);
if (isset($dtfakultas)) $mys->assign('dtfakultas', $dtfakultas);

if (isset($dtprodi)) $mys->assign('dtprodi', $dtprodi);
if (isset($dtmhs)) $mys->assign('dtmhs', $dtmhs);

if (isset($dtPenguji)) $mys->assign('dtPenguji', $dtPenguji);
if (isset($getPenguji)) $mys->assign('getPenguji', $getPenguji);

if (isset($frmNilai)) $mys->assign('frmNilai', $frmNilai);
if (isset($dtNilaiMhs)) $mys->assign('dtNilaiMhs', $dtNilaiMhs);
if (isset($dtBa)) $mys->assign('dtBa', $dtBa);

if (isset($getStatus)) $mys->assign('getStatus', $getStatus);
if (isset($stat)) $mys->assign('cstatus', $stat);

if (isset($isJadwal)) $mys->assign('isJadwal', $isJadwal);

if (isset($txttmp)) $mys->assign('txttmp', $txttmp);
if (isset($partArr)) $mys->assign('dtForm', $partArr);
if (isset($getLinkBa)) $mys->assign('getLinkBa', $getLinkBa);
if (isset($cekData)) $mys->assign('cekData', $cekData);
//cetakArray($cekData,"cekdata");

$mys->assign('use', '1');

if (isset($errMsg)) $mys->assign('errMsg', $errMsg);
if (isset($infoMsg)) $mys->assign('infoMsg', $infoMsg);
//if (isset($showFrm))
//    $mys->assign('showFrm', $showFrm);


$mys->assign('showFrm', $showFrm);
isset($_SESSION['nmmenu']) ? $nmmenu = $_SESSION['nmmenu'] : $nmmenu = 'login';
$mys->assign('nmmenu', $nmmenu);
$mys->display(Server_Root . "/" . $mys->template_dir . "/$nmfile.tpl");

