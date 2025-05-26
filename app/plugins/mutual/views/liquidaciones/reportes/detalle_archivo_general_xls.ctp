<?php 
//debug($liquidacion);
//debug($datos);
//exit;

ini_set("memory_limit", "500M"); // set in php.ini, I cannot change this
set_time_limit(0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();
$oPHPExcel->createSheet(1);

$oPHPExcel->getActiveSheet()->setTitle('CONTROL GENERAL DE ARCHIVOS');
$offSet = 5;

$oPHPExcel->getActiveSheet()->setCellValue("A1","ORGANISMO:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A2","PERIODO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

if(!empty($datos)):
	$i=0;
	foreach ($datos[0] as $field => $value) {
		$columnName = Inflector::humanize($field);
		$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
	}
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('969696');
	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
	for ($j=1; $j<$i; $j++) {
		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
	}
	
	
	
	$i = $offSet + 1;
	foreach ($datos as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;

$oPHPExcel->setActiveSheetIndex(1);
$oPHPExcel->getActiveSheet()->setTitle('RESUMEN NO COBRADOS');
$offSet = 5;

$oPHPExcel->getActiveSheet()->setCellValue("A1","ORGANISMO:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A2","PERIODO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

if(!empty($nocob)):
	$i=0;
	foreach ($nocob[0] as $field => $value) {
		$columnName = Inflector::humanize($field);
		$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
	}
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('969696');
	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
	for ($j=1; $j<$i; $j++) {
		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
	}
	
	
	
	$i = $offSet + 1;
	foreach ($nocob as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="reporte.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>