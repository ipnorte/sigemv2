<?php

// debug($turnos);
// exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();

$oPHPExcel->getActiveSheet()->setTitle("DETALLE DE TURNOS");


$oPHPExcel->getActiveSheet()->setCellValue("A1","DETALLE DE TURNOS");
$oPHPExcel->getActiveSheet()->getStyle("A1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->setCellValue("A2","LIQUIDACION");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$liquidacion['Liquidacion']['periodo_desc'] . " - " . $liquidacion['Liquidacion']['organismo']);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);



$oPHPExcel->getActiveSheet()->setCellValue("A3","TURNO");
$oPHPExcel->getActiveSheet()->setCellValue("B3","EMPRESA");
$oPHPExcel->getActiveSheet()->setCellValue("C3","REGISTROS");
$oPHPExcel->getActiveSheet()->setCellValue("D3","LIQUIDADO");
$oPHPExcel->getActiveSheet()->setCellValue("E3","DISKETTE");
$oPHPExcel->getActiveSheet()->setCellValue("F3","IMPO_DEBITO");

$oPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->getStartColor()->setRGB('969696');


$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A3"), "B3:F3");

if(!empty($turnos)):
    $i = 4;
    foreach($turnos as $turno):
        $aTrunos = explode("-",$turno[0]['turno_descripcion']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit(substr(trim($turno[0]['turno']),-5,5));
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($aTrunos[0].(isset($aTrunos[1]) ? " - " . $aTrunos[1] : ""));
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValue($turno[0]['cantidad']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValue($turno[0]['importe_adebitar']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValue($turno[0]['diskette']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($turno[0]['importe_seleccionado']);
        $i++;
    endforeach;
    
endif;


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="detalle_turnos.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');


?>