<?php
class BancoRendicionCodigo extends ConfigAppModel{
	
	var $name = 'BancoRendicionCodigo';
	
	function getDescripcionCodigo($banco_id,$codigo){
		$codigos = $this->find('all',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.codigo' => $codigo,
										)
		));
		if(isset($codigos[0]['BancoRendicionCodigo']))return $codigos[0]['BancoRendicionCodigo']['descripcion'];
		else return null;
	}
	
	function isCodigoPago($banco_id,$codigo){
		$esPago = $this->find('count',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.codigo' => $codigo,
																'BancoRendicionCodigo.indica_pago' => 1,
										)
		));
		if($esPago > 0) return true;
		else return false;		
	}
	
	/**
	 * devuelve los codigos de rendicion del banco que indican un pago
	 * @param unknown_type $banco_id
	 * @return unknown
	 */
	function getCodigosPago($banco_id){
		$codigos = $this->find('all',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.indica_pago' => 1,
										)
		));
		return $codigos;
	}
	
	/**
	 * devuelve los codigos de rendicion de un banco que no indican pago
	 * @param $banco_id
	 */
	function getCodigosNoPago($banco_id){
		$codigos = $this->find('all',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.indica_pago' => 0,
										)
		));
		return $codigos;
	}

	function getCalificacionSocio($banco_id,$codigo){
		$codigos = $this->find('all',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.codigo' => $codigo,
										),
										'fields' => array('BancoRendicionCodigo.calificacion_socio')
		));
		if(isset($codigos[0]['BancoRendicionCodigo']['calificacion_socio'])) return $codigos[0]['BancoRendicionCodigo']['calificacion_socio'];
		else return null;		
	}

	
	function getCodigoByConcepto($banco_id,$concepto){
		$codigos = $this->find('all',array(
										'conditions' => array(
																'BancoRendicionCodigo.banco_id' => $banco_id,
																'BancoRendicionCodigo.descripcion' => $concepto,
										),
										'fields' => array('BancoRendicionCodigo.codigo')
		));
		if(isset($codigos[0]['BancoRendicionCodigo']['codigo'])) return $codigos[0]['BancoRendicionCodigo']['codigo'];
		else return null;		
		
	}
	
	function getCodigos($banco_id){
		$codigos = $this->find('all',array(
				'conditions' => array(
						'BancoRendicionCodigo.banco_id' => $banco_id,
				),'order' => ('BancoRendicionCodigo.descripcion'),
		));
		return $codigos;
	}	
	
}
?>