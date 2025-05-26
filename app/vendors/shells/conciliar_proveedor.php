<?php

/**
 * CONCILIACION DE CREDITOS CON PROVEEDOR
 * 
 * /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php conciliar_proveedor 334 -app /home/adrian/dev/www/sigem/app/
 * 
 * @author ADRIAN TORRES
 * @package shells
 * @subpackage background-execute
 *
 */

class ConciliarProveedorShell extends Shell {

	var $uses = array();
	
	var $tasks = array('Temporal');
	
	function main() {
		
		$STOP = 0;
		
		if(empty($this->args[0])){
			$this->out("ERROR: PID NO ESPECIFICADO");
			return;
		}
		
		$pid = $this->args[0];
		
		$asinc = &ClassRegistry::init(array('class' => 'Shells.Asincrono','alias' => 'Asincrono'));
		$asinc->id = $pid; 

		$proveedor_id = $asinc->getParametro('p1');
		$archivo_excel = $asinc->getParametro('p2');
		$periodo_cjp = $asinc->getParametro('p3');
		$periodo_anses = $asinc->getParametro('p4');
		$periodo_cbu = $asinc->getParametro('p5');
		
//		$this->out("ADRIAN $proveedor_id | $archivo_excel | $periodo_cjp | $periodo_anses | $periodo_cbu");
		
		$asinc->actualizar(0,1,"ESPERE, INICIANDO PROCESO...");
		$STOP = 0;
		$total = 0;
		$i = 0;
		$asinc->actualizar(0,2,"ESPERE, ANALIZANDO ARCHIVO EXCEL...");

		App::import('Vendor','PHPExcel',array('file' => 'excel/PHPExcel.php'));
		App::import('Vendor','PHPExcelWriter',array('file' => 'excel/PHPExcel/Writer/Excel5.php'));
		App::import('Vendor','PHPExcel_IOFactory',array('file' => 'excel/PHPExcel/IOFactory.php'));		
		
		$oPHPExcel = new PHPExcel();
		$oXLS = PHPExcel_IOFactory::load(TMP . $archivo_excel);

		$objWorksheet = $oXLS->getActiveSheet();
        $highestRow = $objWorksheet->getHighestRow(); // e.g. 10
        $highestColumn = $objWorksheet->getHighestColumn(); // e.g 'F'
        $highestColumnIndex = PHPExcel_Cell::columnIndexFromString($highestColumn);	

		for ($row = 2; $row <= $highestRow; ++$row):        
		
			$fila = array();
			$fila['nro_credito_proveedor'] = $objWorksheet->getCellByColumnAndRow(0, $row)->getValue();
			$fila['cliente'] = $objWorksheet->getCellByColumnAndRow(1, $row)->getValue();
			$fila['documento'] = $objWorksheet->getCellByColumnAndRow(2, $row)->getValue();
			$fila['importe_cuota'] = $objWorksheet->getCellByColumnAndRow(3, $row)->getValue();
			debug($fila);
//			$this->out($fila['nro_credito_proveedor']);
		endfor;
		
		
		
		
		
	}
	

}
?>