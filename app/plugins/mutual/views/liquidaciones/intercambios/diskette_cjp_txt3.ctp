<?php 
/**
 * diskette caja de jubilaciones de cordoba
 */
//Configure::write('debug',0);
//$fileName = "CODIGO_207".$codDto.($nuevoFormato==1 ? "_FN" . ($codDto == 0 ? ($cuotaSocialAB == 'A' ? "_ALTAS" : "_BAJAS") : "") : "").".txt";


if($codDto == 0){
	if($altaBaja == 'A') $fileName = "ALTAS_MODIFICACIONES_207".$codDto.".txt";
	else $fileName = "BAJAS_207".$codDto.".txt";
}else{
	if($nuevoFormato==1){
		
		$fileName = "CONSUMOS_207".$codDto.($altaBaja == 'A' ? "_ALTAS" : ($altaBaja == 'B' ? "_BAJAS" : "")).".txt";
		
	}else{
		$fileName = "CONSUMOS_207".$codDto.".txt";
	}
}

//debug($socios);
//exit;

header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	
foreach($socios as $socio){
	if($altaBaja == 'B') echo substr($socio['LiquidacionSocioNoimputada']['intercambio'],0,23).'0000000000000000000000000000000000000000000000000000'.substr($socio['LiquidacionSocioNoimputada']['intercambio'],75,6)."\r\n";
	else echo str_replace("\r\n","",$socio['LiquidacionSocioNoimputada']['intercambio'])."\r\n";
}

?>