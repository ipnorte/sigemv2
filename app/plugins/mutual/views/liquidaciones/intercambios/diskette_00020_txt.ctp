<?php 
/**
 * NO SE USA MAS
 * diskette banco cordoba
 */
Configure::write('debug',0);
$fileName = "DEB8679.HAB";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	
foreach($socios as $socio){
	echo $socio['LiquidacionSocio']['intercambio'];
}

?>