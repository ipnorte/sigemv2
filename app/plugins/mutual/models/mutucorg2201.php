<?php
/**
 * @deprecated
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class Mutucorg2201 extends object{
	
	var $name = 'Mutucorg2201';
	var $useTable = null;
	
	
	function getDatosExportacion($periodo,$socio_id,$liquidacion_id){

		App::import('Model', 'Mutual.LiquidacionCuota');
		$oLQ = new LiquidacionCuota();	

		$sql = "SELECT 
						LiquidacionCuota.codigo_organismo,
						LiquidacionCuota.socio_id,
						LiquidacionCuota.persona_beneficio_id,
						sum(importe) as deuda,
						'' as codigo_dto,
						'' as sub_codigo,
						1 as actual	
				FROM 
					liquidacion_cuotas as LiquidacionCuota 
				WHERE 
					LiquidacionCuota.liquidacion_id = $liquidacion_id
					AND periodo_cuota = '$periodo'	
					AND codigo_organismo = '".strtoupper($this->name)."'
					AND LiquidacionCuota.socio_id = $socio_id
				GROUP BY
					LiquidacionCuota.codigo_organismo,
					LiquidacionCuota.socio_id,
					LiquidacionCuota.persona_beneficio_id
				union
				SELECT 
					LiquidacionCuota.codigo_organismo,
					LiquidacionCuota.socio_id,
					LiquidacionCuota.persona_beneficio_id,
					sum(importe) as deuda,
					'' as codigo_dto,
					'' as sub_codigo,
					0 as actual
				FROM 
					liquidacion_cuotas as LiquidacionCuota 
				WHERE 
					LiquidacionCuota.liquidacion_id = $liquidacion_id
					AND periodo_cuota < '$periodo'	
					AND codigo_organismo = '".strtoupper($this->name)."'
					AND LiquidacionCuota.socio_id = $socio_id
				GROUP BY
					LiquidacionCuota.codigo_organismo,
					LiquidacionCuota.socio_id,
					LiquidacionCuota.persona_beneficio_id";
		
		$datos = $oLQ->query($sql);
		debug($datos);
//		foreach($datos as $dato){
//			
//		}
		
	}
	
	
	
}
?>