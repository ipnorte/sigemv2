<?php
/**
 * 
 * @author ADRIAN TORRES
 * @package mutual
 * @subpackage controller
 */
class CancelacionOrdenCuotasController extends MutualAppController{
	
	var $name = 'CancelacionOrdenCuotas';
	var $autorizar = array('cuotas_by_orden');
	
	function beforeFilter(){
		$this->Seguridad->allow($this->autorizar);
	    parent::beforeFilter();		
	}	
	
	function cuotas_by_orden($orden_cancelacion_id){
		$cuotas = $this->CancelacionOrdenCuota->getByOrden($orden_cancelacion_id);
		return $cuotas;
	}

	
}
?>