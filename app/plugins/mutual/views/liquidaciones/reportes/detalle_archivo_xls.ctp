<?php 
//debug($liquidacion);
//debug($datos);
//exit;

ini_set("memory_limit", "500M"); // set in php.ini, I cannot change this
set_time_limit(0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();


$oPHPExcel->getActiveSheet()->setTitle($liquidacion['LiquidacionIntercambio']['archivo_nombre']);


$oPHPExcel->getActiveSheet()->setCellValue("A2","LIQUIDACION");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['LiquidacionIntercambio']['periodo'],true) . " - " . $util->globalDato($liquidacion['LiquidacionIntercambio']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","BANCO");
$oPHPExcel->getActiveSheet()->setCellValue("B3",$liquidacion['LiquidacionIntercambio']['banco_intercambio']);
$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A4","ARCHIVO");
$oPHPExcel->getActiveSheet()->setCellValue("B4",$liquidacion['LiquidacionIntercambio']['archivo_nombre']);
$oPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setBold(true);


//GENERO LAS COLUMNAS
$oPHPExcel->getActiveSheet()->setCellValue("A7","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B7","APELLIDO_Y_NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C7","NRO_SOCIO");
$oPHPExcel->getActiveSheet()->setCellValue("D7","IDENTIFICACION");
$oPHPExcel->getActiveSheet()->setCellValue("E7","EMPRESA");
$oPHPExcel->getActiveSheet()->setCellValue("F7","TURNO");
$oPHPExcel->getActiveSheet()->setCellValue("G7","TURNO_DESC");
$oPHPExcel->getActiveSheet()->setCellValue("H7","IMPORTE");
$oPHPExcel->getActiveSheet()->setCellValue("I7","CODIGO");
$oPHPExcel->getActiveSheet()->setCellValue("J7","DESCRIPCION");
$oPHPExcel->getActiveSheet()->setCellValue("K7","COBRO");
$oPHPExcel->getActiveSheet()->setCellValue("L7","FECHA_DEBITO");
$oPHPExcel->getActiveSheet()->setCellValue("M7","ORDEN_DTO");

$oPHPExcel->getActiveSheet()->getStyle("A7")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A7")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A7")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A7"), "B7:M7");

if(!empty($datos)):

	$i = 8;

	foreach($datos as $dato):
		
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($dato['Persona']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValue(utf8_encode($dato['Persona']['apellido'].", ".$dato['Persona']['nombre']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['socio_id']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['identificacion']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['empresa']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit(substr(trim($dato['LiquidacionSocioRendicion']['turno_pago']),-5,5));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['descripcion']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($dato['LiquidacionSocioRendicion']['importe_debitado']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['status']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['status_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit(($dato['BancoRendicionCodigo']['indica_pago'] == 1 ? 'SI' : 'NO'));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['fecha_debito']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValueExplicit($dato['LiquidacionSocioRendicion']['orden_descuento_id']);
		$i++;
	endforeach;


endif;



$fileName = str_replace(" ","_",$liquidacion['LiquidacionIntercambio']['archivo_nombre']).".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>