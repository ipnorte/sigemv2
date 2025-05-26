<?php 
App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();



$oPHPExcel->setActiveSheetIndex(0);
$oPHPExcel->getActiveSheet()->setTitle('LISTADO_DEUDA_SOCIO');

$oPHPExcel->getActiveSheet()->setCellValue('A1','LISTADO DE DEUDA CONSOLIDADO POR SOCIO');
$oPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setBold(true);

$offSet = 4;

if(!empty($codigo_organismo)){
	$oPHPExcel->getActiveSheet()->setCellValue('A3',"ORGANISMO");	
	$oPHPExcel->getActiveSheet()->setCellValue('B3',$util->globalDato($codigo_organismo));
	$offSet += 1;
}
if(!empty($periodo_corte)){
	$oPHPExcel->getActiveSheet()->setCellValue('A4',"PERIODO DE CORTE");
	$oPHPExcel->getActiveSheet()->setCellValue('B4',$util->periodo($periodo_corte,true));
	$offSet += 1;
}
if(!empty($proveedor)){
	$oPHPExcel->getActiveSheet()->setCellValue('A5',"PROVEEDOR");
	$oPHPExcel->getActiveSheet()->setCellValue('B5',$proveedor);
	$offSet += 1;
}
if(!empty($tipo_producto)){
	$oPHPExcel->getActiveSheet()->setCellValue('A6',"PRODUCTO");
	$oPHPExcel->getActiveSheet()->setCellValue('B6',$util->globalDato($tipo_producto));
	$offSet += 1;
}

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
//	for ($j=1; $j<$i; $j++) {
//		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
//	}
	
	
	
	$i = $offSet + 1;
	foreach ($datos as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="listado_deuda_socios.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>