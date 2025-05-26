<?php 
// debug($descripcion_turno);
// debug($liquidacion);
// debug($socios);
// debug($socios_error_cbu);
// exit;

ini_set("memory_limit", "500M"); // set in php.ini, I cannot change this
set_time_limit(0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle('LISTADO CONTROL TURNO');
$offSet = 5;

$oPHPExcel->getActiveSheet()->setCellValue("A1","LIQUIDACION:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$liquidacion['Liquidacion']['periodo_desc_amp']);
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A2","ORGANISMO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$liquidacion['Liquidacion']['organismo']);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","TURNO:");
$oPHPExcel->getActiveSheet()->setCellValue("B3",(empty($descripcion_turno) ? "*** SIN TURNO DE PAGO ***" : $descripcion_turno));
$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B4","NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","REG");
$oPHPExcel->getActiveSheet()->setCellValue("D4","BANCO");
$oPHPExcel->getActiveSheet()->setCellValue("E4","SUCURSAL");
$oPHPExcel->getActiveSheet()->setCellValue("F4","CUENTA");
$oPHPExcel->getActiveSheet()->setCellValue("H4","CBU");
$oPHPExcel->getActiveSheet()->setCellValue("H4","SALDO");
$oPHPExcel->getActiveSheet()->setCellValue("I4","DEBITO");

$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:I4");


if(!empty($socios)):
	$n = 1;
	$i = 5;
	foreach($socios as $socio):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit(utf8_encode($socio['LiquidacionSocioNoimputada']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValue($socio['LiquidacionSocioNoimputada']['registro']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['banco']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValue($socio['LiquidacionSocioNoimputada']['sucursal']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($socio['LiquidacionSocioNoimputada']['nro_cta_bco']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValue($socio['LiquidacionSocioNoimputada']['cbu']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($socio['LiquidacionSocioNoimputada']['saldo_actual']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($socio['LiquidacionSocioNoimputada']['importe_adebitar']);
		$n++;
		$i++;
	endforeach;


endif;

if(!empty($socios_error_cbu)):
	$oPHPExcel->createSheet(1);
	$oPHPExcel->setActiveSheetIndex(1);
	$oPHPExcel->getActiveSheet()->setTitle('ERROR CBU');
	
	$oPHPExcel->getActiveSheet()->setCellValue("A1","LIQUIDACION:");
	$oPHPExcel->getActiveSheet()->setCellValue("B1",$liquidacion['Liquidacion']['periodo_desc_amp']);
	$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
	
	$oPHPExcel->getActiveSheet()->setCellValue("A2","ORGANISMO:");
	$oPHPExcel->getActiveSheet()->setCellValue("B2",$liquidacion['Liquidacion']['organismo']);
	$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);
	
	$oPHPExcel->getActiveSheet()->setCellValue("A3","TURNO:");
	$oPHPExcel->getActiveSheet()->setCellValue("B3",(empty($descripcion_turno) ? "*** SIN TURNO DE PAGO ***" : $descripcion_turno));
	$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);

	$oPHPExcel->getActiveSheet()->setCellValue("A4","");
	$oPHPExcel->getActiveSheet()->setCellValue("B4","DETALLE DE REGISTROS CON CBU INCORRECTO");
	$oPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setBold(true);	
	
	$oPHPExcel->getActiveSheet()->setCellValue("A5","#");
	$oPHPExcel->getActiveSheet()->setCellValue("B5","DOCUMENTO");
	$oPHPExcel->getActiveSheet()->setCellValue("C5","NOMBRE");
	$oPHPExcel->getActiveSheet()->setCellValue("D5","REG");
	$oPHPExcel->getActiveSheet()->setCellValue("E5","BANCO");
	$oPHPExcel->getActiveSheet()->setCellValue("F5","SUCURSAL");
	$oPHPExcel->getActiveSheet()->setCellValue("G5","CUENTA");
	$oPHPExcel->getActiveSheet()->setCellValue("H5","CBU");
	$oPHPExcel->getActiveSheet()->setCellValue("I5","IMPORTE");
	
	$oPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle("A5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A5")->getFill()->getStartColor()->setRGB('969696');
	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A5"), "B5:I5");

	$n = 1;
	$i = 6;
	foreach($socios_error_cbu as $socio):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValue($n);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit(utf8_encode($socio['LiquidacionSocioNoimputada']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['registro']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($util->banco($socio['LiquidacionSocioNoimputada']['banco_id']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($socio['LiquidacionSocioNoimputada']['sucursal']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValue($socio['LiquidacionSocioNoimputada']['nro_cta_bco']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($socio['LiquidacionSocioNoimputada']['cbu']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($socio['LiquidacionSocioNoimputada']['importe_adebitar']);
		$n++;
		$i++;
	endforeach;	
	

endif;



$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="reporte_detalle_turno.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>