<?php 

/**
 * SALIDA DISKETTE PROVEEDOR URGENCIAS
 * 
 * FORMATO
 * NRO_DOCUMENTO 9 COMPLETA CON CEROS A LA IZQUIERDA
 * SEXO 1 (M/F)
 * PERIODO 6 AAAAMM
 * IMPORTE 7 (4 ENTEROS + . + 2 DECIMALES)
 * 
 */

//debug($cobrados);
//exit;

Configure::write('debug',0);
$fileName = "URGENCIAS_".$liquidacion['Liquidacion']['periodo'].".txt";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');

if(!empty($registros['urgencias'])):
	foreach($registros['urgencias'] as $registro):
		echo $registro . "\r\n";
	endforeach;
endif;

?>