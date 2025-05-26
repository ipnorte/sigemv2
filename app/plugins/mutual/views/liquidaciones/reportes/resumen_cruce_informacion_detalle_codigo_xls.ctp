<?php 
//DEBUG($socios);
//EXIT;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle($status);

$oPHPExcel->getActiveSheet()->setCellValue("A1","BANCO:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$banco_nombre);
$oPHPExcel->getActiveSheet()->setCellValue("A2","BANCO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2","$status ($status_descripcion)");
$oPHPExcel->getActiveSheet()->setCellValue("A3","LIQUIDACION:");
$oPHPExcel->getActiveSheet()->setCellValue("B3","#".$liquidacion['Liquidacion']['id']."-".$util->periodo($liquidacion['Liquidacion']['periodo'],true) . ' | ' . $util->globalDato($liquidacion['Liquidacion']['codigo_organismo']));


$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A4","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B4","NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","IDENTIFICACION");
$oPHPExcel->getActiveSheet()->setCellValue("D4","FECHA DEBITO");
$oPHPExcel->getActiveSheet()->setCellValue("E4","IMPORTE");

$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:E4");


$ACU_CANTIDAD = $ACU_DEBITADO = 0;

$i = 5;

foreach($socios as $socio){
    
    $ACU_CANTIDAD++;
    $ACU_DEBITADO 	+= $socio['LiquidacionSocioRendicion']['importe_debitado'];
    if(empty($socio['Persona']['tipo_documento']))$tdoc = "DNI";
    else $tdoc = $util->globalDato($socio['Persona']['tipo_documento']);
    if(empty($socio['Persona']['documento'])) $ndoc =  $socio['LiquidacionSocioRendicion']['documento'];
    else $ndoc = $socio['Persona']['documento'];

    if(empty($socio['Persona']['apellido'])) $apenom = "*** NO EXISTE EN PADRON ***";
    else $apenom = $socio['Persona']['apellido'].", ".$socio['Persona']['nombre'];
    
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($tdoc.' '.$ndoc);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit(utf8_encode($apenom));
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($socio['LiquidacionSocioRendicion']['identificacion']);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValue($socio['LiquidacionSocioRendicion']['fecha_debito']);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValue($socio['LiquidacionSocioRendicion']['importe_debitado']);
    
    $i++;
}





$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="LIQ_'.$liquidacion['Liquidacion']['id']."_".$liquidacion['Liquidacion']['periodo'].'_'.$status.'.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>

