<?php 
//debug($planilla_XLS);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));


$oPHPExcel = new PHPExcel();


$oPHPExcel->getActiveSheet()->setTitle(substr($planilla_XLS['hoja'],0,20));
$offSet = 1;

if(isset($planilla_XLS['titulos']) && !empty($planilla_XLS['titulos'])):
        $offSet = 2;
	foreach($planilla_XLS['titulos'] as $coordenada => $valor):
		$oPHPExcel->getActiveSheet()->setCellValue($coordenada,$valor);
		$offSet++;
	endforeach;
endif;


$style = array(
    'alignment' => array(
        'horizontal' => PHPExcel_Style_Alignment::HORIZONTAL_CENTER,
    )
);


if(!empty($planilla_XLS)):
	
	$i=0;
	foreach ($planilla_XLS['columnas'] as $field => $value) {
		$columnName = Inflector::humanize($value);
		$oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
	}
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true)->getColor()->setRGB('FFFFFF');
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
	$oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('333399');
        
        $oPHPExcel->getActiveSheet()
                        ->getStyle("A$offSet")
                        ->getBorders()
                        ->getAllBorders()
                        ->setBorderStyle(PHPExcel_Style_Border::BORDER_THIN)
                        ->getColor()
                        ->setRGB('000000');
        $oPHPExcel->getActiveSheet()->getStyle("A$offSet")->applyFromArray($style);          
        
	$oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
	for ($j=1; $j<$i; $j++) {
		$oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
	}
	$i = $offSet + 1;
	foreach ($planilla_XLS['renglones'] as $row) {
            
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($row[0]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($row[1]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit(utf8_encode($row[2]));
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($row[3]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(4,$i)->setValueExplicit($row[4]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($row[5]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($row[6]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(7,$i)->setValueExplicit($row[7]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(8,$i)->setValueExplicit($row[8]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(9,$i)->setValueExplicit($row[9]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(10,$i)->setValueExplicit($row[10]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(11,$i)->setValueExplicit($row[11]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(12,$i)->setValueExplicit($row[12]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(13,$i)->setValueExplicit($row[13]);
                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(14,$i)->setValueExplicit($row[14]);
//                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(15,$i)->setValue($row[15]);
                

                $oPHPExcel->getActiveSheet()->getStyle(sprintf('P%d',$i))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                $oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(15,$i, $row[15]);
		$i++;
                
	}	

//	debug($planilla);

endif;

$fileName = str_replace(" ","_",$banco);
$fileName = str_replace(".","",$fileName).".xls";

$oPHPExcel->setActiveSheetIndex(0);
header("Content-type: application/vnd.ms-excel"); 
header('Content-Disposition: attachment;filename="'.$fileName.'"');
header('Cache-Control: max-age=0');
$objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
$objWriter->setTempDir(TMP);
$objWriter->save('php://output');

?>