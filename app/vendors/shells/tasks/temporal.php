<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-task
 */

App::import('Model','Shells.AsincronoError');
App::import('Vendor','PHPExcel',array('file' => 'PHPExcel/Classes/PHPExcel.php'));
App::import('Vendor','PHPExcelWriter',array('file' => 'PHPExcel/Classes/PHPExcel/Writer/Excel5.php'));


class TemporalTask extends Shell{
	
	var $temporal = null;
	var $temporal_detalle = null;
	var $pid = 0;
	var $errores = array();
    var $temporalID = 0;
    var $XLS = NULL;
    var $XLS_COLUMNS = array();
	
	function execute(){
		$oERROR = new AsincronoError();
		$oERROR->deleteAll("AsincronoError.asincrono_id = " . $this->pid,true);					
		$this->__setModeloTemporal();
	}
	
	function limpiarTabla($asincrono_id){
		if(empty($this->temporal))$this->__setModeloTemporal();
		if(!$this->temporal_detalle->deleteAll("AsincronoTemporalDetalle.asincrono_id = $asincrono_id")) return false;
		return $this->temporal->deleteAll("AsincronoTemporal.asincrono_id = $asincrono_id");
	}
	
	function grabar($datos){
		if(empty($this->temporal))$this->__setModeloTemporal();
		$this->temporal->id = 0;
		if(isset($datos['AsincronoTemporalDetalle']) && !empty($datos['AsincronoTemporalDetalle'])){
            if($this->temporal->saveAll($datos)){
                $this->temporalID = $this->temporal->getLastInsertID();
                return true;
            }else{
                return false;
            }
        }else{            
            if($this->temporal->save($datos)){
                $this->temporalID = $this->temporal->getLastInsertID();
                return true;
            }else{
                return false;
            }            
        }
	}
    
    
    function leerTemporal($asincrono_id,$fields=array(),$order=array(),$clave_1 = NULL,$clave_2 = NULL,$clave_3 = NULL,$clave_4 = NULL,$clave_5 = NULL){
		App::import('Model','Shells.AsincronoTemporal');
		$oTemporal = new AsincronoTemporal();	
        return $oTemporal->leerTemporal($asincrono_id,$fields,$order,$clave_1,$clave_2,$clave_3,$clave_4,$clave_5);
    }

	function grabarTemporalDetalle($datos){
		if(empty($this->temporal_detalle))$this->__setModeloTemporal();
		$this->temporal_detalle->id = 0;
		return $this->temporal_detalle->save($datos);		
	}
	
	
	function grabarMuchos($datos){
		if(empty($this->temporal))$this->__setModeloTemporal();
		$this->temporal->id = 0;
		return $this->temporal->saveAll($datos);
	}
	
	
	function __setModeloTemporal(){
		App::import('Model','Shells.AsincronoTemporal');
		$this->temporal = new AsincronoTemporal();	
		App::import('Model','Shells.AsincronoTemporalDetalle');
		$this->temporal_detalle = new AsincronoTemporalDetalle();		
	}
	
