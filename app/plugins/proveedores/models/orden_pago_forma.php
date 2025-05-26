<?php
class OrdenPagoForma extends ProveedoresAppModel{
	
	var $name = 'OrdenPagoForma';
	
	function getOrdenPagoForma($id=null){
		$aOrdenPagoForma = array();
		
		if(empty($id)) return $aOrdenPagoForma;
		
    	$aOrdenPagoForma = $this->find('all',array('conditions' => array('OrdenPagoForma.orden_pago_id' => $id)));
    	
		$aOrdenPagoForma = Set::extract("{n}.OrdenPagoForma",$aOrdenPagoForma);
    	return $aOrdenPagoForma;
	}
	
	
	function getImporte($orden_pago_id){
		$condiciones = array(
							'conditions' => array(
								'OrdenPagoForma.orden_pago_id' => $orden_pago_id,
							),
							'fields' => array('SUM(OrdenPagoForma.importe) as importe'),
		);
		$importe_detalle = $this->find('all',$condiciones);
		return (isset($importe_detalle[0][0]['importe']) ? $importe_detalle[0][0]['importe'] : 0);		
		
	}

	
	function getFormaPago($id, $return = null){
		$condiciones = array(
							'conditions' => array(
								'OrdenPagoForma.banco_cuenta_movimiento_id' => $id,
							)
		);
		$forma_pago = $this->find('all',$condiciones);

		if(empty($return)) return $forma_pago[0];
		else return $forma_pago[0]['OrdenPagoForma'][$return];		
		
		
	}
	
	
	
	
}

?>