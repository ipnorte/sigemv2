<?php 
class BancoCuenta extends CajabancoAppModel{
	
	var $name = 'BancoCuenta';
	
	var $belongsTo = array('Banco');
	var $hasMany = array('BancoCuentaChequera');
	
	var $validate = array(
							'numero' => array(
													'rule' => 'validateBancoAndNroCuenta',
													'message' => '!'
													),
							'co_plan_cuenta_id' => array(
													'rule' => 'validateCambioCuentaContable',
													'message' => ''
													),													
						);								
	
	function guardar($datos){
                if($datos['BancoCuenta']['banco_id'] == 99999) {$datos['BancoCuenta']['chequeras'] = 0;};
                $datos['BancoCuenta']['fecha_conciliacion'] = (isset($datos['BancoCuenta']['fecha_apertura']) ? $datos['BancoCuenta']['fecha_apertura'] : date ('Y-m-d'));
		return parent::save($datos);
	}
	
	function getCuentaCajaId(){
		$aDatos = $this->find('all', array('conditions' => array('BancoCuenta.banco_id' => '99999', 'BancoCuenta.caja_general' => 1)));
		return (!empty($aDatos) ? $aDatos[0]['BancoCuenta']['id'] : NULL);
	}
	
	
	function getCuenta($id){
		$cuenta = $this->read(null,$id);
		return $this->armaDatos($cuenta);
	}
	
	function armaDatos($cuenta){
		$cuenta['BancoCuenta']['banco'] = parent::getNombreBanco($cuenta['BancoCuenta']['banco_id']);
		$oPLANCUENTA = parent::importarModelo("PlanCuenta","contabilidad");
		$planCuenta = $oPLANCUENTA->getCuenta($cuenta['BancoCuenta']['co_plan_cuenta_id']);

		$cuenta['BancoCuenta']['cuenta_contable'] = '';

		if($planCuenta['PlanCuenta']['existe'] == 1) $cuenta['BancoCuenta']['cuenta_contable'] = $planCuenta['PlanCuenta']['cuenta']." - ".$planCuenta['PlanCuenta']['descripcion'];
		return $cuenta;
	}
		
	
	function afterFind($results, $primary = true){
		if(empty($results)) return $results;
		foreach($results as $i => $result):
			if(isset($result['BancoCuenta']) && !empty($result['BancoCuenta'])):
				$results[$i] = $this->armaDatos($result);
			endif;
		
		endforeach;
		return $results;
	}
	
	function beforeDelete($cascade = true){
		$oBANCOMOVIM = parent::importarModelo("BancoCuentaMovimiento","cajabanco");
		if(!$oBANCOMOVIM->tieneMovimientosByBancoCuentaId($this->id)) return true;
		else return false;
	}
	


	/**
	 * Valida que el numero de cuenta no exista para el id del banco
	 */
	function validateBancoAndNroCuenta(){
		$conditions = array();
		$conditions['BancoCuenta.id'] = 0;
		$conditions['BancoCuenta.banco_id'] = $this->data['BancoCuenta']['banco_id'];
		$conditions['BancoCuenta.numero'] = $this->data['BancoCuenta']['numero'];
		$cantidad = $this->find('count',array('conditions' => $conditions));
		if(empty($cantidad))return true;
		parent::notificar("YA EXISTE EL NUMERO DE CUENTA " . $this->data['BancoCuenta']['numero'] . " PARA " . parent::getNombreBanco($this->data['BancoCuenta']['banco_id']));
		return false;
	}
	
	
	function validateCambioCuentaContable(){
		return true;
	}
	
	
	function combo($ecepto=0){
//		$aCombo = $this->find('list', array('fields' => array('BancoCuenta.denominacion')));
		$aDatos = $this->find('all');
		$aCombo = array();
		if(empty($aDatos)) return $aCombo;
		
		foreach($aDatos as $dato){
			if($dato['BancoCuenta']['banco_id'] != '99999' && $dato['BancoCuenta']['id'] != $ecepto):
				$sBanco = parent::getNombreBanco($dato['BancoCuenta']['banco_id']);
				$aCombo[$dato['BancoCuenta']['id']] = $sBanco . ' - ' . $dato['BancoCuenta']['numero'] . ' - ' . $dato['BancoCuenta']['denominacion'];
			endif;
		}
		
		return $aCombo;
		
	}
	
	
	function comboByBanco($bancoId=null){
		if(empty($bancoId)) return array();
		
		$aDatos = $this->find('all', array('conditions' => array('BancoCuenta.banco_id' => $bancoId)));
		$aCombo = array();
		if(empty($aDatos)) return $aCombo;
		
		foreach($aDatos as $dato){
			$sBanco = parent::getNombreBanco($dato['BancoCuenta']['banco_id']);
			$aCombo[$dato['BancoCuenta']['id']] = $sBanco . ' - ' . $dato['BancoCuenta']['numero'] . ' - ' . $dato['BancoCuenta']['denominacion'];
		}
		
		return $aCombo;
		
	}
	
	
	function comboCuentas(){
//		$aCombo = $this->find('list', array('fields' => array('BancoCuenta.denominacion')));
		$aDatos = $this->find('all');
		$aCombo = array();
		if(empty($aDatos)) return $aCombo;
		
		foreach($aDatos as $dato){
			$sBanco = parent::getNombreBanco($dato['BancoCuenta']['banco_id']);
			if($dato['BancoCuenta']['banco_id'] != '99999'):
				$aCombo['B'.$dato['BancoCuenta']['id']] = $sBanco . ' - ' . $dato['BancoCuenta']['numero'] . ' - ' . $dato['BancoCuenta']['denominacion'];
			else:
				$aCombo['C'.$dato['BancoCuenta']['id']] = $sBanco . ' - ' . $dato['BancoCuenta']['numero'] . ' - ' . $dato['BancoCuenta']['denominacion'];
			endif;
		}
		
		return $aCombo;
		
	}
	
	
	function getCodigoPlanCuenta($id){
		$bancoCuenta = $this->read(null, $id);
		
		return $bancoCuenta['BancoCuenta']['co_plan_cuenta_id'];
	}
	
	
}

?>