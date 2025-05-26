<?php 

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->createSheet(1);
$oPHPExcel->createSheet(2);
$oPHPExcel->createSheet(3);

################################################################################################################
#	ARMO LOS TITULOS
################################################################################################################

for ($j=0; $j <= 3; $j++):

	$oPHPExcel->setActiveSheetIndex($j);
	
	$oPHPExcel->getActiveSheet()->setCellValue('A1','LIQUIDACION:');
	$oPHPExcel->getActiveSheet()->setCellValue('B1',$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $util->periodo($liquidacion['Liquidacion']['periodo']));

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
$oPHPExcel->getActiveSheet()->setTitle('COBRADOS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DE CUOTAS COBRADAS');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);
if(!empty($cobrados)):
	$i=0;
	foreach ($cobrados[0] as $field => $value) {
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
	foreach ($cobrados as $row) {
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


$oPHPExcel->setActiveSheetIndex(1);
$oPHPExcel->getActiveSheet()->setTitle('NO COBRADOS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DE CUOTAS NO COBRADAS');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

if(!empty($noCobrados)):
	$i=0;
	foreach ($noCobrados[0] as $field => $value) {
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
	foreach ($noCobrados as $row) {
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
$oPHPExcel->setActiveSheetIndex(2);
$oPHPExcel->getActiveSheet()->setTitle('DEBITOS NO COBRADOS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE DEBITOS NO COBRADOS INFORMADOS POR BANCO');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

if(!empty($noCobradosBanco)):
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

$oPHPExcel->setActiveSheetIndex(3);
$oPHPExcel->getActiveSheet()->setTitle('REVERSOS');

$oPHPExcel->getActiveSheet()->setCellValue('B'.($offSet - 2),'DETALLE CUOTAS REVERSADAS');
$oPHPExcel->getActiveSheet()->getStyle('B'.($offSet - 2))->getFont()->setBold(true);

if(!empty($reversos)):

$i=0;

$i = $offSet + 1;
foreach ($reversos as $reverso) {

	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(0,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['socio']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(1,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['tipo_nro']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(2,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['nro_referencia_proveedor']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(3,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['producto_cuota']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$i,utf8_encode($reverso['OrdenDescuentoCobroCuota']['cuota']['cuota']));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(5,$i,utf8_encode($util->periodo($reverso['OrdenDescuentoCobroCuota']['cuota']['periodo'])));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$i,utf8_encode($util->nf($reverso['OrdenDescuentoCobroCuota']['importe'])));
	
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
