<?php
if(isset($_REQUEST['UID']))$UID = $_REQUEST['UID'];
else exit();

if(isset($_REQUEST['CUPON']))$CUPON = $_REQUEST['CUPON'];
else exit();

$conexionDB = conexionDB();

function conexionDB(){
	$conexion = unserialize(base64_decode($_REQUEST['UID']));
	$link = mysql_connect($conexion['host'],$conexion['login'],$conexion['password'])
	or die ("No se establecio conexion a la base de datos");
	mysql_select_db($conexion['database'],$link);
	return $link;
}


$sql = "select * from solicitud_cupones_anses where id = " . $CUPON;
$result = mysql_query($sql,$conexionDB);

if(mysql_errno($conexionDB)!=0) exit;

while($row = mysql_fetch_assoc($result)){
	$binario = $row['cupon'];
	$type = $row['cupon_type'];
	$size = $row['cupon_size'];
	$fileName = $row['file_cupon'];
}

if(!empty($binario)){
	header("Cache-control: private");
	header("Content-type: $type");
	header("Content-Disposition: inline; filename=".$fileName."");
	header("Content-length: $size");
	header("Expires: ".gmdate("D, d M Y H:i:s", mktime(date("H")+2, date("i"), date("s"), date("m"), date("d"), date("Y")))." GMT");
	header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	print $binario;
	
}



?>