<?php 
//debug($cols);
//debug($datos);
//debug($datos2);
//exit;


Configure::write('debug',0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->setActiveSheetIndex(0);
$oPHPExcel->getActiveSheet()->setTitle('CONTROL VENTAS PROVEEDORES');

$oPHPExcel->getActiveSheet()->setCellValue('A1','CONTROL VENTAS PROVEEDORES');
$oPHPExcel->getActiveSheet()->setCellValue('A2','PERIODO DESDE');
$oPHPExcel->getActiveSheet()->setCellValue('B2',$util->periodo($periodoDesde));
$oPHPExcel->getActiveSheet()->setCellValue('A3','PERIODO HASTA');
$oPHPExcel->getActiveSheet()->setCellValue('B3',$util->periodo($periodoHasta));

$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);


$oPHPExcel->getActiveSheet()->setCellValue("A4","PROVEEDOR");
$oPHPExcel->getActiveSheet()->setCellValue("B4","SOLICITUD");
$oPHPExcel->getActiveSheet()->setCellValue("C4","FECHA");
$oPHPExcel->getActiveSheet()->setCellValue("D4","ESTADO");
$oPHPExcel->getActiveSheet()->setCellValue("E4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("F4","APELLIDO Y NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("G4","SOLICITADO");
$oPHPExcel->getActiveSheet()->setCellValue("H4","EN_MANO");
$oPHPExcel->getActiveSheet()->setCellValue("I4","CUOTAS");
$oPHPExcel->getActiveSheet()->setCellValue("J4","MONTO_CUOTA");
$oPHPExcel->getActiveSheet()->setCellValue("K4","PERIODO_LIQ");
$oPHPExcel->getActiveSheet()->setCellValue("L4","ORGANISMO");


$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');

$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:L4");

if(!empty($datos)):


	$actual = "";
	
	$iniROW = 5;
	
	foreach($datos as $dato):
	
//		if($actual != bin2hex(trim($dato['clave_1']))){
//			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_1'])));
//			$oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($xCol).$iniROW)->getFont()->setBold(true);
//			$oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($xCol).$iniROW)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//			$oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($xCol).$iniROW)->getFill()->getStartColor()->setRGB('8AAEC6');
//			
//			$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($xCol).$iniROW), "B$iniROW:J$iniROW");
//			
//			$actual = bin2hex(trim($dato['clave_1']));
//			$iniROW++;
//		}
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_1'])));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$iniROW)->setValueExplicit($dato['texto_4']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$iniROW)->setValueExplicit($dato['texto_5']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$iniROW)->setValueExplicit($dato['texto_6']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$iniROW)->setValueExplicit($dato['texto_2']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$iniROW)->setValueExplicit($dato['texto_3']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$iniROW)->setValue($dato['decimal_1']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$iniROW)->setValue($dato['decimal_2']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$iniROW)->setValue($dato['entero_1']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$iniROW)->setValue($dato['decimal_3']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$iniROW)->setValueExplicit($dato['texto_9']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$iniROW)->setValueExplicit($dato['texto_8']);
		
		$iniROW++;
		
	endforeach;

endif;

if(!empty($datos2)):

	//genero la hoja con el resumen
	$oPHPExcel->createSheet(1);
	$oPHPExcel->setActiveSheetIndex(1);
	$oPHPExcel->getActiveSheet()->setTitle('RESUMEN');	
	
	$oPHPExcel->getActiveSheet()->setCellValue('A1','CONTROL VENTAS PROVEEDORES');
	$oPHPExcel->getActiveSheet()->setCellValue('A2','PERIODO DESDE');
	$oPHPExcel->getActiveSheet()->setCellValue('B2',$util->periodo($periodoDesde));
	$oPHPExcel->getActiveSheet()->setCellValue('A3','PERIODO HASTA');
	$oPHPExcel->getActiveSheet()->setCellValue('B3',$util->periodo($periodoHasta));
	
	$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
	
	$letras = array();
	$iniCOL = 1;
	$iniROW = 5;
	
	$oPHPExcel->getActiveSheet()->setCellValue("A$iniROW","PROVEEDOR");

	
	$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFont()->setBold(true);
	$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFill()->getStartColor()->setRGB('969696');	
	
//	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$iniROW"), "B$iniROW:B$iniROW");	
	
	foreach($cols as $col):
		$xCol = $iniCOL + $col['entero_1'];
		$letras[$col['entero_1']] = PHPExcel_Cell::stringFromColumnIndex($xCol);
		
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow($xCol,$iniROW)->setValueExplicit(strtoupper(utf8_encode($col['texto_2'])));
	endforeach;	
	
	$l1 = $letras[0];
	$l2 = $letras[count($letras) - 1];
	
	$oPHPExcel->getActiveSheet()->duplicateStyle($oPHPExcel->getActiveSheet()->getStyle("A$iniROW"), $l1.$iniROW.":".$l2.$iniROW);	

	
	$iniROW += 1;
	
	$actual = "";
	$rwActual = $iniROW;

	
	foreach($datos2 as $dato):
	
		if($actual != bin2hex(trim($dato['clave_2']))){
			
//			debug($actual . " -- " . $dato['clave_1'] . " *** " .convert_uuencode($dato['clave_1']));
			
			$actual = bin2hex(trim($dato['clave_2']));
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_1'])));
//			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_2'])));
//			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$iniROW)->setValue($dato['decimal_2']);
			$xCol = $iniCOL + $dato['entero_1'];
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow($xCol,$iniROW)->setValue($dato['decimal_1']);
			$rwActual = $iniROW;
			$iniROW++;
		}else{
			$xCol = $iniCOL + $dato['entero_1'];
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow($xCol,$rwActual)->setValue($dato['decimal_1']);
			
		}
	
		
	endforeach;	

endif;

//exit;

$fileName = "control_ventas_proveedores_".$periodoControl.".xls";
$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');
exit;

?>