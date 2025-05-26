<?php

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("MORA TEMPRANA");

$oPHPExcel->getActiveSheet()->setCellValue("A1","LIQUIDACION:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$liquidacion['Liquidacion']['organismo'] . "|" . $liquidacion['Liquidacion']['periodo_desc']);
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("B3","NOMBRE");

$oPHPExcel->getActiveSheet()->setCellValue("C3","TEL. FIJO");
$oPHPExcel->getActiveSheet()->setCellValue("D3","TEL. MOVIL");
$oPHPExcel->getActiveSheet()->setCellValue("E3","TEL. MENS");


$oPHPExcel->getActiveSheet()->setCellValue("F3","EMPRESA/REPARTICION");
$oPHPExcel->getActiveSheet()->setCellValue("G3","ORDEN DTO");
$oPHPExcel->getActiveSheet()->setCellValue("H3","TIPO NRO");
$oPHPExcel->getActiveSheet()->setCellValue("I3","PROVEEDOR");
$oPHPExcel->getActiveSheet()->setCellValue("J3","PRODUCTO");
$oPHPExcel->getActiveSheet()->setCellValue("K3","CONCEPTO");
$oPHPExcel->getActiveSheet()->setCellValue("L3","SALDO");
$oPHPExcel->getActiveSheet()->setCellValue("M3","FECHA_DEBITO");

$oPHPExcel->getActiveSheet()->getStyle("A3")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A3")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A3"), "B3:M3");

if(!empty($mora_temprana)){
    
    $i = 4;
    
    foreach ($mora_temprana as $orden){
        
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($orden['p']['documento']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($orden['p']['apellido']." ".$orden['p']['nombre']);
        
        
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($orden['p']['telefono_fijo']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($orden['p']['telefono_movil']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($orden['p']['telefono_referencia']);
        
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($orden['e']['concepto_1']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($orden['o']['id']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($orden['o']['tipo_orden_dto']." #".$orden['o']['numero']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($orden['pr']['razon_social_resumida']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($orden['tp']['concepto_1']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit($orden['tc']['concepto_1']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValue($orden[0]['saldo_cuota']);
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValue($util->armaFecha($orden['lsr']['fecha_debito']));
        $i++;
        
    }
}


$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="mora_cuota_uno_'.$liquidacion['Liquidacion']['periodo'].'.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>
