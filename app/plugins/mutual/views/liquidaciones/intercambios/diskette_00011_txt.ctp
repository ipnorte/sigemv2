<?php 
/**
 * NO SE USA MAS
 * diskette banco NACION
 * FORMATO 3 REGISTROS (CABECERA | DETALLE | PIE)
 */
Configure::write('debug',0);
$fileName = "NACION".$liquidacion['Liquidacion']['periodo'].".TXT";
header("Content-type: text/plain"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');	

//IMPRIMO EL REGISTRO CABECERA

$DATOS_GLOBALES = Configure::read('APLICACION.intercambio_bancos');

echo "1";
echo $DATOS_GLOBALES['sucursal_bco_nacion'];
echo $DATOS_GLOBALES['tipo_cuenta_banco_nacion'];
echo $DATOS_GLOBALES['cuenta_banco_nacion'];
echo $DATOS_GLOBALES['moneda_cuenta_banco_nacion'];
echo "E";
echo date('m',strtotime($fechaDebito));
echo str_pad(trim($nro_archivo), 2, "0", STR_PAD_LEFT);
echo date('Ymd',strtotime($fechaDebito));
echo "EMP";
echo str_pad("", 94, " ", STR_PAD_LEFT);
echo "\r\n";

$registros = 0;
$sumatoria = 0;

foreach($socios as $socio){
	$sumatoria += round($socio['LiquidacionSocio']['importe_adebitar'],2);
	echo $socio['LiquidacionSocio']['intercambio'];
	$registros++;
}
$sumatoria = round($sumatoria,2);

//IMPRIMO EL REGISTRO PIE
echo "3";
echo str_pad($sumatoria * 100, 15, '0', STR_PAD_LEFT);
echo str_pad($registros, 6, '0', STR_PAD_LEFT);
echo str_pad("", 15, '0', STR_PAD_LEFT);
echo str_pad("", 6, '0', STR_PAD_LEFT);
echo str_pad("", 85, " ", STR_PAD_LEFT);
echo "\r\n";

?>