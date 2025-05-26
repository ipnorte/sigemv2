<?php 

//debug($cobrosByCaja);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

//$oPHPExcel->createSheet(1);
//$oPHPExcel->createSheet(2);
//$hojas = 2;
//if(!empty($cobrosByCaja)){
//	$oPHPExcel->createSheet(3);
//	$hojas++;
//}

$oPHPExcel->getActiveSheet()->setCellValue('A1','LIQUIDACION:');
$oPHPExcel->getActiveSheet()->setCellValue('B1',$util->globalDato($liquidacion['Liquidacion']['codigo_organismo']) ." - ". $util->periodo($liquidacion['Liquidacion']['periodo']));

$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setSize(16);
$oPHPExcel->getActiveSheet()->getStyle('B1')->getFont()->setBold(true);
	

$oPHPExcel->getActiveSheet()->setCellValue('A3','PROVEEDOR:');
$oPHPExcel->getActiveSheet()->setCellValue('B3',$proveedor['Proveedor']['razon_social']);
$oPHPExcel->getActiveSheet()->setCellValue('A4','CONCEPTO:');
$oPHPExcel->getActiveSheet()->setCellValue('B4',"REVERSOS");

$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setSize(14);
$oPHPExcel->getActiveSheet()->getStyle('B3')->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setSize(10);
$oPHPExcel->getActiveSheet()->getStyle('B4')->getFont()->setBold(true);

$offSet = 8;

//$oPHPExcel->setActiveSheetIndex(2);
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
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(6,$i,utf8_encode($util->nf($reverso['OrdenDescuentoCobroCuota']['importe_reversado'])));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(7,$i,utf8_encode($util->nf($reverso['OrdenDescuentoCobroCuota']['alicuota_comision_cobranza'])));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(8,$i,utf8_encode($util->nf($reverso['OrdenDescuentoCobroCuota']['comision_cobranza'])));
	$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(9,$i,utf8_encode($util->nf($reverso['OrdenDescuentoCobroCuota']['importe_reversado'] - $reverso['OrdenDescuentoCobroCuota']['comision_cobranza'])));
	$i++;
}


endif;

header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="reporte.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>