	function setErrorMsg($mensaje1,$mensaje2=null,$mensaje3=null,$mensaje4=null){
		$oERROR = new AsincronoError();	
		$error = array();
		$error['AsincronoError']['asincrono_id'] = $this->pid;
		$error['AsincronoError']['mensaje_1'] = $mensaje1;
		$error['AsincronoError']['mensaje_2'] = $mensaje2;
		$error['AsincronoError']['mensaje_3'] = $mensaje3;
		$error['AsincronoError']['mensaje_4'] = $mensaje4;
		$oERROR->save($error);
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
	
    function saveToXLSFile($file){
        if(is_object($this->XLS) && $this->XLS instanceof PHPExcel){
//            $objWriter = new PHPExcel_Writer_Excel5($this->XLS);
            $objWriter = PHPExcel_IOFactory::createWriter($this->XLS, 'Excel2007');
            //$objWriter->setIncludeCharts(TRUE);
            $file = WWW_ROOT . "files" . DS . "reportes" . DS . $file;
            $objWriter->save($file);
        }
    }
	
    function prepareXLSSheet($Sheet = 0,$set = array()){
        if(!is_object($this->XLS) || !$this->XLS instanceof PHPExcel)$this->XLS = new PHPExcel();
        $this->XLS->setActiveSheetIndex($Sheet);
        if(!empty($set)){
            $this->XLS->getActiveSheet()->setTitle($set['sheet_title']);
            if(!empty($set['labels'])){
                foreach($set['labels'] as $cell => $label){
                    $this->XLS->getActiveSheet()->setCellValue($cell,$label);
                    $this->XLS->getActiveSheet()->getStyle($cell)->getFont()->setBold(true);
                    $this->XLS->getActiveSheet()->getStyle($cell)->getFont()->setSize(14);
                }
            }
            if(!empty($set['columns'])){
                $this->XLS_COLUMNS[$Sheet] = $set['columns'];
                $xls_offSet = $this->XLS->setActiveSheetIndex($Sheet)->getHighestRow() + 2;
                $i = 0;
                foreach ($this->XLS_COLUMNS[$Sheet] as $col => $value) {
                    $this->XLS->getActiveSheet()->setCellValueByColumnAndRow($i, $xls_offSet, Inflector::humanize($value));
                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFont()->setBold(true);
                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFill()->setFillType(PHPExcel_Style_Fill::FILL_SOLID);
                    $this->XLS->getActiveSheet()->getStyle(PHPExcel_Cell::stringFromColumnIndex($i).$xls_offSet)->getFill()->getStartColor()->setRGB('969696');
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
    
    
    function addChart($Sheet,$chart){
        if (!is_object($this->XLS) || !$this->XLS instanceof PHPExcel) {
            $this->XLS = new PHPExcel();
        }
        $this->XLS->setActiveSheetIndex($Sheet);
        $this->XLS->getActiveSheet()->addChart($chart);
    }
    
    function createBarGraph($params,$stack = FALSE){
        $dataSeriesLabels = $dataSeriesValues = array();
        foreach($params['dataSeriesLabels'] as $value){
            $label = new PHPExcel_Chart_DataSeriesValues($value[0],$value[1]);
            array_push($dataSeriesLabels, $label);
            
        }
        foreach($params['dataSeriesValues'] as $value){
            $value = new PHPExcel_Chart_DataSeriesValues($value[0],$value[1]);
            array_push($dataSeriesValues, $value);
        }
        $xAxisTickValues = array(
                new PHPExcel_Chart_DataSeriesValues($params['xAxisTickValues'][0], $params['xAxisTickValues'][1]),	//	Q1 to Q4
        );    
        
        $plotGrouping = ($stack ? PHPExcel_Chart_DataSeries::GROUPING_STACKED : PHPExcel_Chart_DataSeries::GROUPING_STANDARD);
        
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_BARCHART,		// plotType
                $plotGrouping,	// plotGrouping
                range(0, count($dataSeriesValues)-1),			// plotOrder
                $dataSeriesLabels,								// plotLabel
                $xAxisTickValues,								// plotCategory
                $dataSeriesValues								// plotValues
        );        
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
        $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
        
        $showValues = (isset($params['showVal']) && $params['showVal'] ? TRUE : FALSE);
        $showPer = (isset($params['showPer']) && $params['showPer'] ? TRUE : FALSE);
        $layout1->setShowVal($showValues);         
        $layout1->setShowPercent($showPer);
        
       $plotArea = new PHPExcel_Chart_PlotArea($layout1, array($series));
        //	Set the chart legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
        $title = new PHPExcel_Chart_Title($params['title']);
        $xAxisLabel = new PHPExcel_Chart_Title($params['xAxisLabel']);
        $yAxisLabel = new PHPExcel_Chart_Title($params['yAxisLabel']);
        //	Create the chart
        $chart = new PHPExcel_Chart(
                $params['title'],		// name
                $title,			// title
                $legend,		// legend
                $plotArea,		// plotArea
                true,			// plotVisibleOnly
                '',				// displayBlanksAs
                $xAxisLabel,			// xAxisLabel
                $yAxisLabel		// yAxisLabel
        );
        //	Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition($params['size'][0]);
        $chart->setBottomRightPosition($params['size'][1]);        
        
        $this->addChart($params['sheet'],$chart);        
        
    }
    
    
    function createLineGraph($params,$stack = FALSE){
        $dataSeriesLabels = $dataSeriesValues = array();
        foreach($params['dataSeriesLabels'] as $value){
            $label = new PHPExcel_Chart_DataSeriesValues($value[0],$value[1]);
            array_push($dataSeriesLabels, $label);
            
        }
        foreach($params['dataSeriesValues'] as $value){
            $value = new PHPExcel_Chart_DataSeriesValues($value[0],$value[1]);
            array_push($dataSeriesValues, $value);
        }
        $xAxisTickValues = array(
                new PHPExcel_Chart_DataSeriesValues($params['xAxisTickValues'][0], $params['xAxisTickValues'][1]),	//	Q1 to Q4
        );    
        
        $plotGrouping = ($stack ? PHPExcel_Chart_DataSeries::GROUPING_STACKED : PHPExcel_Chart_DataSeries::GROUPING_STANDARD);
        
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_LINECHART,		// plotType
                $plotGrouping,	// plotGrouping
                range(0, count($dataSeriesValues)-1),			// plotOrder
                $dataSeriesLabels,								// plotLabel
                $xAxisTickValues,								// plotCategory
                $dataSeriesValues								// plotValues
        );        
        $series->setPlotDirection(PHPExcel_Chart_DataSeries::DIRECTION_COL);
        $layout1 = new PHPExcel_Chart_Layout();    // Create object of chart layout to set data label 
        $showValues = (isset($params['showVal']) && $params['showVal'] ? TRUE : FALSE);
        $showPer = (isset($params['showPer']) && $params['showPer'] ? TRUE : FALSE);
        $layout1->setShowVal($showValues);         
        $layout1->setShowPercent($showPer);     
        
       $plotArea = new PHPExcel_Chart_PlotArea($layout1, array($series));
        //	Set the chart legend
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
        $title = new PHPExcel_Chart_Title($params['title']);
        $xAxisLabel = new PHPExcel_Chart_Title($params['xAxisLabel']);
        $yAxisLabel = new PHPExcel_Chart_Title($params['yAxisLabel']);
        //	Create the chart
        $chart = new PHPExcel_Chart(
                $params['title'],		// name
                $title,			// title
                $legend,		// legend
                $plotArea,		// plotArea
                true,			// plotVisibleOnly
                '',				// displayBlanksAs
                $xAxisLabel,			// xAxisLabel
                $yAxisLabel		// yAxisLabel
        );
        //	Set the position where the chart should appear in the worksheet
        $chart->setTopLeftPosition($params['size'][0]);
        $chart->setBottomRightPosition($params['size'][1]);        
        
        $this->addChart($params['sheet'],$chart);        
        
    }    
    
    function createPieGraph($params){
        $dataSeriesLabels = array(
                new PHPExcel_Chart_DataSeriesValues($params['dataSeriesLabels'][0],$params['dataSeriesLabels'][1]),
        );
        $xAxisTickValues = array(
            new PHPExcel_Chart_DataSeriesValues($params['xAxisTickValues'][0],$params['xAxisTickValues'][1]),
        );        
        
        $dataSeriesValues = array(
                new PHPExcel_Chart_DataSeriesValues($params['dataSeriesValues'][0],$params['dataSeriesValues'][1]),
        );    
        $series = new PHPExcel_Chart_DataSeries(
                PHPExcel_Chart_DataSeries::TYPE_PIECHART,				// plotType
                PHPExcel_Chart_DataSeries::GROUPING_PERCENT_STACKED,	// plotGrouping
                range(0, count($dataSeriesValues)-1),					// plotOrder
                $dataSeriesLabels,										// plotLabel
                $xAxisTickValues,										// plotCategory
                $dataSeriesValues										// plotValues
        );
        $layout1 = new PHPExcel_Chart_Layout();
//        $layout1->setShowVal(TRUE);
        $layout1->setShowPercent(TRUE);  
        $layout1->setShowBubbleSize(TRUE);
        $plotArea = new PHPExcel_Chart_PlotArea($layout1, array($series));
        $legend = new PHPExcel_Chart_Legend(PHPExcel_Chart_Legend::POSITION_BOTTOM, NULL, false);
        $title = new PHPExcel_Chart_Title($params['title']);
        $chart = new PHPExcel_Chart(
                $params['title'],		// name
                $title,			// title
                $legend,		// legend
                $plotArea,		// plotArea
                true,			// plotVisibleOnly
                0,				// displayBlanksAs
                NULL,			// xAxisLabel
                NULL		// yAxisLabel
        );            
        $chart->setTopLeftPosition($params['size'][0]);
        $chart->setBottomRightPosition($params['size'][1]);
        $this->addChart($params['sheet'],$chart);        
    }
    
    
//    function getColumnIndexFromString($string){
//        return PHPExcel_Cell::columnIndexFromString($string);
//    }
    
}
?>