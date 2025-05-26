<?php 

//debug($reintegros);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle("DETALLE DE REINTEGROS");

$oPHPExcel->getActiveSheet()->setCellValue("A1","DETALLE DE REINTEGROS");
$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->setCellValue("A2","LIQUIDACION");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true) . " - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B4","APELLIDO_Y_NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("D4","EMPRESA");
$oPHPExcel->getActiveSheet()->setCellValue("E4","TURNO");
$oPHPExcel->getActiveSheet()->setCellValue("F4","LIQUIDADO");
$oPHPExcel->getActiveSheet()->setCellValue("G4","DEBITADO");
$oPHPExcel->getActiveSheet()->setCellValue("H4","IMPUTADO");
$oPHPExcel->getActiveSheet()->setCellValue("I4","REINTEGRO");
$oPHPExcel->getActiveSheet()->setCellValue("J4","ANTICIPOS");
$oPHPExcel->getActiveSheet()->setCellValue("K4","SALDO");
$oPHPExcel->getActiveSheet()->setCellValue("L4","CODIGO");
$oPHPExcel->getActiveSheet()->setCellValue("M4","BANCO");


$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:M4");


if(!empty($reintegros)):

	$i = 5;
	
	foreach($reintegros as $reintegro):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($reintegro['LiquidacionSocio']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValue(utf8_encode($reintegro['LiquidacionSocio']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($reintegro['LiquidacionSocio']['beneficio_str']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($reintegro['LiquidacionSocio']['codigo_empresa_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($reintegro['LiquidacionSocio']['turno_pago_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($reintegro['LiquidacionSocio']['importe_dto']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValue($reintegro['LiquidacionSocio']['importe_debitado']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($reintegro['LiquidacionSocio']['importe_imputado']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($reintegro['LiquidacionSocio']['importe_reintegro']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValue($reintegro['LiquidacionSocio']['importe_anticipado']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValue($reintegro['LiquidacionSocio']['saldo_reintegro']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValueExplicit($reintegro['LiquidacionSocio']['banco_intercambio']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValue($reintegro['LiquidacionSocio']['banco_intercambio_nombre']);
        
		$i++;
	
	endforeach;
	
endif;	



$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="reintegros.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>