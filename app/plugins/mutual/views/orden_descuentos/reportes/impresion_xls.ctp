<?php 

// debug($orden);
// debug($cuotas);
// exit;


App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("ORDEN_DTO_".$orden['OrdenDescuento']['id']);

$oPHPExcel->getActiveSheet()->setCellValue("A1","ACTUALIZADO AL:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",date('d-m-Y H:i:s'));

$oPHPExcel->getActiveSheet()->setCellValue("A2","SOCIO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$orden['OrdenDescuento']['beneficiario']);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","ORDEN DTO:");
$oPHPExcel->getActiveSheet()->setCellValue("B3",$orden['OrdenDescuento']['id']);
$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A4","FECHA EMISION:");
$oPHPExcel->getActiveSheet()->setCellValue("B4",$util->armaFecha($orden['OrdenDescuento']['fecha']));
$oPHPExcel->getActiveSheet()->getStyle("B4")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A5","REFERENCIA:");
$oPHPExcel->getActiveSheet()->setCellValue("B5",$orden['OrdenDescuento']['tipo_nro'] . " " . ($orden['OrdenDescuento']['permanente'] == 1 ? "PERMANENTE" : ($orden['OrdenDescuento']['reprogramada'] == 1 ? " - REPROGRAMADA":"")));
$oPHPExcel->getActiveSheet()->getStyle("B5")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A6","PRODUCTO:");
$oPHPExcel->getActiveSheet()->setCellValue("B6",$orden['OrdenDescuento']['proveedor_producto']);
$oPHPExcel->getActiveSheet()->getStyle("B6")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A7","BENEFICIO:");
$oPHPExcel->getActiveSheet()->setCellValue("B7",$orden['OrdenDescuento']['beneficio_str']);
$oPHPExcel->getActiveSheet()->getStyle("B7")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A8","INICIA EN:");
$oPHPExcel->getActiveSheet()->setCellValue("B8",$util->periodo($orden['OrdenDescuento']['periodo_ini']));
$oPHPExcel->getActiveSheet()->getStyle("B8")->getFont()->setBold(true);

//IMPRIMO LAS COLUMNAS
$oPHPExcel->getActiveSheet()->setCellValue("A10","TIPO");
$oPHPExcel->getActiveSheet()->setCellValue("B10","NRO_CRED_PROV");
$oPHPExcel->getActiveSheet()->setCellValue("C10","NRO");
$oPHPExcel->getActiveSheet()->setCellValue("D10","PERIODO");
$oPHPExcel->getActiveSheet()->setCellValue("E10","VENCIMIENTO");
$oPHPExcel->getActiveSheet()->setCellValue("F10","PROVEEDOR");
$oPHPExcel->getActiveSheet()->setCellValue("G10","CONCEPTO");
$oPHPExcel->getActiveSheet()->setCellValue("H10","ESTADO");
$oPHPExcel->getActiveSheet()->setCellValue("I10","SITUACION");
$oPHPExcel->getActiveSheet()->setCellValue("J10","IMPORTE");
$oPHPExcel->getActiveSheet()->setCellValue("K10","PAGADO");
$oPHPExcel->getActiveSheet()->setCellValue("L10","SALDO");

$oPHPExcel->getActiveSheet()->getStyle("A10")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A10")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A10")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A10"), "B10:L10");

if(!empty($cuotas)):

	$i = 11;
	
	foreach($cuotas as $cuota):
	
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit("CUOTA");
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['nro_referencia_proveedor']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['cuota']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($util->periodo($cuota['OrdenDescuentoCuota']['periodo']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($util->armaFecha($cuota['OrdenDescuentoCuota']['vencimiento']));
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['proveedor']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['tipo_cuota_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['estado_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['situacion_desc']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValue(number_format($cuota['OrdenDescuentoCuota']['importe'],2));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValue(number_format($cuota['OrdenDescuentoCuota']['pagado'],2));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValue(number_format($cuota['OrdenDescuentoCuota']['saldo_cuota'],2));
		$i++;
		if(!empty($cuota['OrdenDescuentoCuota']['cobros'])):
		
			foreach($cuota['OrdenDescuentoCuota']['cobros'] as $cobro):
			
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit("COBRO");
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['nro_referencia_proveedor']);
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($cuota['OrdenDescuentoCuota']['cuota']);
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($util->periodo($cobro['OrdenDescuentoCobroCuota']['periodo_cobro']));
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($util->armaFecha($cobro['OrdenDescuentoCobroCuota']['fecha_cobro']));
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($cobro['OrdenDescuentoCobroCuota']['tipo_cobro_desc']);
				$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValue(number_format($cobro['OrdenDescuentoCobroCuota']['importe'],2));
				$i++;
			
			endforeach;
		
		endif;
		
	
	
	endforeach;

endif;




$fileName = "orden_dto_".$orden['OrdenDescuento']['id'].".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>