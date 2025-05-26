<?php 

//debug($stops);
//exit;

ini_set("memory_limit", "1000M"); // set in php.ini, I cannot change this
set_time_limit(0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->createSheet(1);
$oPHPExcel->createSheet(2);
$oPHPExcel->createSheet(3);
$oPHPExcel->createSheet(4);
$oPHPExcel->createSheet(5);
$hojas = 5;
//if(!empty($cobrosByCaja)){
//	$oPHPExcel->createSheet(3);
//	$hojas++;
//}
//if(!empty($bajas)){
//	$oPHPExcel->createSheet(4);
//	$hojas++;
//}

################################################################################################################
#	ARMO LOS TITULOS
################################################################################################################

for ($j=0; $j <= $hojas; $j++):

	$oPHPExcel->setActiveSheetIndex($j);
	
	$oPHPExcel->getActiveSheet()->setCellValue('A1','LIQUIDACION :');
	$oPHPExcel->getActiveSheet()->setCellValue('B1',$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $util->periodo($liquidacion['Liquidacion']['periodo']) . ($procesarSobrePreImputacion == 1 ? " ** PRE-IMPUTADA **" : ""));

	$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);
	$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
		
	
	$oPHPExcel->getActiveSheet()->setCellValue('A3','PROVEEDOR:');
	$oPHPExcel->getActiveSheet()->setCellValue('B3',$proveedor['Proveedor']['razon_social']);
	$oPHPExcel->getActiveSheet()->setCellValue('A4','CONCEPTO:');
	$oPHPExcel->getActiveSheet()->setCellValue('B4',$util->globalDato($tipo_producto)." - ". $util->globalDato($tipo_cuota));
	
	$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(14);
	$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setSize(10);
	$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);
	
	

endfor;

$offSet = 8;

################################################################################################################
#	GENERO LA HOJA CON LOS COBRADOS
################################################################################################################
$oPHPExcel->setActiveSheetIndex(0);
$titleCell = 'DETALLE ' . ($procesarSobrePreImputacion == 1 ? " (PRE-IMPUTACION)" : "" );
$oPHPExcel->getActiveSheet()->setTitle($titleCell);

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DE LA LIQUIDACION ' . ($procesarSobrePreImputacion == 1 ? " *** PRE-IMPUTADA ***" : "" ));
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
if(!empty($cuotas)):
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
#	GENERO LA HOJA CON LOS NO COBRADOS
################################################################################################################
//
//
//$oPHPExcel->setActiveSheetIndex(1);
//$oPHPExcel->getActiveSheet()->setTitle('NO COBRADOS');
//
//$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DE CUOTAS NO COBRADAS');
//$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
//
//if(!empty($noCobrados)):
//	$i=0;
//	foreach ($noCobrados[0] as $field => $value) {
//		$columnName = Inflector::humanize($field);
//		$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
//	}
//	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true);
//	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('969696');
//	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
//	for ($j=1; $j<$i; $j++) {
//		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
//	}
//	
//	
//	$i = $offSet + 1;
//	foreach ($noCobrados as $row) {
//		$j=0;
//		foreach ($row as $field => $value) {
//			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
//		}
//		$i++;
//	}
//endif;



################################################################################################################
#	GENERO LA HOJA CON LOS DEBITOS NO COBRADOS
################################################################################################################
$oPHPExcel->setActiveSheetIndex(1);
$oPHPExcel->getActiveSheet()->setTitle('DEBITOS NO COBRADOS');

if(!empty($noCobradosBanco)):

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
#	GENERO LA HOJA CON LOS REVERSOS
################################################################################################################

$oPHPExcel->setActiveSheetIndex(2);
$oPHPExcel->getActiveSheet()->setTitle('REVERSOS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE CUOTAS REVERSADAS');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

if(!empty($reversos)):

$i=0;

$i = $offSet + 1;

$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$i,"SOCIO");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$i,"TIPO_NUMERO");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$i,"REF_PROVEEDOR");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$i,"PRODUCTO_CONCEPTO");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$i,"CUOTA");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$i,"PERIODO");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$i,"IMPORTE_REVERSADO");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$i,"%_COMISION");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$i,"COMISION_REVERSADA");
$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9,$i,"NETO_PROVEEDOR");


$oPHPExcel->getActiveSheet()->getStyle("A$i")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A$i")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$i"), "B$i:".$oPHPExcel->getActiveSheet()->getHighestColumn().$i);


$i++;

foreach ($reversos as $reverso) {

	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['socio']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_nro']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['nro_referencia_proveedor']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['producto_cuota']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['cuota']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$i,utf8_encode($util->periodo($reverso['OrdenDescuentoCobroCuota']['cuota']['periodo'])));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$i,$reverso['OrdenDescuentoCobroCuota']['importe_reversado']);
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$i,$reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza']);
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$i,$reverso['OrdenDescuentoCobroCuota']['comision_cobranza']);
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9,$i,$util->nf($reverso['OrdenDescuentoCobroCuota']['importe'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza']));
	$i++;
}


endif;


################################################################################################################
#	GENERO LA HOJA CON OTROS PAGOS (CAJA Y CANCELACIONES)
################################################################################################################
$oPHPExcel->setActiveSheetIndex(3);
$oPHPExcel->getActiveSheet()->setTitle('OTRAS COBRANZAS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'INFORME DE COBRANZAS NO EFECTUADAS POR RECIBO DE SUELDO');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

if(!empty($cobrosByCaja)):

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
#	GENERO LA HOJA CON DETALLE DE BAJAS
################################################################################################################
$oPHPExcel->setActiveSheetIndex(4);
$oPHPExcel->getActiveSheet()->setTitle('BAJAS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'INFORME DE CUOTAS CORRESPONDIENTES AL PERIODO DADAS DE BAJA');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);


if(!empty($bajas)):

	$i=0;
	foreach ($bajas[0] as $field => $value) {
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
	foreach ($bajas as $row) {
		$j=0;
		foreach ($row as $field => $value) {
			$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($j++,$i, utf8_encode($value));
		}
		$i++;
	}


endif;


################################################################################################################
#	GENERO LA HOJA CON REGISTRO DE STOPS ANTERIORES
################################################################################################################
$oPHPExcel->setActiveSheetIndex(5);
$oPHPExcel->getActiveSheet()->setTitle('STOP_DEBIT');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'INFORME DE STOP DEBIT ANTERIORES');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
if(!empty($stops)):

	$i=0;
	foreach ($stops[0] as $field => $value) {
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
	foreach ($stops as $row) {
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
