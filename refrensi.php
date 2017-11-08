<?php
    include("koneksi.php");
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistem Pakar Metode CF (Certainty Factor)</title>
<style type="text/css">
<!--
body,td,th {
    font-family: Georgia, Times New Roman, Times, serif;
    font-size: 13px;
    color: #333333;
}
.style1 {
    color: #000099;
    font-size: 24px;
}
a:link {
    text-decoration: none;
    color: #333333;
}
a:visited {
    text-decoration: none;
    color: #333333;
}
a:hover {
    text-decoration: underline;
    color: #FF0000;
}
a:active {
    text-decoration: none;
    color: #333333;
}
.style2 {font-weight: bold}
-->
</style></head>

<body>
<table width="1000" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#000099">
  <tr>
    <td height="50" bgcolor="#FFFFFF"><span class="style1">Sistem Pakar Metode CF (Certainty Factor)</span></td>
  </tr>
  <tr>
    <td height="35" bgcolor="#FFFFFF"><span class="style2"><a href="index.php">Home</a> | <a href="cf-php-mysql.php">Konsultasi Pakar Metode Certainty Factor</a> | <a href="login.php">Login</a></span></td>
  </tr>
  <tr>
    <td align="center" valign="top" bgcolor="#FFFFFF"><br />
      <strong>Analisa Menggunakan Sistem Pakar Metode CF (Certainty Factor)</strong><br />
      <br />
