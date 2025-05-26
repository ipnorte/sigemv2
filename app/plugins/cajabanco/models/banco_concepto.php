<?php
class BancoConcepto extends CajabancoAppModel{
	
	var $name = 'BancoConcepto';
	
	var $validate = array(
							'co_plan_cuenta_id' => array(
													'rule' => 'validateCambioCuentaContable',
													'message' => ''
													),													
						);								
	
	function guardar($datos){
		$datos['BancoConcepto']['concepto'] = strtoupper($datos['BancoConcepto']['concepto']);
		return parent::save($datos);
	}
	
	function getCuenta($id){
		$concepto = $this->read(null,$id);
		return $this->armaDatos($concepto);
	}
	
	
	function armaDatos($concepto){
		$oPLANCUENTA = parent::importarModelo("PlanCuenta","contabilidad");
		$planCuenta = $oPLANCUENTA->getCuenta($concepto['BancoConcepto']['co_plan_cuenta_id']);
		
		$concepto['BancoConcepto']['cuenta_contable'] = '';
		
		if($planCuenta['PlanCuenta']['existe'] == 1) $concepto['BancoConcepto']['cuenta_contable'] = $planCuenta['PlanCuenta']['cuenta']." - ".$planCuenta['PlanCuenta']['descripcion'];
		return $concepto;
	}
		
	
	function afterFind($results, $primary = true){
		if(empty($results)) return $results;
		foreach($results as $i => $result):
			if(isset($result['BancoConcepto']) && !empty($result['BancoConcepto'])):
				$results[$i] = $this->armaDatos($result);
			endif;
		
		endforeach;
		return $results;
	}
	
	function beforeDelete($cascade = true){
		$oBANCOMOVIM = parent::importarModelo("BancoCuentaMovimiento","cajabanco");
		if(!$oBANCOMOVIM->tieneMovimientosByBancoConceptoId($this->id)) return true;
		else return false;
	}
	
	function validateCambioCuentaContable(){
		return true;
	}
	
	
	function getConceptoByTipoId($tipo){
		$aConcepto = $this->find('all', array('conditions' => array('BancoConcepto.tipo' => $tipo)));
		
		return (!empty($aConcepto) ? $aConcepto[0]['BancoConcepto']['id'] : NULL);
	}

	
	function getComboConcepto($caja = false){
		$aCombo = array();
		if($caja):
			$conditions = array('BancoConcepto.tipo' => 7);
		else:
			$conditions = array('BancoConcepto.tipo != ' => 7);
		endif;
		$aDatos = $this->find('all', array('conditions' => $conditions));
		if(empty($aDatos)) return $aCombo;
		
		$aCombo = array('' => 'Seleccionar . . .');
		foreach($aDatos as $dato){
			$aCombo[$dato['BancoConcepto']['id']] = $dato['BancoConcepto']['concepto'];
		}
		
		return $aCombo;
		
	}
}
?>