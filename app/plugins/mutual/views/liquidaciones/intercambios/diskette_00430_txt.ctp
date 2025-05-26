<?php 
/**
 * NO SE USA MAS
 * diskette banco standar
 */
Configure::write('debug',0);
$fileName = "STBANK_".date('Ymd',strtotime($fechaDebito)).".txt";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');

//IMPRIMO EL REGISTRO CABECERA
$cadena = "1";
$cadena .= Configure::read('APLICACION.cuit_mutual');
$cadena .= "0";
$cadena .= "CUOTA PTMO";
$cadena .= date('Ymd',strtotime($fechaDebito));
$cadena .= str_pad("", 69, " ", STR_PAD_LEFT);
$cadena .= "\r\n";
echo $cadena;

$registros = 0;
$sumatoria = 0;

foreach($socios as $socio){
	$sumatoria += round((double)$socio['LiquidacionSocio']['importe_adebitar'],2);
	echo $socio['LiquidacionSocio']['intercambio'];
	$registros++;
}
$sumatoria = (float) $sumatoria;
$sumatoria = $sumatoria * 100;
$sumatoria = number_format($sumatoria,0,"","");
//imprimo el registro pie
$cadena = "9";
$cadena .= str_pad($registros, 8, '0', STR_PAD_LEFT);
$cadena .= str_pad($sumatoria, 14, '0', STR_PAD_LEFT);
$cadena .= str_pad("", 77, " ", STR_PAD_LEFT);
$cadena .= "\r\n";
echo $cadena;


?>