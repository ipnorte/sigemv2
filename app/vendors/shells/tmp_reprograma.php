<?php

/**
* REPROGRAMACION MASIVA DE ORDENES DE DESCUENTOS
*
* tmp_reprograma.php
* @author adrian [* 20/01/2012]
* 
* /usr/bin/php5 /home/adrian/dev/www/sigem/cake/console/cake.php tmp_reprograma 195 -app /home/adrian/dev/www/sigem/app/
* 
*/

class TmpReprogramaShell extends Shell{
	
	
	function main(){
		
		$this->out("***** REPROGRAMACION DE ORDENES DE DESCUENTO ********");
		
		$periodoIni = '201201';
		$nvoPeriodoIni = '201202';
		$organismo = 'MUTUCORG7701';
		
		$sql = "SELECT OrdenDescuento.id,OrdenDescuento.socio_id,OrdenDescuento.fecha FROM orden_descuentos as OrdenDescuento, persona_beneficios b 
				WHERE OrdenDescuento.periodo_ini = '$periodoIni' AND OrdenDescuento.activo = 1
				AND OrdenDescuento.persona_beneficio_id = b.id AND b.codigo_beneficio = '$organismo' and OrdenDescuento.reprogramada = 0;";
		
		App::import('Model', 'Mutual.OrdenDescuento');
		$oORDEN = new OrdenDescuento();	

		
		App::import('Model', 'Mutual.LiquidacionSocio');
		$oLS = new LiquidacionSocio();		
		
		$ordenes = $oORDEN->query($sql);
		
		foreach($ordenes as $orden){
			
			
			$id = $orden['OrdenDescuento']['id'];
			$fechaOrden = $orden['OrdenDescuento']['fecha'];
			$socioId = $orden['OrdenDescuento']['socio_id'];
//			$ret[1] = "";
//			$ret = $oLS->reliquidar($socioId,$periodoIni,true,false,$organismo);
//			$this->out($id . "\t" . $fechaOrden . "\t" . $socioId . "\t\t" . $ret[1]);	
			
			
			if($oORDEN->is_date($fechaOrden)){
				
//				$this->out($id . "\t" . $fechaOrden);
				
				$orden = $oORDEN->reprogramarOrdenByPeriodoInicio($id,$nvoPeriodoIni);
				
//				debug($orden);
				
				if($oORDEN->reprogramarOrden($orden)){
					#RELIQUIDO EL SOCIO
					$ret = $oLS->reliquidar($socioId,$periodoIni,true,false,$organismo);
					$this->out($id . "\t" . $fechaOrden . "\t" . $socioId . "\t\t" . $ret[1]);	
				}
				
				
			}
			
			
			
		}
		
	}
	
	
}

?>