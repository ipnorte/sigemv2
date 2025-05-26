<?php 

//debug($datos);
//exit;

//Configure::write('debug',0);

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->setActiveSheetIndex(0);
$oPHPExcel->getActiveSheet()->setTitle('CONTROL VENTAS PRODUCTORES');

$oPHPExcel->getActiveSheet()->setCellValue('A1','CONTROL VENTAS PRODUCTORES');
$oPHPExcel->getActiveSheet()->setCellValue('A2','PERIODO DESDE');
$oPHPExcel->getActiveSheet()->setCellValue('B2',$util->periodo($periodoDesde));

$oPHPExcel->getActiveSheet()->setCellValue('A3','PERIODO HASTA');
$oPHPExcel->getActiveSheet()->setCellValue('B3',$util->periodo($periodoHasta));

$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);


$iniCOL = 3;
$iniROW = 6;
$letras = array();


$oPHPExcel->getActiveSheet()->setCellValue("A$iniROW","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B$iniROW","PRODUCTOR");
$oPHPExcel->getActiveSheet()->setCellValue("C$iniROW","VENTA");

$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A$iniROW")->getFill()->getStartColor()->setRGB('969696');

$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$iniROW"), "B$iniROW:C$iniROW");

foreach($cols as $col):
	$xCol = $iniCOL + $col['entero_1'];
	$letras[$col['entero_1']] = PHPExcel_Cell::stringFromColumnIndex($xCol);
	
	$oPHPExcel->getActiveSheet()->getCellByColumnAndRow($xCol,$iniROW)->setValueExplicit(strtoupper(utf8_encode($col['texto_3'])));
endforeach;

$l1 = $letras[0];
$l2 = $letras[count($letras) - 1];

$oPHPExcel->getActiveSheet()->duplicateStyle($oPHPExcel->getActiveSheet()->getStyle("A$iniROW"), $l1.$iniROW.":".$l2.$iniROW);

if(!empty($datos)):

	$iniROW += 1;
	
	$actual = "";
	$rwActual = $iniROW;
	
	foreach($datos as $dato):
	
		if($actual != bin2hex(trim($dato['clave_1']))){
			
//			debug($actual . " -- " . $dato['clave_1'] . " *** " .convert_uuencode($dato['clave_1']));
			
			$actual = bin2hex(trim($dato['clave_1']));
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_1'])));
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$iniROW)->setValueExplicit(strtoupper(utf8_encode($dato['texto_2'])));
			$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$iniROW)->setValue($dato['decimal_2']);
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


$fileName = "control_ventas_productores_".$periodoControl.".xls";
$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');
exit;

?>