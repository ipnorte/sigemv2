<?php
class AsientoRenglon extends ContabilidadAppModel {
	var $name = 'AsientoRenglon';
	var $useTable = 'co_asiento_renglones';

	function get_asiento_renglon($asiento_id){
		$this->bindModel(array('belongsTo' => array('PlanCuenta')));
		$asiento = $this->find('all', array('conditions' => array('AsientoRenglon.co_asiento_id' => $asiento_id)));
		return $asiento;
	}
	
	function getRenglonesAsiento($asiento_id){
		$renglonDebe = $this->find('all', array('conditions' => array('AsientoRenglon.co_asiento_id' => $asiento_id, 'AsientoRenglon.debe >' => 0)));

		$renglones = array();
		
		if(!empty($renglonDebe)):
			foreach($renglonDebe as $renglon){
				$renglon = $this->setDatoRenglon($renglon);
				array_push($renglones, $renglon);
			}
		endif;
		
		$renglonHaber = $this->find('all', array('conditions' => array('AsientoRenglon.co_asiento_id' => $asiento_id, 'AsientoRenglon.haber >' => 0)));

		if(!empty($renglonHaber)):
			foreach($renglonHaber as $renglon){
				$renglon = $this->setDatoRenglon($renglon);
				array_push($renglones, $renglon);
			}
		endif;
		
	return $renglones;
		
	}
	
	function getRenglon($id){
		$renglon = $this->read(null,$id);
		return $this->setDatoRenglon($renglon);
	}
	
	function setDatoRenglon($renglon){
		$cuenta = parent::getCuenta($renglon['AsientoRenglon']['co_plan_cuenta_id']);
		$renglon['AsientoRenglon']['codigo_cuenta'] = $cuenta['PlanCuenta']['codigo'];
		$renglon['AsientoRenglon']['descripcion_cuenta'] = $cuenta['PlanCuenta']['descripcion'];
		return $renglon;
	}
	
	/**
	 * Verifica si una cuenta tiene asientos
	 * @param $planCuentaId
	 * @return boolean
	 */
	function tieneAsientos($planCuentaId){
		$conditions = array();
		$conditions['AsientoRenglon.co_plan_cuenta_id'] = $planCuentaId;
		$renglones = $this->find('count', array('conditions' => $conditions));
		if(empty($renglones)) return false;
		else return true;
	}
	
}
?>