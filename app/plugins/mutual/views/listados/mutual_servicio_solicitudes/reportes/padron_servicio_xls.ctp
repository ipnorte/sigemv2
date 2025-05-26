<?php 

//debug($datos);

Configure::write('debug',0);
ini_set("memory_limit", "500M"); // set in php.ini, I cannot change this
set_time_limit(0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle('PADRON');

$oPHPExcel->getActiveSheet()->setCellValue('A1','PADRON: ');
$oPHPExcel->getActiveSheet()->setCellValue('B1',$servicio_desc);
$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue('A2','COBERTURA DESDE: ');
$oPHPExcel->getActiveSheet()->setCellValue('B2',$util->armaFecha($fecha_cobertura_desde));
$oPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);



$offSet = 4;
//$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'RESUMEN IMPUTACION PROVEEDORES');
//$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
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

if(!empty($datos2)):

	$oPHPExcel->createSheet(1);
	$oPHPExcel->setActiveSheetIndex(1);
	
	$oPHPExcel->getActiveSheet()->setTitle('BAJAS');
	
	$oPHPExcel->getActiveSheet()->setCellValue('A1','PADRON: ');
	$oPHPExcel->getActiveSheet()->setCellValue('B1',$servicio_desc);
	$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	
	$oPHPExcel->getActiveSheet()->setCellValue('A2','COBERTURA HASTA: ');
	$oPHPExcel->getActiveSheet()->setCellValue('B2',$util->armaFecha($fecha_cobertura_desde));
	$oPHPExcel->getActiveSheet()->getStyle('B2')->getFont()->setBold(true);
	
	$i=0;
	
	foreach ($datos2[0] as $field => $value) {
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
	foreach ($datos2 as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}	

endif;


$fileName = str_replace(" ","",$servicio_desc);
$fileName .= "_".date('Ymd',strtotime($fecha_cobertura_desde)).".xls";


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');




?>