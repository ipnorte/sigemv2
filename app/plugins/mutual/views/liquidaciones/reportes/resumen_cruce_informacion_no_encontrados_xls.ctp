<?php 
//debug($socios);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle("NO RENDIDOS A LA FECHA");

$oPHPExcel->getActiveSheet()->setCellValue("A1","NO RENDIDOS A LA FECHA");
$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->setCellValue("A2","LIQUIDACION");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true) . " - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B4","APELLIDO_Y_NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","BENEFICIO");
$oPHPExcel->getActiveSheet()->setCellValue("D4","A_DEBITAR");

$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:D4");


if(!empty($socios)):

	$i = 5;
	
	foreach($socios as $socio):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($socio['LiquidacionSocio']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValue(utf8_encode($socio['LiquidacionSocio']['apenom']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($socio['LiquidacionSocio']['beneficio_str']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValue($socio[0]['importe_adebitar']);
		$i++;
	
	endforeach;
	
endif;	



$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="NoRendidos.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>