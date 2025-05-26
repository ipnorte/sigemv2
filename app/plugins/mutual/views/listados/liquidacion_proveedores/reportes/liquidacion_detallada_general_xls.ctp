<?php 
//debug($cuotas);
//debug($cobrosByCaja);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

ini_set("memory_limit", "500M"); // set in php.ini, I cannot change this
set_time_limit(0);

$oPHPExcel = new PHPExcel();
//$hojas = 1;
//if(!empty($cuotas)){
//	$oPHPExcel->createSheet(1);
//	$hojas = 1;
//}
//if(!empty($noCobradosBanco)){
//	$oPHPExcel->createSheet(2);
//	$hojas++;
//}
//if(!empty($cobrosByCaja)){
//	$oPHPExcel->createSheet(1);
//	$hojas++;
//}
//if(!empty($reversos)){
//	$oPHPExcel->createSheet(4);
//	$hojas++;
//}

################################################################################################################
#	ARMO LOS TITULOS
################################################################################################################

$fileName = "liquidacion_" . $periodo_desde ."_".$periodo_hasta."_".$codigo_organismo."_".$proveedor['Proveedor']['id'].".xls";

//for ($j=0; $j <= $hojas; $j++):

$oPHPExcel->setActiveSheetIndex(0);

$oPHPExcel->getActiveSheet()->setTitle('REPORTE_ACUMULADO');

$oPHPExcel->getActiveSheet()->setCellValue('A1','LIQUIDACION:');
$oPHPExcel->getActiveSheet()->setCellValue('B1',$util->periodo($periodo_desde) . " / " . $util->periodo($periodo_hasta));

$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);
$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	

$oPHPExcel->getActiveSheet()->setCellValue('A3','PROVEEDOR:');
$oPHPExcel->getActiveSheet()->setCellValue('B3',$proveedor['Proveedor']['razon_social']);

$oPHPExcel->getActiveSheet()->setCellValue('A4','ORGANISMO:');
$oPHPExcel->getActiveSheet()->setCellValue('B4',$util->globalDato($codigo_organismo));	

$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(14);
$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setSize(14);
$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);	
	

//endfor;

$offSet = 8;

################################################################################################################
#	GENERO LA HOJA CON LOS COBRADOS
################################################################################################################
if(!empty($cuotas)):

	$oPHPExcel->setActiveSheetIndex(0);
	$oPHPExcel->getActiveSheet()->setTitle('DETALLE DE LA LIQUIDACION');
	
	$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DE LA LIQUIDACION');
	$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

	$i=0;
	foreach ($cuotas[0] as $field => $value) {
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
	foreach ($cuotas as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;



################################################################################################################
#	GENERO LA HOJA CON LOS DEBITOS NO COBRADOS
################################################################################################################


if(!empty($noCobradosBanco)):

	$oPHPExcel->setActiveSheetIndex(0);
	$oPHPExcel->getActiveSheet()->setTitle('DEBITOS NO COBRADOS');

	$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DEBITOS NO COBRADOS INFORMADOS POR BANCO');
	$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);


	$i=0;
	foreach ($noCobradosBanco[0] as $field => $value) {
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
	foreach ($noCobradosBanco as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}
endif;


################################################################################################################
#	GENERO LA HOJA CON OTROS PAGOS (CAJA Y CANCELACIONES)
################################################################################################################

if(!empty($cobrosByCaja)):

	$oPHPExcel->setActiveSheetIndex(0);
	$oPHPExcel->getActiveSheet()->setTitle('OTRAS COBRANZAS');
	
	$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'INFORME DE COBRANZAS NO EFECTUADAS POR RECIBO DE SUELDO');
	$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);


	$i=0;
	foreach ($cobrosByCaja[0] as $field => $value) {
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
	foreach ($cobrosByCaja as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}


endif;

################################################################################################################
#	GENERO LA HOJA CON LOS REVERSOS
################################################################################################################

if(!empty($reversos)):

	$oPHPExcel->setActiveSheetIndex(0);
	$oPHPExcel->getActiveSheet()->setTitle('REVERSOS');
	
	$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE CUOTAS REVERSADAS');
	$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

	$i=0;
	foreach ($reversos[0] as $field => $value) {
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
	foreach ($reversos as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}


endif;


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header("Content-Disposition: attachment;filename=$fileName");
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>
