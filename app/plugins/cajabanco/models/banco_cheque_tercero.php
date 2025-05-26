<?php
class BancoChequeTercero extends CajabancoAppModel{
	
	var $name = 'BancoChequeTercero';
	
	function getChqTerceroById($id){
		if(empty($id)) return array();
		
		$chqTercero = $this->read(null, $id);
		
		if(empty($chqTercero)) return array();
		
		$chqTercero['BancoChequeTercero']['banco'] = $this->getNombreBanco($chqTercero['BancoChequeTercero']['banco_id']);

		return $chqTercero['BancoChequeTercero'];
	}


	function getChequeCartera(){

		$chqCarteras = $this->find('all', array('conditions' => array('BancoChequeTercero.fecha_baja' => NULL), 'order' => 'BancoChequeTercero.fecha_vencimiento'));
		
		foreach($chqCarteras as $indice => $chqCartera):
			$chqCarteras[$indice]['BancoChequeTercero']['banco'] = $this->getNombreBanco($chqCartera['BancoChequeTercero']['banco_id']);
		endforeach;
		
		return $chqCarteras;
	}
	
	
	function getChqTerceroByMovimiento($id){
		if(empty($id)) return array();
		
		$chqTerceros = $this->find('all', array('conditions' => array('BancoChequeTercero.salida_banco_cuenta_movimiento_id' => $id)));
		
		if(empty($chqTerceros)) return array();
		
		foreach($chqTerceros as $index => $chqTercero):
			$chqTerceros[$index]['BancoChequeTercero']['banco'] = $this->getNombreBanco($chqTercero['BancoChequeTercero']['banco_id']);
		endforeach;
		
		return $chqTerceros;
	}

	
	function getSaldoCheque($fecha){
		$sql = "SELECT (
				SELECT IFNULL(SUM(importe),0.00) 
				FROM banco_cheque_terceros
				WHERE fecha_ingreso <= '$fecha') AS saldo_ingreso_cheque,
				
				(
				SELECT IFNULL(SUM(importe),0.00) 
				FROM banco_cheque_terceros
				WHERE fecha_baja <= '$fecha' AND fecha_baja IS NOT NULL) AS saldo_egreso_cheque
				FROM banco_cheque_terceros
				LIMIT 1	
		";
		
		$saldo = $this->query($sql);

		return $saldo[0][0]['saldo_ingreso_cheque'] - $saldo[0][0]['saldo_egreso_cheque'];
	}

}
?>