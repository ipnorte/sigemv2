<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class LiquidacionDisenioRegistro extends MutualAppModel{
	
	var $name = 'LiquidacionDisenioRegistro';
	
	function getCampoCodigoStatus($codigo_organismo){
		$condiciones = array(
								'LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,
								'LiquidacionDisenioRegistro.codigo_status' => 1
		);
		$campo = $this->find('all',array('conditions' => $condiciones,'group' => array('LiquidacionDisenioRegistro.columna_destino')));
		return $campo[0]['LiquidacionDisenioRegistro']['columna_destino'];
	}
	
	/**
	 * devuelve los campos marcados para consulta
	 * @param unknown_type $codigo_organismo
	 * @param unknown_type $banco_id
	 */
	function getCamposConsulta($codigo_organismo){
		$campos = array();
		$condiciones = array(
								'LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,
								'LiquidacionDisenioRegistro.campo_consulta' => 1
		);
		$registros = $this->find('all',array('conditions' => $condiciones,'group' => array('LiquidacionDisenioRegistro.columna_destino')));
		array_push($campos,'LiquidacionIntercambioRegistro.liquidacion_id');
		array_push($campos,'LiquidacionIntercambioRegistro.banco_intercambio');
		foreach($registros as $registro){
			$campo = 'LiquidacionIntercambioRegistro.'.$registro['LiquidacionDisenioRegistro']['columna_destino'];
			if($registro['LiquidacionDisenioRegistro']['sumar'] == 1){
				$campo = "SUM(".$campo.") as ".$registro['LiquidacionDisenioRegistro']['columna_destino'];
			}
			array_push($campos,$campo);
		}
		return $campos;		
	}
	
	function getCamposAgrupa($codigo_organismo){
		$campos = array();
		$condiciones = array(
								'LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,
								'LiquidacionDisenioRegistro.campo_consulta' => 1,
								'LiquidacionDisenioRegistro.agrupa' => 1
		);
		$registros = $this->find('all',array('conditions' => $condiciones,'group' => array('LiquidacionDisenioRegistro.columna_destino')));
		array_push($campos,'LiquidacionIntercambioRegistro.liquidacion_id');
		foreach($registros as $registro){
			$campo = 'LiquidacionIntercambioRegistro.'.$registro['LiquidacionDisenioRegistro']['columna_destino'];
			array_push($campos,$campo);
		}
		return $campos;		
	}	
	
	
	function getCamposVariables($codigo_organismo){
		$campos = array();
		$condiciones = array(
								'LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,
								'LiquidacionDisenioRegistro.campo_consulta' => 1,
								'not' => array('LiquidacionDisenioRegistro.modelo_campo' => null),
		);
		$registros = $this->find('all',array('conditions' => $condiciones,'group' => array('LiquidacionDisenioRegistro.columna_destino')));
		foreach($registros as $registro){
			$campos[$registro['LiquidacionDisenioRegistro']['modelo_campo']] = array('LiquidacionIntercambioRegistro',$registro['LiquidacionDisenioRegistro']['columna_destino'],$registro['LiquidacionDisenioRegistro']['tipo_dato']);
		}
		return $campos;			
	}
	
	
	/**
	 * devuelve array con campos marcados como criterio de igualacion ordenados segun orden de igualacion
	 * @param $codigo_organismo
	 */
	function getCamposIgualables($codigo_organismo){
		$campos = array();
		$condiciones = array(
								'LiquidacionDisenioRegistro.codigo_organismo' => $codigo_organismo,
								'LiquidacionDisenioRegistro.condicion_igualacion' => 1,
								'not' => array('LiquidacionDisenioRegistro.modelo_campo' => null),
		);
		$registros = $this->find('all',array('conditions' => $condiciones,'fields' => array('LiquidacionDisenioRegistro.modelo,LiquidacionDisenioRegistro.modelo_campo'),'order' => array('LiquidacionDisenioRegistro.orden_igualacion')));
		$registros = Set::extract('/LiquidacionDisenioRegistro/modelo_campo',$registros);
		return $registros;			
	}	

}
?>