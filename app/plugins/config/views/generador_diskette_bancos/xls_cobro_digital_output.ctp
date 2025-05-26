<?php 
//debug($planillas_XLS);
//exit;

//debug($registros_xls);
//exit;

App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));

$n = 1;
if(!empty($registros_xls) && $registros_xls != 1){
    $zip = new ZipArchive();
    $fileZip = WWW_ROOT . "files" . DS . "reportes" . DS .  "COBRO_DIGITAL_FRACCIONADO.zip";
    if(file_exists($fileZip)){
        unlink($fileZip);
    }
}

foreach($planillas_XLS as $i => $planilla_XLS){
    
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


    if(!empty($planilla_XLS)):

            $i=0;
            foreach ($planilla_XLS['columnas'] as $field => $value) {
                    $columnName = Inflector::humanize($value);
                    $oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow($i++, $offSet, $columnName);
            }
            $oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFont()->setBold(true);
            $oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $oPHPExcel->getActiveSheet()->getStyle("A$offSet")->getFill()->getStartColor()->setRGB('969696');
            $oPHPExcel->getActiveSheet()->duplicateStyle( $oPHPExcel->getActiveSheet()->getStyle("A$offSet"), "B$offSet:".$oPHPExcel->getActiveSheet()->getHighestColumn().$offSet);
            for ($j=1; $j<$i; $j++) {
                    $oPHPExcel->getActiveSheet()->getColumnDimension(PHPExcel_Cell::stringFromColumnIndex($j))->setAutoSize(true);
            }
            $i = $offSet + 1;
            foreach ($planilla_XLS['renglones'] as $row) {

                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(0,$i)->setValueExplicit($row[0]);
                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(1,$i)->setValueExplicit($row[1]);
                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(2,$i)->setValueExplicit($row[2]);
                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(3,$i)->setValueExplicit($row[3]);
                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(5,$i)->setValueExplicit($row[5]);
                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow(6,$i)->setValueExplicit($row[6]);

                    $oPHPExcel->getActiveSheet()->getStyle(sprintf('E%d',$i))->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                    $oPHPExcel->getActiveSheet()->setCellValueByColumnAndRow(4,$i, $row[4]);
                    $i++;

            }	

    endif;

    $fileName = str_replace(" ","_",$banco);
    if(!empty($registros_xls) && $registros_xls != 1){
        $fileName = str_replace(".","",$fileName) . "_" . ($n) .".xls";
    }else{
        $fileName = str_replace(".","",$fileName) .".xls";
    }
    
    
    $n++;
    
    $file = WWW_ROOT . "files" . DS . "reportes" . DS . $fileName;
    $oPHPExcel->setActiveSheetIndex(0);
    $objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
    
    
    if(!empty($registros_xls) && $registros_xls != 1){
        if(file_exists($file)){
            unlink($file);
        }         
        $objWriter->save($file);
        if ($zip->open($fileZip, ZipArchive::CREATE)==TRUE) {
            $zip->addFile($file,$fileName);
        }
//        if(file_exists($file)){
//            unlink($file);
//        }        
        
        
    }else{
        
        $oPHPExcel->setActiveSheetIndex(0);
        header("Content-type: application/vnd.ms-excel"); 
        header('Content-Disposition: attachment;filename="'.$fileName.'"');
        header('Cache-Control: max-age=0');
        $objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
        $objWriter->setTempDir(TMP);
        $objWriter->save('php://output');        
        
    }
    
    


    

    
//    debug($file);

//    $oPHPExcel->setActiveSheetIndex(0);
//    header("Content-type: application/vnd.ms-excel"); 
//    header('Content-Disposition: attachment;filename="'.$fileName.'"');
//    header('Cache-Control: max-age=0');
//    $objWriter = new PHPExcel_Writer_Excel5($oPHPExcel);
//    $objWriter->setTempDir(TMP);
//    $objWriter->save('php://output');
    
    
    
}


if(!empty($registros_xls) && $registros_xls != 1){
    $zip->close();
    header("Content-type: application/zip"); 
    header("Content-Disposition: attachment; filename=COBRO_DIGITAL_FRACCIONADO.zip"); 
    header("Content-length: " . filesize($fileZip));
    header("Pragma: no-cache"); 
    header("Expires: 0"); 
    readfile("$fileZip");
    
}


?>