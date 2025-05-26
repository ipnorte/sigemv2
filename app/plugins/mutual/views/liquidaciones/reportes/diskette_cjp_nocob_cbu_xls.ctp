<?php 

//debug($datos);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("CJP ARCHIVO PARA DEBITO POR CBU");


$oPHPExcel->getActiveSheet()->setCellValue("B1","LISTADO CONTROL DEBITO POR CBU - CJP");
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A2","PERIODO");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","REG");
$oPHPExcel->getActiveSheet()->setCellValue("B3","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("C3","SOCIO");
$oPHPExcel->getActiveSheet()->setCellValue("D3","TIPO");
$oPHPExcel->getActiveSheet()->setCellValue("E3","LEY");
$oPHPExcel->getActiveSheet()->setCellValue("F3","BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("G3","SUB_BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("H3","CBU");
$oPHPExcel->getActiveSheet()->setCellValue("I3","SUCURSAL");
$oPHPExcel->getActiveSheet()->setCellValue("J3","CUENTA");
$oPHPExcel->getActiveSheet()->setCellValue("K3","A DEBITAR");
$oPHPExcel->getActiveSheet()->setCellValue("L3","STATUS");

$oPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A3"), "B3:L3");


if(!empty($datos)):

	$reg = 0;
	$i = 4;
	
	
	foreach($datos as $dato):
	
		$reg++;
		
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($reg);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($dato['LiquidacionSocio']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit(utf8_encode($dato['LiquidacionSocio']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($dato['LiquidacionSocio']['tipo']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($dato['LiquidacionSocio']['nro_ley']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit(substr(str_pad(trim($dato['LiquidacionSocio']['nro_beneficio']), 6, '0', STR_PAD_LEFT),-6));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($dato['LiquidacionSocio']['sub_beneficio']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($dato['LiquidacionSocio']['cbu']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($dato['LiquidacionSocio']['cbu_sucursal']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($dato['LiquidacionSocio']['cbu_nro_cta_bco']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValue($dato['LiquidacionSocio']['importe_adebitar']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValueExplicit(utf8_encode($dato['LiquidacionSocio']['ERROR_INTERCAMBIO']));
		$i++;
	
	endforeach;

endif;



$fileName = "control_diskette_cbu_cjp".$liquidacion['Liquidacion']['periodo'].".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');



?>