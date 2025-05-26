<?php
class ReciboForma extends ClientesAppModel{
	var $name = 'ReciboForma';
	
	function getReciboFormaByRecibo($id=null){
		$aReciboForma = array();

		if(empty($id)) return $aReciboForma;
		
		// Caja y Banco Movimientos. ('Banco Cuenta Movimientos').
		$oBancoMovimiento = $this->importarModelo('BancoCuentaMovimiento', 'cajabanco');
		
		$aReciboForma = $this->find('all',array('conditions' => array('ReciboForma.recibo_id' => $id)));
		
		$aReciboForma = Set::extract("{n}.ReciboForma",$aReciboForma);

		foreach($aReciboForma as $key => $forma){
//			$movimiento = $oBancoMovimiento->getMovimientoByMovimId($forma['banco_cuenta_movimiento_id']);
			$movimiento = $oBancoMovimiento->getMovimientoId($forma['banco_cuenta_movimiento_id'], false);

			$aReciboForma[$key]['banco_cuenta'] = $movimiento[0]['BancoCuentaMovimiento']['banco_cuenta'];
			$aReciboForma[$key]['plaza'] = $movimiento[0]['BancoCuentaMovimiento']['plaza'];
			$aReciboForma[$key]['fecha_cheque'] = $movimiento[0]['BancoCuentaMovimiento']['fecha_cheque'];
			
		}
		
		return $aReciboForma;
	}    
	
	
	function getImporte($recibo_id){
		$condiciones = array(
							'conditions' => array(
								'ReciboForma.recibo_id' => $recibo_id,
							),
							'fields' => array('SUM(ReciboForma.importe) as importe'),
		);
		$importe_cobro = $this->find('all',$condiciones);
		return (isset($importe_cobro[0][0]['importe']) ? $importe_cobro[0][0]['importe'] : 0);		
		
	}

	
	function getFormaCobro($id, $return = null){
		$condiciones = array(
							'conditions' => array(
								'ReciboForma.banco_cuenta_movimiento_id' => $id,
							)
		);
		$forma_cobro = $this->find('all',$condiciones);

		if(empty($return)) return $forma_cobro[0];
		else return $forma_cobro[0]['ReciboForma'][$return];		
		
		
	}
}

?>