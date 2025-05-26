<?php 

// debug($datos);
// exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$oPHPExcel = new PHPExcel();
$oPHPExcel->getActiveSheet()->setTitle("FACTURACION ELECTRONICA");


$oPHPExcel->getActiveSheet()->setCellValue("A1","DESDE:");
$oPHPExcel->getActiveSheet()->setCellValue("B1",$fecha_desde);
$oPHPExcel->getActiveSheet()->getStyle('B1')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
$oPHPExcel->getActiveSheet()->getStyle("B1")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->setCellValue("A2","HASTA:");
$oPHPExcel->getActiveSheet()->setCellValue("B2",$fecha_hasta);
$oPHPExcel->getActiveSheet()->getStyle('B2')->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
$oPHPExcel->getActiveSheet()->getStyle("B2")->getFont()->setBold(true);


$oPHPExcel->getActiveSheet()->setCellValue("A4","FECHA");
$oPHPExcel->getActiveSheet()->setCellValue("B4","COMPROBANTE");
$oPHPExcel->getActiveSheet()->setCellValue("C4","CONCEPTO");
$oPHPExcel->getActiveSheet()->setCellValue("D4","CUIT");
$oPHPExcel->getActiveSheet()->setCellValue("E4","NO GRAVADO");
$oPHPExcel->getActiveSheet()->setCellValue("F4","GRAVADO");
$oPHPExcel->getActiveSheet()->setCellValue("G4","IVA");
$oPHPExcel->getActiveSheet()->setCellValue("H4","TOTAL");
$oPHPExcel->getActiveSheet()->setCellValue("I4","TIPO");
$oPHPExcel->getActiveSheet()->setCellValue("J4","CODIGO");
$oPHPExcel->getActiveSheet()->setCellValue("K4","VENCIMIENTO");
$oPHPExcel->getActiveSheet()->setCellValue("L4","ERROR");

$oPHPExcel->getActiveSheet()->getStyle("A4")->getFont()->setBold(true);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
$oPHPExcel->getActiveSheet()->getStyle("A4")->getFill()->getStartColor()->setRGB('969696');
$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A4"), "B4:L4");


$i = 5;
foreach ($datos as $renglon) {
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit(date('d/m/Y',strtotime($renglon['Factura']['fecha_comprobante'])));
    if($renglon['Factura']['e_codigo'] > 0) {
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit("");
    }else {
        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($renglon['Factura']['comprobante']);
    }
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($renglon['Factura']['nom_apel']);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($renglon['Factura']['numero_documento']);
    
    
    
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValue($renglon['Factura']['importe_total_concepto']);
    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(4).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValue($renglon['Factura']['importe_neto']);
    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(5).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValue($renglon['Factura']['importe_iva']);
    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(6).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValue($renglon['Factura']['importe_total']);
    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(7).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValue($renglon['Factura']['tipo_emision']);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($renglon['Factura']['codigo_autorizacion']);
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValue($renglon['Factura']['cae_fecha_vto']);
    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(10).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
    
    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValue($renglon['Factura']['e_mensaje']);
    
    $i++;
    
}



$fileName = "reporte_facturacion.xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel");
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>