<?php 
/**
 * diskette caja de jubilaciones de cordoba
 */
//Configure::write('debug',0);
//$fileName = "CODIGO_207".$codDto.($nuevoFormato==1 ? "_FN" . ($codDto == 0 ? ($cuotaSocialAB == 'A' ? "_ALTAS" : "_BAJAS") : "") : "").".txt";

$INI_FILE = parse_ini_file(CONFIGS.'mutual.ini', true);
$CJP_COD = (isset($INI_FILE['intercambio']['CJP_COD_CSOC']) && !empty($INI_FILE['intercambio']['CJP_COD_CSOC']) ? $INI_FILE['intercambio']['CJP_COD_CSOC'] : "207");

if($codDto == 0){
	if($altaBaja == 'A') $fileName = "ALTAS_MODIFICACIONES_".$CJP_COD."_".$codDto.".txt";
	else $fileName = "BAJAS_".$CJP_COD."_".$codDto.".txt";
}else{
	if($nuevoFormato==1){
		
		$fileName = "CONSUMOS_".$CJP_COD."_".$codDto.($altaBaja == 'A' ? "_ALTAS" : ($altaBaja == 'B' ? "_BAJAS" : "")).".txt";
		
	}else{
		$fileName = "CONSUMOS_".$CJP_COD."_".$codDto.".txt";
	}
}

//debug($socios);
//exit;

header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	
foreach($socios as $socio){
	if($altaBaja == 'B') echo substr($socio['LiquidacionSocio']['intercambio'],0,23).'0000000000000000000000000000000000000000000000000000'.substr($socio['LiquidacionSocio']['intercambio'],75,6)."\r\n";
	else echo str_replace("\r\n","",$socio['LiquidacionSocio']['intercambio'])."\r\n";
}

?>