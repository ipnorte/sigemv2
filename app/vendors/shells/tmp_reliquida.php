<?php

/**
*
* tmp_reliquida.php
* @author adrian [* 20/01/2012]
* 
* /usr/bin/php5 /home/adrian/trabajo/www/sigem/cake/console/cake.php tmp_reliquida 195 -app /home/adrian/trabajo/www/sigem/app/
* 
*/

class TmpReliquidaShell extends Shell{
	
	
	function main(){
//		
//		$this->out("***** RELIQUIDACION DE CASOS ********");
//		
//		$liquidacionId = 195;
//		$periodo = '201404';
//        
//		App::import('Model', 'Mutual.LiquidacionSocio');
//		$oLS = new LiquidacionSocio();		
//
//        $sql = "DELETE FROM liquidacion_cuotas where liquidacion_id in (23,31,33);";
//        $oLS->query($sql);
//
//        $sql = "DELETE FROM liquidacion_socios where liquidacion_id in (23,31,33);";
//        $oLS->query($sql);
//        
//        
//		$sql = "select socio_id from mutual22_sigemdb.tmp_201404_bcocba_reliquida group by socio_id";
//		$socios = $oLS->query($sql);
//		
//		foreach($socios as $socio){
//            
//            $socio_id = $socio['tmp_201404_bcocba_reliquida']['socio_id'];
//            
//            $sql = "DELETE FROM liquidacion_cuotas where liquidacion_id in (23,31,33) and socio_id = $socio_id;";
//            $this->out($sql);
//            $oLS->query($sql);
//
//            $sql = "DELETE FROM liquidacion_socios where liquidacion_id in (23,31,33) and socio_id = $socio_id;";
//            $this->out($sql);
//            $oLS->query($sql);            
//            
//			
//            $status = $oLS->reliquidar($socio_id, $periodo);
//            
//            $this->out($socio_id);
//            if($status[0] == 1){
//                debug($status);
//                break;
//            }
//            
//			
//		}
		
	}
	
	
}

?>