<?php
	// untuk memanggil file
	include 'Crud.php';
	// untuk mendeklarasikan class menjadi variabel
	$crud = new Crud();
	$arrayName = $crud->readGejala();
 ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Sistem Pakar Metode CF (Certainty Factor)</title>
<!-- Latest compiled and minified CSS -->
<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/css/bootstrap.min.css">

<!-- jQuery library -->
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.2.1/jquery.min.js"></script>
<!-- Popper JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.6/umd/popper.min.js"></script>
<!-- Latest compiled JavaScript -->
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta.2/js/bootstrap.min.js"></script>
</head>

<body>
	<div class="container">
		<div class="row">
		  <div class="col-lg-12">
				<h1 style="text-align: center;">Sistem Pakar Metode CF (Certainty Factor)</h1>	  	
		  </div>
		</div>
	</div>
	
	<div class="container">
		<div class="col-lg-12">
			<table width="1000" border="0" align="center" cellpadding="0" cellspacing="1" bgcolor="#000099">
			  <tr>
			    <td align="center" valign="top" bgcolor="#FFFFFF"><br />
					<form name="form1" method="post" action="hasil.php"><br>
					  <table align="center" width="600" border="1" cellspacing="0" cellpadding="5">
					  <tr>
					  <td id="ignore" bgcolor="#DBEAF5" width="300"><div align="center"><strong><font size="2" face="Arial, Helvetica, sans-serif"><font size="2">GEJALA</font> </font></strong></div></td>
					  <?php
					    	foreach ($arrayName as $r)
						  {
						?>
					    <tr>
					      <td width="600">
					        <input id="gejala<?php echo $r['id_gejala']; ?>" name="gejala[]" type="checkbox" value="<?php echo $r['id_gejala']; ?>">
					        <?php echo $r['nama_gejala']; ?><br/>
					        </td>
					    </tr>
					    <?php
					      }
					     ?>
					    <tr>
					      <td><input type="submit" name="button" value="Proses"></td>
					    </tr>
					  </table>
					  <br>
					</form>
				</td>
			  </tr>
			</table>
		</div>
	</div>
</body>
</html>
