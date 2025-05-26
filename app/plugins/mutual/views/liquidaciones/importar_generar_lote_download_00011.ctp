<?php 
$registros = explode("\r\n",$envio['LiquidacionSocioEnvio']['lote']);
// debug($envio);
// exit;
foreach($registros as $id => $registro):

	if($id > 0){
		$registroDetalle = $envio['LiquidacionSocioEnvioRegistro'][$id - 1];
		$codigo = str_pad(trim($registroDetalle['descripcion_codigo']), 30, " ", STR_PAD_RIGHT);
		$cadena_1 = substr($registro,0,42);
		$cadena_2 = substr($registro,72,56);
		if($id < count($registros) - 1){
			$registros[$id] = $cadena_1.$codigo.$cadena_2;
		}
	}
endforeach;

Configure::write('debug',0);
$fileName = "NACION_LOTE_COBRANZA_GENERADO.txt";
header("Content-type: text/plain");
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');

foreach($registros as $registro):

	if(!empty($registro) || strlen($registro) > 5) echo $registro."\r\n";

endforeach;

?>