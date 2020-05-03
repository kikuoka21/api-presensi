<?php
/*    _____       _ __   __        _________
*    / ___/____  (_) /__/ /____  _<  / ____/
*    \__ \/ __ \/ / //_/ //_/ / / / /___ \
*   ___/ / / / / / ,< / ,< / /_/ / /___/ /
*  /____/_/ /_/_/_/|_/_/|_|\__, /_/_____/
*                         /____/
*
*  Everybody in this country should learn to program a computer, because it teaches you how to think.
*  The best thing about a boolean is even if you are wrong, you are only off by a bit.
*  Before software can be reusable it first has to be usable.
*  There are two ways to write error-free programs; only the third one works.
*  In a room full of top software designers, if two agree on the same thing, that’s a majority.
*  A program is never less than 90% complete, and never more than 95% complete.
*  If the code and the comments do not match, possibly both are incorrect.
*  If you think your users are idiots, only idiots will use it.
*  The question is "Are u an idiot ?"
*  Powered by Snikky15 - https://gitlab.com/Snikky1505
*  Copyright © 2019 Snikky15 All Rights Reserved.
*/

namespace App\Modules_siswa;

use DB;

class Utilities
{
    //TODO: fungsi berada dibawah ini
    private $xsearch;
    private $xtahun_cari;

    public function getXtahunCari()
    {
        return trim($this->xtahun_cari);
    }

    public function setXtahunCari($xtahun_cari)
    {
        $this->xtahun_cari = $xtahun_cari;
    }

    public function getXsearch()
    {
        return $this->xsearch;
    }

    public function setXsearch($xsearch)
    {
        $this->xsearch = $xsearch;
    }



    public function PencarianPertama()
    {
        $keyword = explode(' ', $this->getXsearch());
        //$this->getXtahunCari() != null || || $this->getXtahunCari() != "0" || $this->getXtahunCari() != "99"
        $add_sql = '';
        if ($this->getXtahunCari() != null || $this->getXtahunCari() != 0) {
            $add_sql = " and substr(a.cthajar,1,4) = '" . $this->getXtahunCari() . "'";
        }
        $sql = '';
        $sql_set = '';
        $sql_final = '';
        $n = count($keyword) - 1;
        $i = -1;
        $par = [];
        foreach ($keyword as $key => $value) {
            $i++;
            $sql = "select a.cnim as cnim, initcap(f.cnama) as cnama, upper(b.cjuduli) as cjudul, initcap(e.cnmfak) as cnmfak, initcap(d.cnmprogst) as cnmprogst, substr(cthajar,1,4) || '/' || substr(cthajar,5,8) as cthajar, decode(csmt,'E','Genap','Ganjil') as csmt, substr(f.dtglyudisi,1,4) as dtglyudisi, 'Skripsi' as jenis from tdaftarbt a,thista b, mprodi d, mfakultas e, mmahasiswa f where a.cnim=b.cnim and a.cnim=f.cnim and f.ckdprogst = d.ckdprogst and f.ckdjen = d.ckdjen and d.ckdfak = e.ckdfak $add_sql ";
            $param_set = " and upper(b.cjuduli) like '%' || :XPARAM" . $i . " || '%'";
            $sql_set .= $sql . $param_set;
            if ($n != $i) {
                $sql_set .= " UNION ";
            }
            $par['XPARAM' . $i] = strtoupper($value);
        }
        $par['XPARAMFINAL'] = strtoupper($this->getXsearch());
        $sql_final .= $sql_set . " UNION " . $sql . "and upper(b.cjuduli) like '%'|| :XPARAMFINAL ||'%' order by 3";
        return $add_sql;
//        return DB::connection('oracle')->select($sql_final, $par);
    }


}
