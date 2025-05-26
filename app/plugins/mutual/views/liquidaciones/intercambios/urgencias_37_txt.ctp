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

if(!empty($cobrados)):


	foreach($cobrados as $dato):
	
		echo str_pad(trim($dato['texto_11']), 9, '0', STR_PAD_LEFT);		
		echo trim($dato['texto_12']);
		echo $liquidacion['Liquidacion']['periodo'];
		$impo = number_format($dato['decimal_2'] * 100,0,"","");
		$impo = str_pad(trim($impo), 6, '0', STR_PAD_LEFT);
		echo substr($impo,0,4).".".substr($impo,4,2);
		echo "\r\n";
//		echo "</br>";
		
	endforeach;


endif;

?>