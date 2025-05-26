<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package proveedores
 * @subpackage controller
 */
class ProveedoresAppController extends AppController{
	
	function get_proveedor($id=null, $conSaldo=0){
		
		if(empty($id)) return null;
    	App::import('Model','Proveedores.Proveedor');
    	$oPROVEEDOR = new Proveedor();		
	    $proveedor = $oPROVEEDOR->getProveedor($id,$conSaldo);
		return $proveedor;
	}
	
	
	function getGrillaXLS_to_array($tmpUpload){
	
		// 		if($tmpUpload['type'] != 'application/vnd.ms-excel' || $tmpUpload['error'] != 0) return null;
		if($tmpUpload['error'] != 0) return null;
	
		App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
		App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
		App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));
	
		$oPHPExcel = new PHPExcel();
		$oXLS = PHPExcel_IOFactory::load($tmpUpload['tmp_name']);
	
		$objWorksheet = $oXLS->getActiveSheet();
		$highestRow = $objWorksheet->getHighestRow(); // e.g. 10
		$highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
		$highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);
	
		$columnas = array();
		for ($col = 0; $col < $highestColumnIndex; ++$col):
            $label = strtoupper($objWorksheet->getCellByColumnAndRow($col, 1)->getValue());
            if(!empty($label)) $columnas[$col] = $label;
		endfor;
	
		$values = array();
        $values['error'] = array();
		$values['columnas'] = $columnas;
		$values['detalle'] = array();
		for ($row = 2; $row <= $highestRow; ++$row):
            $value = array();
            $value['EN_MANO'] = 0;
            $value['SOLICITADO'] = 0;
            foreach($columnas as $index => $label){
                $importe = $objWorksheet->getCellByColumnAndRow($index, $row)->getValue();
                if(!empty($label)){
                    if(is_numeric($importe)){
                        $value[strtoupper($label)] = $importe;
                    }else{
                        $columna = $index + 1;
                        array_push($values['error'], "*** ERROR *** FILA [$row] COLUMNA [$columna] [ $importe ] (VALOR NO NUMERICO)");
                    }
                }
            }
            if(count($value) > 2){
                array_push($values['detalle'], $value);
            }
		endfor;
		return $values;
	
	}	
	
	
}
?>