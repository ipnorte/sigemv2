<?php 
//debug($cobrados);
//exit;
//debug($liquidacion);
//exit;

//debug($registros);
//exit;

$provName = str_replace(".","",$proveedor['Proveedor']['razon_social_resumida']);
$provName = str_replace(" ","_",$provName);

$organismo = str_replace(".","",$liquidacion['Liquidacion']['organismo']);
$organismo = str_replace(" ","",$organismo);
$periodo = str_replace("-","",$liquidacion['Liquidacion']['periodo']);


Configure::write('debug',0);
$fileName = $provName."_".$organismo."_".$periodo.".txt";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	
//if(!empty($cobrados)):
//	foreach($cobrados as $dato){
//		echo trim($dato['texto_15']);
//		echo "\r\n";
//	}
//endif;

if(!empty($registros)){
    if(!empty($registros['cabecera'])) echo $registros['cabecera'] . "\r\n";
    foreach($registros['detalle'] as $registro){
        if(!empty($registro)) echo $registro . "\r\n";
    }
    if(!empty($registros['pie'])) echo $registros['pie'] . "\r\n";
}

?>