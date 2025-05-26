<?php
// debug($liquidacion);
// debug($datos);
// exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle("SCORING INFORME");

$oPHPExcel->getActiveSheet()->setCellValue("A1","DETALLE DE SOCIOS");
$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->setCellValue("A2","LIQUIDACION");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$util->periodo($liquidacion['Liquidacion']['periodo'],true) . " - " . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B4","APELLIDO_Y_NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","#SOCIO");
$oPHPExcel->getActiveSheet()->setCellValue("D4","SALDO_ACTUAL");
$oPHPExcel->getActiveSheet()->setCellValue("E4","ADICIONALES");
$oPHPExcel->getActiveSheet()->setCellValue("F4","0 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("G4","0-3 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("H4","3-6 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("I4","6-9 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("J4","9-12 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("K4","MAS DE 12 MESES");
$oPHPExcel->getActiveSheet()->setCellValue("L4","RIESGO");
$oPHPExcel->getActiveSheet()->setCellValue("M4","SCORE");
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:M4");


if(!empty($datos)){
    
    $i = 5;
    
    foreach ($datos as $key => $dato) {
        
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($dato['Persona']['documento']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValue(utf8_encode($dato['Persona']['apellido']).', '. utf8_encode($dato['Persona']['nombre']));
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($dato['LiquidacionSocioScore']['socio_id']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValue($dato['LiquidacionSocioScore']['saldo_actual']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValue($dato['LiquidacionSocioScore']['cargos_adicionales']);
		$oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($dato['LiquidacionSocioScore']['00']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValue($dato['LiquidacionSocioScore']['03']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($dato['LiquidacionSocioScore']['06']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($dato['LiquidacionSocioScore']['09']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValue($dato['LiquidacionSocioScore']['12']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValue($dato['LiquidacionSocioScore']['13']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValue($dato['LiquidacionSocioScore']['riesgo']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValue($dato['LiquidacionSocioScore']['score']);
		$i++;        
        
    }
    
}


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="scoring.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>