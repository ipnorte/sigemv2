<?php
//debug($datos);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("LISTADO CONTROL");

$oPHPExcel->getActiveSheet()->setCellValue("A1","LIQUIDACION:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$liquidacion['Liquidacion']['organismo'] . "|" . $liquidacion['Liquidacion']['periodo_desc']);
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A2","BANCO:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$diskette['banco_intercambio_nombre']);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A3","FECHA DEBITO:");
$oPHPExcel->getActiveSheet()->setCellValue("B3",$diskette['fecha_debito']);
$oPHPExcel->getActiveSheet()->getStyle("B3")->getFont()->setBold(true);

$oPHPExcel->getActiveSheet()->setCellValue("A5","ORGANISMO");
$oPHPExcel->getActiveSheet()->setCellValue("B5","DOCUMENTO");
$oPHPExcel->getActiveSheet()->setCellValue("C5","CUIT");
$oPHPExcel->getActiveSheet()->setCellValue("D5","APELLIDO");
$oPHPExcel->getActiveSheet()->setCellValue("E5","NOMBRE");
$oPHPExcel->getActiveSheet()->setCellValue("F5","SOCIO");
$oPHPExcel->getActiveSheet()->setCellValue("G5","CBU");
$oPHPExcel->getActiveSheet()->setCellValue("H5","CUENTA");
$oPHPExcel->getActiveSheet()->setCellValue("I5","SUCURSAL");
$oPHPExcel->getActiveSheet()->setCellValue("J5","IMPORTE_DEBITO");
$oPHPExcel->getActiveSheet()->setCellValue("K5","FECHA_DEBITO");

$oPHPExcel->getActiveSheet()->getStyle("A5")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A5")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A5")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A5"), "B5:K5");




if(!empty($datos)){
    
    $i = 6;
    foreach($datos['info_procesada_by_turno'] as $codigoTurno => $turno){
//        debug($turno['descripcion']);
        
        foreach($turno['registros'] as $socio){
            
            list($apellido,$nombre) = explode(",",utf8_encode($socio['LiquidacionSocioNoimputada']['apenom']));
            
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($turno['descripcion']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['documento']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['cuit_cuil']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($apellido);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($nombre);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['socio_id']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['cbu']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['sucursal']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($socio['LiquidacionSocioNoimputada']['nro_cta_bco']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValue($socio['LiquidacionSocioNoimputada']['importe_adebitar']);
            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit($diskette['fecha_debito']);
            $i++;
        }

    }
    
    
//    exit;
}

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="listado_control.xls"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');
?>
