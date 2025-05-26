<?php 
//debug($liquidacion);

Configure::write('debug',0);

$fileName = "control_diskette_cjp".$liquidacion['Liquidacion']['periodo'].".csv";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	

//$socios = Set::extract('{n}.LiquidacionSocio',$socios);

//debug($socios);
//exit;

echo "DOCUMENTO,SOCIO,TIPO,LEY,BENEFICIO,SUB_BENEFICIO,CODIGO_DTO,IMPORTE,STATUS,CRITERIO\n";

if(!empty($socios)):

	foreach($socios as $socio):

	echo $socio['LiquidacionSocio']['documento'].",";
	echo str_replace(","," ",utf8_encode($socio['LiquidacionSocio']['apenom'])).",";
	echo $socio['LiquidacionSocio']['tipo'].",";
	echo $socio['LiquidacionSocio']['ley'].",";
	echo substr(str_pad(trim($socio['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6).",";
	echo $socio['LiquidacionSocio']['sub_beneficio'].",";
	echo $socio['LiquidacionSocio']['codigo_dto']."-".$socio['LiquidacionSocio']['sub_codigo'].",";
	echo $socio['LiquidacionSocio']['sub_beneficio'].",";
	echo number_format($socio['LiquidacionSocio']['importe_adebitar'],2).",";
	echo $socio['LiquidacionSocio']['ERROR_INTERCAMBIO'].",";
	echo str_replace("\n","|",$socio['LiquidacionSocio']['formula_criterio_deuda']);
	echo "\n";
	
	endforeach;

endif;

exit;
?>