<?php
    if (!isset($_POST['button']))
    {
?>
<form name="form1" method="post" action=""><br>
  <table align="center" width="600" border="1" cellspacing="0" cellpadding="5">
  <tr>
  <td id="ignore" bgcolor="#DBEAF5" width="300"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><font size="2">GEJALA</font> </font></strong></div></td>
  <?php
    $q = mysql_query("select * from gejala ORDER BY id_gejala");
    while ($r = mysql_fetch_array($q))
    {
    ?>
    <tr>
      <td width="600">
        <input id="gejala<?php echo $r['id_gejala']; ?>" name="gejala<?php echo $r['id_gejala']; ?>" type="checkbox" value="true">
        <?php echo $r['nama_gejala']; ?><br/>
        </td>
    </tr>
    <?php } ?>
    <tr>
      <td><input type="submit" name="button" value="Proses"></td>
    </tr>
  </table>
  <br>
</form>
  <?php
  }
  else
  {

    $perintah = "SELECT * from gejala";
    $minta =mysql_query($perintah);
    $sql = '';
    $i = 0;
    //mengecek semua chekbox gejala
    while($hs=mysql_fetch_array($minta))
    {
        //jika gejala dipilih
        //menyusun daftar gejala misal '1','2','3' dst utk dipakai di query
        if ($_POST['gejala'.$hs['id_gejala']] == 'true')
        {
            if ($sql == '')
            {
                $sql = "'$hs[id_gejala]'";
            }
            else
            {
                $sql = $sql.",'$hs[id_gejala]'";
            }
        }
        $i++;
    }
    echo $sql.'<br/>';
    empty($daftar_penyakit);
    empty($daftar_cf);
    if ($sql != '')
    {
        //mencari id_penyakit di tabel pengetahuan yang gejalanya dipilih
        $perintah = "SELECT id_penyakit FROM pengetahuan WHERE id_gejala IN ($sql) GROUP BY id_penyakit ORDER BY id_penyakit";
        //echo "<br/>".$perintah."<br/>";
        $minta =mysql_query($perintah);
        $id_penyakit_terbesar = '';
        $nama_penyakit_terbesar = '';
        $c = 0;
        while($hs=mysql_fetch_array($minta))
        {
            //memproses id penyakit satu persatu
            $id_penyakit = $hs['id_penyakit'];
            $qry = mysql_query("SELECT * FROM penyakit WHERE id_penyakit = '$id_penyakit'");
            $dt = mysql_fetch_array($qry);
            $nama_penyakit = $dt['nama_penyakit'];
            $daftar_penyakit[$c] = $hs['id_penyakit'];
            echo "<br/>Proses Penyakit ".$daftar_penyakit[$c].".".$nama_penyakit."<br/>==============<br/>";
            //mencari gejala yang mempunyai id penyakit tersebut, agar bisa menghitung CF dari MB dan MD nya
            $p = "SELECT id_penyakit, mb, md, id_gejala FROM pengetahuan WHERE id_gejala IN ($sql) AND id_penyakit = '$id_penyakit'";
            //echo $p.'<br/>';
            $m =mysql_query($p);
            //mencari jumlah gejala yang ditemukan
            $jml = mysql_num_rows($m);
            //jika gejalanya 1 langsung ketemu CF nya
            echo "jml gejala = ".$jml."<br/>";
            if ($jml == 1)
            {
                $h=mysql_fetch_array($m);
                $mb = $h['mb'];
                $md = $h['md'];
                $cf = $mb - $md;
                $daftar_cf[$c] = $cf;
                //cek apakah penyakit ini adalah penyakit dgn CF terbesar ?
                if (($id_penyakit_terbesar == '') || ($cf_terbesar < $cf))
                {
                    $cf_terbesar = $cf;
                    $id_penyakit_terbesar = $id_penyakit;
                    $nama_penyakit_terbesar = $nama_penyakit;
                }
                echo "<br/>proses 1<br/>------------------------<br/>";
                echo "mb = ".$mb."<br/>";
                echo "md = ".$md."<br/>";
                echo "cf = mb - md = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";
            }
            //jika gejala lebih dari satu harus diproses semua gejala
            else if ($jml > 1)
            {
                $i = 1;
                //proses gejala satu persatu
                while($h=mysql_fetch_array($m))
                {
                    echo "<br/>proses ".$i."<br/>------------------------------------<br/>";
                    //pada gejala yang pertama masukkan MB dan MD menjadi MBlama dan MDlama
                    if ($i == 1)
                    {
                        $mblama = $h['mb'];
                        $mdlama = $h['md'];
                        echo "mblama = ".$mblama."<br/>";
                        echo "mdlama = ".$mdlama."<br/>";
                    }
                    //pada gejala yang nomor dua masukkan MB dan MD menjadi MBbaru dan MB baru kemudian hitung MBsementara dan MDsementara
                    else if ($i == 2)
                    {
                        $mbbaru = $h['mb'];
                        $mdbaru = $h['md'];
                        echo "mbbaru = ".$mbbaru."<br/>";
                        echo "mdbaru = ".$mdbaru."<br/>";
                        $mbsementara = $mblama + ($mbbaru * (1 - $mblama));
                        $mdsementara = $mdlama + ($mdbaru * (1 - $mdlama));
                        echo "mbsementara = mblama + (mbbaru * (1 - mblama)) = $mblama + ($mbbaru * (1 - $mblama)) = ".$mbsementara."<br/>";
                        echo "mdsementara = mdlama + (mdbaru * (1 - mdlama)) = $mdlama + ($mdbaru * (1 - $mdlama)) = ".$mdsementara."<br/>";
                        //jika jumlah gejala cuma dua maka CF ketemu
                        if ($jml == 2)
                        {
                            $mb = $mbsementara;
                            $md = $mdsementara;
                            $cf = $mb - $md;
                            echo "mb = mbsementara = ".$mb."<br/>";
                            echo "md = mdsementara = ".$md."<br/>";
                            echo "cf = mb - md = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";
                            $daftar_cf[$c] = $cf;
                            //cek apakah penyakit ini adalah penyakit dgn CF terbesar ?
                            if (($id_penyakit_terbesar == '') || ($cf_terbesar < $cf))
                            {
                                $cf_terbesar = $cf;
                                $id_penyakit_terbesar = $id_penyakit;
                                $nama_penyakit_terbesar = $nama_penyakit;
                            }
                        }
                    }
                    //pada gejala yang ke 3 dst proses MBsementara dan MDsementara menjadi MBlama dan MDlama
                    //MB dan MD menjadi MBbaru dan MDbaru
                    //hitung MBsementara dan MD sementara yg sekarang
                    else if ($i >= 3)
                    {
                        $mblama = $mbsementara;
                        $mdlama = $mdsementara;
                        echo "mblama = mbsementara = ".$mblama."<br/>";
                        echo "mdlama = mdsementara = ".$mdlama."<br/>";
                        $mbbaru = $h['mb'];
                        $mdbaru = $h['md'];
                        echo "mbbaru = ".$mbbaru."<br/>";
                        echo "mdbaru = ".$mdbaru."<br/>";
                        $mbsementara = $mblama + ($mbbaru * (1 - $mblama));
                        $mdsementara = $mdlama + ($mdbaru * (1 - $mdlama));
                        echo "mbsementara = mblama + (mbbaru * (1 - mblama)) = $mblama + ($mbbaru * (1 - $mblama)) = ".$mbsementara."<br/>";
                        echo "mdsementara = mdlama + (mdbaru * (1 - mdlama)) = $mdlama + ($mdbaru * (1 - $mdlama)) = ".$mdsementara."<br/>";
                        //jika ini adalah gejala terakhir berarti CF ketemu
                        if ($jml == $i)
                        {
                            $mb = $mbsementara;
                            $md = $mdsementara;
                            $cf = $mb - $md;
                            echo "mb = mbsementara = ".$mb."<br/>";
                            echo "md = mdsementara = ".$md."<br/>";
                            echo "cf = mb - md = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";
                            $daftar_cf[$c] = $cf;
                            //cek apakah penyakit ini adalah penyakit dgn CF terbesar ?
                            if (($id_penyakit_terbesar == '') || ($cf_terbesar < $cf))
                            {
                                $cf_terbesar = $cf;
                                $id_penyakit_terbesar = $id_penyakit;
                                $nama_penyakit_terbesar = $nama_penyakit;
                            }
                        }
                    }
                    $i++;
                }
            }
            $c++;
        }
    }

    //urutkan daftar gejala berdasarkan besar CF
    for ($i = 0; $i < count($daftar_penyakit); $i++)
    {
        for ($j = $i + 1; $j < count($daftar_penyakit); $j++)
        {
            if ($daftar_cf[$j] > $daftar_cf[$i])
            {
                $t = $daftar_cf[$i];
                $daftar_cf[$i] = $daftar_cf[$j];
                $daftar_cf[$j] = $t;

                $t = $daftar_penyakit[$i];
                $daftar_penyakit[$i] = $daftar_penyakit[$j];
                $daftar_penyakit[$j] = $t;
            }
        }
    }
    echo "penyakit terbesar = ".$id_penyakit_terbesar.".".$nama_penyakit_terbesar."<br/>";

    //for ($i = 0; $i < count($daftar_penyakit); $i++)
    //{
    //  echo $daftar_penyakit[$i]."=".$daftar_cf[$i]."<br/>";
    //}
    ?>
    <table border="0" cellspacing="0" cellpadding="0" width="605">
        <tr>
        <td width="605" class="pageName" align="center"><p>Hasil konsultasi</p></td>
        </tr>

        <tr>
        <td class="bodyText">
        <p align="justify">
        <table width="423" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor=""#BFD0EA"" class="tb_admin">
            <tr>
                <td width="33%" valign="top">Gejala yang dipilih</td>
                <td width="63%"><strong>
                <?php
                $perintah = "SELECT * from gejala";
                $minta =mysql_query($perintah);
                while($hs=mysql_fetch_array($minta))
                {
                    if ($_POST['gejala'.$hs['id_gejala']] == 'true')
                    {
                ?>
                    <?php echo $hs['nama_gejala']; ?> <br />
                <?php
                    }
                }
                ?>
                </strong></td>
            </tr>

            <tr>
              <td valign="top">&nbsp;</td>
              <td>&nbsp;</td>
              </tr>
            <tr>
              <td>Daftar penyakit </td>
              <td>CF</td>
              </tr>
            <?php
            for ($i = 0; $i < count($daftar_penyakit); $i++)
            {
                $perintah = "SELECT * from penyakit where id_penyakit = '".$daftar_penyakit[$i]."'";
                $minta =mysql_query($perintah);
                $hs=mysql_fetch_array($minta);
                //$id_penyakit_terbesar
                ?>
            <tr>
              <td><?php echo $hs['nama_penyakit']; ?></td>
              <td><?php echo $daftar_cf[$i]; ?></td>
              </tr>
              <?php
              }
              ?>
            <tr>
              <td valign="top">&nbsp;</td>
              <td>&nbsp;</td>
              </tr>
            <tr>
                <td valign="top">Kemungkinan Terbesar Terkena Penyakit </td>
                <?php
                $perintah = "SELECT * from penyakit where id_penyakit = '$id_penyakit_terbesar'";
                $minta =mysql_query($perintah);
                $hs=mysql_fetch_array($minta);
                //$id_penyakit_terbesar
                ?>
                <td><strong><?php echo $hs['nama_penyakit']; ?> </strong></td>
            </tr>
            <tr>
              <td>CF</td>
              <td><strong><?php echo $cf_terbesar; ?></strong></td>
              </tr>

            <tr>
              <td>&nbsp;</td>
              <td>&nbsp;</td>
              </tr>
        </table>
        </p>
        </td>
        </tr>
        </table>
    <?php
    }
    ?>
</td>
  </tr>
  <tr>
    <td bgcolor="#FFFFFF"><table width="100%" border="0" cellspacing="0" cellpadding="0">
      <tr>
        <td width="47%" height="35" align="left"><strong>&copy; 2014 ContohProgram.com</strong></td>
        <td width="53%" height="35" align="right"><strong><a href="http://contohprogram.com" target="_blank">Kontak</a> | <a href="http://contohprogram.com/cf-php-mysql-source-code.php" target="_blank">About</a><a href="http://contohprogram.com/wp-php-mysql.php" target="_blank"></a></strong></td>
      </tr>
    </table></td>
  </tr>
</table>
</body>
</html>