<?php
  if (isset($_POST['button']))
    {
        // group kemungkinan terdapat penyakit
        $groupKemungkinanPenyakit = $crud->getGroupPengetahuan(implode(",", $_POST['gejala']));

        // menampilkan kode gejala yang di pilih
        $sql = $_POST['gejala'];

        if (isset($sql)) {
            // mencari data penyakit kemungkinan dari gejala
            for ($h=0; $h < count($sql); $h++) {
                $kemungkinanPenyakit[] = $crud->getKemungkinanPenyakit($sql[$h]);
                for ($x=0; $x < count($kemungkinanPenyakit[$h]); $x++) {
                    for ($i=0; $i < count($groupKemungkinanPenyakit); $i++) {
                        $namaPenyakit = $groupKemungkinanPenyakit[$i]['nama_penyakit'];
                        if ($kemungkinanPenyakit[$h][$x]['nama_penyakit'] == $namaPenyakit) {
                            // list di kemungkinan dari gejala
                            $listIdKemungkinan[$namaPenyakit][] = $kemungkinanPenyakit[$h][$x]['id_pengetahuan'];
                        }
                    }
                }
            }

            $id_penyakit_terbesar = '';
            $nama_penyakit_terbesar = '';
            // list penyakit kemungkinan
            for ($h=0; $h < count($groupKemungkinanPenyakit); $h++) { 
                $namaPenyakit = $groupKemungkinanPenyakit[$h]['nama_penyakit'];
                echo "<br/>Proses Penyakit ".$h.".".$namaPenyakit."<br/>==============<br/>";
                
                // list penyakit kemungkinan dari gejala
                for ($x=0; $x < count($listIdKemungkinan[$namaPenyakit]); $x++) { 
                    $daftarKemungkinanPenyakit = $crud->getListPenyakit($listIdKemungkinan[$namaPenyakit][$x]);
                    
                    echo "<br/>proses ".$x."<br/>------------------------------------<br/>";
                            
                    for ($i=0; $i < count($daftarKemungkinanPenyakit); $i++) {
                        
                        if (count($listIdKemungkinan) == 1) {
                            echo "Jumlah Gejala = ".
                                count($listIdKemungkinan[$namaPenyakit])."<br/>";
                                
                            // bila list kemungkinan terdapat 1
                            $mb = $daftarKemungkinanPenyakit[$i]['mb'];
                            $md = $daftarKemungkinanPenyakit[$i]['md'];
                            $cf = $mb - $md;
                            $daftar_cf[$namaPenyakit][] = $cf;

                            echo "<br/>proses 1<br/>------------------------<br/>";
                            echo "mb = ".$mb."<br/>";
                            echo "md = ".$md."<br/>";
                            echo "cf = mb - md = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";
                            // end bila list kemungkinan terdapat 1
                        } else {
                            // list kemungkinanan lebih dari satu
                            if ($x == 0)
                            {
                                echo "Jumlah Gejala = ".
                                    count($listIdKemungkinan[$namaPenyakit])."<br/>";
                                // record md dan mb sebelumnya
                                $mblama = $daftarKemungkinanPenyakit[$i]['mb'];
                                $mdlama = $daftarKemungkinanPenyakit[$i]['md'];
                                // md yang di esekusi
                                $mb = $daftarKemungkinanPenyakit[$i]['mb'];
                                $md = $daftarKemungkinanPenyakit[$i]['md'];
                                echo "<br/>";
                                echo "mbbaru = ".$mb."<br/>";
                                echo "mdbaru = ".$md."<br/>";
                                $cf = $mb - $md;
                                echo "cf = mb - md = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";

                                $daftar_cf[$namaPenyakit][] = $cf;
                            } 
                            else
                            {
                                $mbbaru = $daftarKemungkinanPenyakit[$i]['mb'];
                                $mdbaru = $daftarKemungkinanPenyakit[$i]['md'];
                                echo "mbbaru = ".$mbbaru."<br/>";
                                echo "mdbaru = ".$mdbaru."<br/>";
                                $mbsementara = $mblama + ($mbbaru * (1 - $mblama));
                                $mdsementara = $mdlama + ($mdbaru * (1 - $mdlama));
                                echo "mbsementara = mblama + (mbbaru * (1 - mblama)) = $mblama + ($mbbaru * (1 - $mblama)) = ".$mbsementara."<br/>";
                                echo "mdsementara = mdlama + (mdbaru * (1 - mdlama)) = $mdlama + ($mdbaru * (1 - $mdlama)) = ".$mdsementara."<br/>";

                                $mb = $mbsementara;
                                $md = $mdsementara;
                                $cf = $mb - $md;
                                echo "cf = mblama - mdlama = ".$mb." - ".$md." = ".$cf."<br/><br/><br/>";
                                $daftar_cf[$namaPenyakit][] = $cf;;
                            }
                            // end list kemungkinanan lebih dari satu
                        }
                    }
                }
            
            }
        }

        /* */

        $arrays = [
            [2, 3, 5],
            [3, 4, 8],
            [3, 4, 5, 8, 9],
        ];
        $flattened = [];
        foreach ($arrays as $array) {
            foreach ($array as $element) {
              $flattened[] = $element;
            }
        }
        for ($x = 0; $x < count($flattened) - 1; $x++) {
          for ($y = $x + 1; $y < count($flattened); $y++) {
            if ($flattened[$x] < $flattened[$y]) {
              $temp = $flattened[$x];
              $flattened[$x] = $flattened[$y];
              $flattened[$y] = $temp;

              $w = $flattened[$y];
            }
          }
        }

        /* */

        for ($i=0; $i < count($groupKemungkinanPenyakit); $i++) { 
            $namaPenyakit = $groupKemungkinanPenyakit[$i]['nama_penyakit'];
            echo "<br/>Nama Penyakit = ".$namaPenyakit."<br/>";
            for ($x=0; $x < count($daftar_cf[$namaPenyakit]); $x++) {
                $merubahIndexCF = max($daftar_cf[$namaPenyakit]);
            }

            echo "Nilai CF Tertinggi Di Kandidat Penyakit = ".$merubahIndexCF."<br>";
            echo "<br/>======================================<br/>";
        }
    }
?>