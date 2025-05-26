<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage model
 */

class CancelacionOrdenCuota extends MutualAppModel{
	
	var $name = 'CancelacionOrdenCuota';
	
	
	function getByOrden($orden_cancelacion_id){
		$cuotas = $this->find('all',array('conditions' => array('CancelacionOrdenCuota.cancelacion_orden_id' => $orden_cancelacion_id)));
		foreach($cuotas as $idx => $cuota){
			$cuotas[$idx] = $this->armaDatosCuota($cuota);
		}
		return $cuotas;
	}

	
	function armaDatosCuota($cuota){
		//armar los datos de la cuota de la orden de descuento que esta cancelando
		App::import('model','Mutual.OrdenDescuentoCuota');
		$oCuota = new OrdenDescuentoCuota();
		$oCuota->unbindModel(array('belongsTo' => array('Socio'),'hasMany' => array('OrdenDescuentoCobroCuota')));
		$cuotaOrigen = $oCuota->getCuota($cuota['CancelacionOrdenCuota']['orden_descuento_cuota_id']);
		$cuota['CancelacionOrdenCuota']['OrdenDescuentoCuota'] = Set::extract('OrdenDescuentoCuota',$cuotaOrigen);
		return $cuota;
	}
	
}
?>