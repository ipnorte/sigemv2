<?php 

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();


$oPHPExcel->getActiveSheet()->setTitle('PLAN DE CUENTA');
$offSet = 4;

$oPHPExcel->getActiveSheet()->setCellValue('A1', $ejercicioDescripcion);

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'PLAN DE CUENTA');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
if(!empty($planCuenta)):
	$i=0;
	foreach ($planCuenta[0] as $field => $value) {
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
	foreach ($planCuenta as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="plan_cuenta.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>