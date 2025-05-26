<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of write_xls
 *
 * @author adrian
 */

App::import('Model','Shells.AsincronoError');
App::import('Vendor','PHPExcel',array('file' => 'PHPExcel/Classes/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'PHPExcel/Classes/PHPExcel/Writer/Excel5.php'));


class GeneraXLS {
    
    private $XLS = NULL;
    private $XLS_COLUMNS = array();
    private $saveToFile = NULL;
    
    function __construct($FILE = 'reporte.xls') {
        
//        $this->saveToFile = WWW_ROOT . "files" . DS . "reportes" . DS . intval(mt_rand())."_".$FILE;
        $this->saveToFile = WWW_ROOT . "files" . DS . "reportes" . DS . date('YmdHis')."_".$FILE;
    }

    function getFileNamber(){
        return $this->saveToFile;
    }
            
    function getXLSObject(){
        if(!is_object($this->XLS) || !$this->XLS instanceof PHPExcel)$this->XLS = new PHPExcel();
        return $this->XLS;
    }
    
    function setXLSObject($Sheets = 0){
        if(!is_object($this->XLS) || !$this->XLS instanceof PHPExcel)$this->XLS = new PHPExcel();
        for ($j=0; $j < $Sheets; $j++){
            $this->XLS->createSheet($j);
        }
    }    
	
    function saveToXLSFile($file = NULL){
        if(is_object($this->XLS) && $this->XLS instanceof PHPExcel){
            $objWriter = new PHPExcel_Writer_Excel5($this->XLS);
            if(empty($file))$file = $this->saveToFile;
            $objWriter->save($file);
        }
    }
    
    function borrarXLSFile(){
        if(file_exists($this->saveToFile)) unlink ($this->saveToFile);
    }
    
    function getXLSFileBuffer(){
        return (file_exists($this->saveToFile) ? file_get_contents($this->saveToFile) : NULL);
    }
	
    function prepareXLSSheet($Sheet = 0,$set = array(),$bolder=TRUE,$fontSize=14){
        if(!is_object($this->XLS) || !$this->XLS instanceof PHPExcel)$this->XLS = new PHPExcel();
        if($Sheet !== 0) $this->XLS->createSheet($Sheet);
        $this->XLS->setActiveSheetIndex($Sheet);
        if(!empty($set)){
            if(isset($set['sheet_title']))$this->XLS->getActiveSheet()->setTitle($set['sheet_title']);
            if(!empty($set['labels'])){
                foreach($set['labels'] as $cell => $label){
                    $this->XLS->getActiveSheet()->setCellValue($cell,$label);
                    if($bolder)$this->XLS->getActiveSheet()->getStyle($cell)->getFont()->setBold(true);
                    $this->XLS->getActiveSheet()->getStyle($cell)->getFont()->setSize($fontSize);
                }
            }
            if(!empty($set['columns'])){
                $this->XLS_COLUMNS[$Sheet] = $set['columns'];
                $xls_offSet = $this->XLS->setActiveSheetIndex($Sheet)->getHighestRow() + 2;
                $i = 0;
                foreach ($this->XLS_COLUMNS[$Sheet] as $col => $value) {
                    $this->XLS->getActiveSheet()->setCellValueByColumnAndRow($i, $xls_offSet, Inflector::humanize($value));
                    $celda = PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet;
                    $this->bolderColumnValue($celda);
                    $this->fillerColumnValue($celda);
//                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFont()->setBold(true);
//                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
//                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFill()->getStartColor()->setRGB('969696');
                    $i++;
                }                
            }
        }
    }
    
    function writeXLSRow($Sheet = 0,$values = array()){
        if(!is_object($this->XLS) || !$this->XLS instanceof PHPExcel)$this->XLS = new PHPExcel();
        $this->XLS->setActiveSheetIndex($Sheet);
        if(!empty($values)){
            $xls_offSet = $this->XLS->setActiveSheetIndex($Sheet)->getHighestRow() + 1;
            if(!empty($this->XLS_COLUMNS[$Sheet])){
                $i = 0;
                foreach ($this->XLS_COLUMNS[$Sheet] as $col => $value){
                    $this->XLS->getActiveSheet()->setCellValueByColumnAndRow($i++,$xls_offSet, utf8_encode($values[$col]));
                }
            }else{
                $i = 0;
                foreach ($values as $value){
                    $this->XLS->getActiveSheet()->setCellValueByColumnAndRow($i++,$xls_offSet, utf8_encode($value));
                }                
            }
        }
        
    }    
    
    function writeXLSCell($value,$col = "A",$row = 1,$sheet = 0,$encode=TRUE){
        if($encode) $value = utf8_encode ($value);
        if(!is_numeric($col)) $this->XLS->getActiveSheet()->setCellValueByColumnAndRow(PHPExcel_Cell::columnIndexFromString($col),$row,$value);
        else $this->XLS->getActiveSheet()->setCellValueByColumnAndRow($col,$row,$value);
    }
    
    function combinarCells($col,$nums){
        
    }
    
    function get_coordenadas($colindex=0,$rowindex=1){
        return PHPExcel_Cell::stringFromColumnIndex($colindex).$rowindex;
    }
    
    
    /**
     * 
     * @param type $column
     * @param type $Sheet
     */
    function bolderColumnValue($column,$Sheet = 0){
        $this->XLS->setActiveSheetIndex($Sheet);
        if(is_array($column)){
            foreach ($column as $col){
                $this->XLS->getActiveSheet()->getStyle($col)->getFont()->setBold(true);
            }
        }else{
            $this->XLS->getActiveSheet()->getStyle($column)->getFont()->setBold(true);
        }
        
    }
    
    function fillerColumnValue($column,$Sheet = 0,$rgb='969696'){
        $this->XLS->setActiveSheetIndex($Sheet);
        if(is_array($column)){
            foreach ($column as $col){
                $this->XLS->getActiveSheet()->getStyle($col)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                $this->XLS->getActiveSheet()->getStyle($col)->getFill()->getStartColor()->setRGB($rgb);
            }
        }else{
            $this->XLS->getActiveSheet()->getStyle($column)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
            $this->XLS->getActiveSheet()->getStyle($column)->getFill()->getStartColor()->setRGB($rgb);
        }
    }
    
    function verticalColumnValue($column,$Sheet = 0){
        $this->XLS->setActiveSheetIndex($Sheet);
        if(is_array($column)){
            foreach ($column as $col){
                $this->XLS->getActiveSheet()->getStyle($col)->getAlignment()->setTextRotation(90);
            }
        }else{
            $this->XLS->getActiveSheet()->getStyle($column)->getAlignment()->setTextRotation(90);
        }
        
    }
    
    
    function writeXLSCellNumberValue($value,$col,$row,$sheet = 0){
        $this->XLS->setActiveSheetIndex($sheet);
        $value = floatval($value);
        $this->XLS->getActiveSheet()->getCellByColumnAndRow($col,$row)->setValue($value);
        $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col). $row . ':' .PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_NUMBER_00);
    }

    function writeXLSCellPercentValue($value,$col,$row,$sheet = 0){
        $this->XLS->setActiveSheetIndex($sheet);
        $value = floatval($value) / 100;
        $this->XLS->getActiveSheet()->getCellByColumnAndRow($col,$row)->setValue($value);
        $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col). $row . ':' .PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_PERCENTAGE_00);
    }    

    function writeXLSCellDateValue($value,$col,$row,$sheet = 0){
        $this->XLS->setActiveSheetIndex($sheet);
        $date = new DateTime($value); 
        $this->XLS->getActiveSheet()->getCellByColumnAndRow($col,$row)->setValue(PHPExcel_Shared_Date::PHPToExcel($date));
        $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($col). $row . ':' .PHPExcel_Cell::stringFromColumnIndex($col).$row)->getNumberFormat()->setFormatCode(PHPExcel_Style_NumberFormat::FORMAT_DATE_YYYYMMDD2);
    }

}
