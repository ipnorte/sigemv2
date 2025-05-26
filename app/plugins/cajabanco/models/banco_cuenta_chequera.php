<?php 
class BancoCuentaChequera extends CajabancoAppModel{
	
	var $name = "BancoCuentaChequera";
	var $belongsTo = array("BancoCuenta");
	
	
	function guardar($datos){
		if(empty($datos['BancoCuentaChequera']['id'])):
			$datos['BancoCuentaChequera']['proximo_numero'] = $datos['BancoCuentaChequera']['desde_numero'];
		endif;
		return parent::save($datos);
	}
	
	function getChequerasByBancoCuenta($banco_cuenta_id){
		return $this->find('all',array('conditions' => array('BancoCuentaChequera.banco_cuenta_id' => $banco_cuenta_id),'order' => array('BancoCuentaChequera.created DESC')));
	}

	function armaDatos($chequera){
		$chequera['BancoCuenta']['banco'] = parent::getNombreBanco($chequera['BancoCuenta']['banco_id']);
		$oPLANCUENTA = parent::importarModelo("PlanCuenta","contabilidad");
		$planCuenta = $oPLANCUENTA->getCuenta($chequera['BancoCuenta']['co_plan_cuenta_id']);
		$chequera['BancoCuenta']['cuenta_contable'] = $planCuenta['PlanCuenta']['codigo']." - ".$planCuenta['PlanCuenta']['descripcion'];
		return $chequera;
	}	
	
	function afterFind($results){
		if(empty($results)) return $results;
		foreach($results as $i => $result):
			if(isset($result['BancoCuenta']) && !empty($result['BancoCuenta'])):
				$results[$i] = $this->armaDatos($result);
			endif;
		
		endforeach;
		return $results;
	}	
	
	
	function beforeDelete(){
		$oBANCOMOVIM = parent::importarModelo("BancoCuentaMovimiento","cajabanco");
		if(!$oBANCOMOVIM->tieneMovimientosByBancoCuentaChequeraId($this->id)) return true;
		else return false;
	}	
	
}

?>