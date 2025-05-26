<?php

if(!isset($diskette['content_type'])){

    Configure::write('debug',0);
    header("Content-type: text/plain");
    header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'"');
    header('Cache-Control: max-age=0');
    echo $diskette['lote'];

}else if($diskette['content_type'] == 'application/vnd.ms-excel'){

   
    $eol = (isset($diskette['eol']) ? $diskette['eol'] : '\r\n');
    $formatoZenrise = (isset($diskette['formato']) && $diskette['formato'] == 'ZENRISE' ? TRUE : FALSE);

    $lines = preg_split("/$eol/",$diskette['lote']);

    App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
    //App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel2007.php'));
    App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Style/NumberFormat.php'));
    $oPHPExcel = new PHPExcel();

    if(!empty($lines)){
        $i = 1;
        $separator = (isset($diskette['field_separator']) ? $diskette['field_separator'] : ',');
        foreach($lines as $line){
            $fields = preg_split("/$separator/",$line);
            if(!empty($fields)){
                $j=0;


                foreach($fields as $col => $value){

                  if ($i == 1) {

                    $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$i)->getFont()->setBold(true);
                    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$i)->getFill()->getStartColor()->setRGB('969696');


                  }else{

                    switch ($col) {
                        
                        case 2: 
                            if ($formatoZenrise) {
                                $value = (empty($value) ? '2000-01-01' : $value);
                                $dateValue = date('Y-m-d', strtotime($value));
                                $date = new DateTime($dateValue);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(PHPExcel_Shared_Date::PHPToExcel($date));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                            } else {
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                            }
                            
                            break;
                            
                        case 4:
                            
                            if ($formatoZenrise) {
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                            } else {
                                $value = floatval($value);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            break;
                            

                        case 6:
                          
                            if ($formatoZenrise) {
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                            } else {
                                $value = floatval($value);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            
                            break;

                        case 8:
                          
                            if ($formatoZenrise) {
                                // es un string
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                            } else {
                                $value = floatval($value);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            break;                           
                        
                        case 14:
                            
                            if ($formatoZenrise) {
                                $value = floatval($value);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++, $i)->setValue(utf8_encode($value));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);                                
                            } else {
                                $value = (empty($value) ? '2000-01-01' : $value);
                                $dateValue = date('Y-m-d', strtotime($value));
                                $date = new DateTime($dateValue);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(PHPExcel_Shared_Date::PHPToExcel($date));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                            }
                            break;
                            
                        case 15:
                          
                            if ($formatoZenrise) {
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                            } else {
                                $value = floatval($value);
                                $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
                            }
                            break;                             
                            
                        case 16:
                          $value = intval($value);
                          $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));

                          break;
                        case 17:
                          $value = intval($value);
                          $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(utf8_encode($value));

                          break;
                        case 18:

                          $date = new DateTime($value);
                          $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(PHPExcel_Shared_Date::PHPToExcel($date));
                          $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
                          $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                          $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).$i)->getFill()->getStartColor()->setRGB('96fdaf');


                          break;
                        case 20:

                          $date = new DateTime($value);

                          $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValue(PHPExcel_Shared_Date::PHPToExcel($date));
                          $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col).'2:'.PHPExcel_Cell::stringFromColumnIndex($col).$i)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);

                          break;
                        
                        case 21:
                            $value = intval($value);
                            $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                            if(empty($value)){
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$i.':'.PHPExcel_Cell::stringFromColumnIndex(25).$i)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                                $oPHPExcel->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex(0).$i.':'.PHPExcel_Cell::stringFromColumnIndex(25).$i)->getFill()->getStartColor()->setRGB('fc764b');        
                            }
                            break;                          

                      default:
                        $oPHPExcel->getActiveSheet()->getCellByColumnAndRow($j++,$i)->setValueExplicit(utf8_encode($value));
                        break;
                    }
                  }

                }
            }
            $i++;
        }
    }

    $extension = (isset($diskette['file_extension']) ? $diskette['file_extension'] : 'xls');

    $oPHPExcel->setActiveSheetIndex(0);

    $objWriter = new PHPExcel_Writer_Excel2007($oPHPExcel);
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="'.$diskette['archivo'].'.'.$extension.'"');
    header('Cache-Control: max-age=0');
    $objWriter->save('php://output');

}


?>
