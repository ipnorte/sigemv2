<?php

/**
*
* mutual_cuenta_asiento.php
* @author adrian [* 19/07/2012]
*/

class MutualCuentaAsiento extends MutualAppModel{
	
	var $name = "MutualCuentaAsiento";
	
	function getVinculo($id){
		$vinculo = $this->read(null,$id);
		return $this->armaDatos($vinculo);
	}
	
	function vincular($datos){
		if(empty($datos['MutualCuentaAsiento']['co_plan_cuenta_id']) || !isset($datos['MutualCuentaAsiento']['co_plan_cuenta_id'])) $datos['MutualCuentaAsiento']['co_plan_cuenta_id'] = 0;
		if(empty($datos['MutualCuentaAsiento']['mutual_tipo_asiento_id']) || !isset($datos['MutualCuentaAsiento']['mutual_tipo_asiento_id'])) $datos['MutualCuentaAsiento']['mutual_tipo_asiento_id'] = 0;
		return parent::save($datos);
	}
	
	function desvincular($id){
		$vinculo = $this->read(null,$id);
		$vinculo['MutualCuentaAsiento']['co_plan_cuenta_id'] = 0;
		$vinculo['MutualCuentaAsiento']['mutual_tipo_asiento_id'] = 0;
		$vinculo['MutualCuentaAsiento']['instancia'] = "COBRO";
		return $this->save($vinculo);
	}
	
	function cargarVinculos(){
		$datos = array();
		$this->actualizarDatosFromCuotas();
		$sql = "SELECT MutualCuentaAsiento.*,
				GlobalDato2.concepto_1 AS concepto_producto,GlobalDato1.concepto_1 AS concepto_cuota
				 FROM mutual_cuenta_asientos AS MutualCuentaAsiento
				LEFT JOIN global_datos AS GlobalDato1 ON (GlobalDato1.id = MutualCuentaAsiento.tipo_cuota)
				LEFT JOIN global_datos AS GlobalDato2 ON (GlobalDato2.id = MutualCuentaAsiento.tipo_producto)
				ORDER BY MutualCuentaAsiento.tipo_orden_dto,GlobalDato1.concepto_1,GlobalDato2.concepto_1";
		$vinculos = $this->query($sql);
		if(empty($vinculos)) return $datos;
		
		App::import('Model','contabilidad.PlanCuenta');
		$oCTA = new PlanCuenta();	
		App::import('Model','mutual.MutualTipoAsiento');
		$oTIPOAS = new MutualTipoAsiento();				
		
		foreach($vinculos as $idx => $vinculo){

			if(!empty($vinculo['GlobalDato2']['concepto_producto'])) $vinculo['MutualCuentaAsiento']['concepto_producto'] = $vinculo['GlobalDato2']['concepto_producto'];
			else $vinculo['MutualCuentaAsiento']['concepto_producto'] = $vinculo['MutualCuentaAsiento']['tipo_producto'];
			if(!empty($vinculo['GlobalDato1']['concepto_cuota'])) $vinculo['MutualCuentaAsiento']['concepto_cuota'] = $vinculo['GlobalDato1']['concepto_cuota'];
			else $vinculo['MutualCuentaAsiento']['concepto_cuota'] = $vinculo['MutualCuentaAsiento']['tipo_cuota'];
			$datos[$vinculo['MutualCuentaAsiento']['tipo_orden_dto']][$vinculo['MutualCuentaAsiento']['tipo_producto']]['concepto'] = $vinculo['MutualCuentaAsiento']['concepto_producto'];
			$cuenta = $oCTA->getCuenta($vinculo['MutualCuentaAsiento']['co_plan_cuenta_id']);
			$vinculo['MutualCuentaAsiento']['cuenta'] = null;
			$vinculo['MutualCuentaAsiento']['tipo_asiento'] = null;
			if($cuenta['PlanCuenta']['existe'] == 1) $vinculo['MutualCuentaAsiento']['cuenta'] = $cuenta['PlanCuenta']['codigo'] . " " . $cuenta['PlanCuenta']['descripcion'];;
			$tipoAsiento = $oTIPOAS->read(null,$vinculo['MutualCuentaAsiento']['mutual_tipo_asiento_id']);
			if(!empty($tipoAsiento)){
				$vinculo['MutualCuentaAsiento']['tipo_asiento'] = "#".$tipoAsiento['MutualTipoAsiento']['id']." - " . $tipoAsiento['MutualTipoAsiento']['concepto'];
			}
			
			$datos[$vinculo['MutualCuentaAsiento']['tipo_orden_dto']][$vinculo['MutualCuentaAsiento']['tipo_producto']]['values'][$idx] = $vinculo['MutualCuentaAsiento'];
		}
		return $datos;
	}
	
	function armaDatos($vinculo){
		if(empty($vinculo)) return $vinculo;
		App::import('Model','contabilidad.PlanCuenta');
		$oCTA = new PlanCuenta();	
		App::import('Model','mutual.MutualTipoAsiento');
		$oTIPOAS = new MutualTipoAsiento();				
		$vinculo['MutualCuentaAsiento']['concepto_producto'] = parent::GlobalDato('concepto_1', $vinculo['MutualCuentaAsiento']['tipo_producto']);
		$vinculo['MutualCuentaAsiento']['concepto_cuota'] = parent::GlobalDato('concepto_1', $vinculo['MutualCuentaAsiento']['tipo_cuota']);
		$cuenta = $oCTA->getCuenta($vinculo['MutualCuentaAsiento']['co_plan_cuenta_id']);
		$vinculo['MutualCuentaAsiento']['cuenta'] = null;
		$vinculo['MutualCuentaAsiento']['tipo_asiento'] = null;
		if($cuenta['PlanCuenta']['existe'] == 1) $vinculo['MutualCuentaAsiento']['cuenta'] = $cuenta['PlanCuenta']['codigo'] . " " . $cuenta['PlanCuenta']['descripcion'];;
		$tipoAsiento = $oTIPOAS->read(null,$vinculo['MutualCuentaAsiento']['mutual_tipo_asiento_id']);
		if(!empty($tipoAsiento)){
			$vinculo['MutualCuentaAsiento']['tipo_asiento'] = "#".$tipoAsiento['MutualTipoAsiento']['id']." - " . $tipoAsiento['MutualTipoAsiento']['concepto'];
		}				
		return $vinculo;
	}
	
	function actualizarDatosFromCuotas(){
		
	}
	
}

?>